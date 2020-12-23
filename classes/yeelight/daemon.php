<?php

namespace Yeelight;

use DB,
    SmartHome\MemoryStorage;

class Daemon implements \SmartHome\DaemonInterface {

    const DAEMON_NAME='yeelight';

    /**
     *  @var \SmartHome\MemoryStorage
     */
    private $storage;
    private $socketserver;
    private $process_url;

    public function __construct($process_url) {
        $this->process_url=$process_url;
    }

    public function getName() {
        return self::DAEMON_NAME;
    }

    public function prepare() {
        $this->storage=new MemoryStorage;
        $this->socketserver=new SocketServer();
        $this->socketserver->run();
        $this->socketserver->sendDiscover();
        DB::disconnect();
    }

    public function iteration() {
        $pkt=$this->socketserver->getPacket();
        $p=$pkt->getParams();
        if (isset($p['id'])) {
            $hwid=self::DAEMON_NAME.'_'.$p['id'];
            $this->storage->lockMemory();
            $device=$this->storage->getDevice($hwid);
            if (is_null($device)) {
                $device=new GenericDevice();
            }
            $device->updateState($p);
            $this->storage->setDevice($hwid, $device);
            $this->storage->releaseMemory();
            $events=$device->getActions();
            if (!is_null($events)) {
                file_get_contents($this->process_url, 0, stream_context_create([
                    'http'=>[
                        'method'=>'POST',
                        'header'=>"Content-Type: application/json; charset=utf-8\r\n",
                        'content'=>json_encode(['hwid'=>$hwid, 'events'=>$events])
                    ]
                ]));
            }
        }
    }

    public function finish() {
        return;
    }

}
