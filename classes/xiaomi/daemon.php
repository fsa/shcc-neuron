<?php

namespace Xiaomi;

use DB,
    SmartHome\MemoryStorage;

class Daemon implements \SmartHome\DaemonInterface {

    const DAEMON_NAME='xiaomi';

    /**
     *  @var \SmartHome\MemoryStorage
     */
    private $storage;
    private $socketserver;
    private $events_url;

    public function __construct($events_url) {
        $this->events_url=$events_url;
    }

    public function getName() {
        return self::DAEMON_NAME;
    }

    public function prepare() {
        $this->storage=new MemoryStorage;
        $this->socketserver=new SocketServer();
        $this->socketserver->run();
        DB::disconnect();
    }

    public function iteration() {
        $pkt=$this->socketserver->getPacket();
        $hwid=$pkt->getSid();
        if (is_null($hwid)) {
            return;
        }
        $hwid=self::DAEMON_NAME.'_'.$hwid;
        $this->storage->lockMemory();
        $device=$this->storage->getDevice($hwid);
        if (is_null($device)) {
            $device=$pkt->getDeviceObject();
            $device->update($pkt);
            $this->storage->setDevice($hwid, $device);
            $this->storage->releaseMemory();
        } else {
            $device->update($pkt);
            $this->storage->setDevice($hwid, $device);
            $this->storage->releaseMemory();
            $events=$device->getEvents();
            if ($events) {
                file_get_contents($this->events_url, 0, stream_context_create([
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
