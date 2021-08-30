<?php

namespace SmartHome\Module\Xiaomi;

use SmartHome\DeviceStorage,
    Xiaomi\SocketServer;

class Daemon implements \SmartHome\DaemonInterface {

    const DAEMON_NAME='xiaomi';

    /**
     *  @var \SmartHome\DeviceStorage
     */
    private $storage;
    private $socketserver;
    private $events_url;
    private $ip;
    private $port;

    public function __construct($params) {
        $this->events_url=$params['events_url'];
        $this->ip=$params['ip'];
        $this->port=$params['port'];
    }

    public function getName() {
        return self::DAEMON_NAME;
    }

    public function prepare() {
        $this->storage=new DeviceStorage;
        $this->socketserver=new SocketServer($this->ip, $this->port);
        $this->socketserver->run();
    }

    public function iteration() {
        $pkt=$this->socketserver->getPacket();
        $hwid=$pkt->getSid();
        if (is_null($hwid)) {
            return;
        }
        $hwid=self::DAEMON_NAME.'_'.$hwid;
        #TODO добавить блокировки
        $device=$this->storage->get($hwid);
        if (is_null($device)) {
            $device=$pkt->getDeviceObject();
            $device->update($pkt);
            $this->storage->set($hwid, $device);
        } else {
            $device->update($pkt);
            $this->storage->set($hwid, $device);
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
    }


}
