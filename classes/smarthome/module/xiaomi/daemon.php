<?php

namespace SmartHome\Module\Xiaomi;

use SmartHome\MemoryStorage,
    Xiaomi\SocketServer;

class Daemon implements \SmartHome\DaemonInterface {

    const DAEMON_NAME='xiaomi';

    /**
     *  @var \SmartHome\MemoryStorage
     */
    private $storage;
    private $socketserver;
    private $events_url;

    public function __construct($params) {
        $this->events_url=$params['events_url'];
    }

    public function getName() {
        return self::DAEMON_NAME;
    }

    public function prepare() {
        $this->storage=new MemoryStorage;
        $this->socketserver=new SocketServer();
        $this->socketserver->run();
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
