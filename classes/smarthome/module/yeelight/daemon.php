<?php

namespace SmartHome\Module\Yeelight;

use SmartHome\DeviceStorage,
    Yeelight\GenericDevice,
    Yeelight\SocketServer;

class Daemon implements \SmartHome\DaemonInterface {

    const DAEMON_NAME='yeelight';

    /**
     *  @var \SmartHome\DeviceStorage
     */
    private $storage;
    private $socketserver;
    private $process_url;

    public function __construct($params) {
        $this->process_url=$params['events_url'];
    }

    public function getName() {
        return self::DAEMON_NAME;
    }

    public function prepare() {
        $this->storage=new DeviceStorage;
        $this->socketserver=new SocketServer();
        $this->socketserver->run();
        $this->socketserver->sendDiscover();
    }

    public function iteration() {
        $pkt=$this->socketserver->getPacket();
        $p=$pkt->getParams();
        if (isset($p['id'])) {
            $hwid=self::DAEMON_NAME.'_'.$p['id'];
            #TODO добавить блокировки
            $device=$this->storage->get($hwid);
            if (is_null($device)) {
                $device=new GenericDevice();
            }
            $device->updateState($p);
            $this->storage->set($hwid, $device);
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
