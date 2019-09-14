<?php

namespace Xiaomi;

use DB;

class Daemon implements \SmartHome\DaemonInterface {

    const DAEMON_NAME='xiaomi';

    /**
    *  @var \SmartHome\Device\MemoryStorage
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
        $this->storage=new \SmartHome\Device\MemoryStorage;
        $this->socketserver=new SocketServer();
        $this->socketserver->run();
        DB::disconnect();
    }

    public function iteration() {
        $pkt=$this->socketserver->getPacket();
        $uid=$pkt->getSid();
        if (is_null($uid)) {
            return;
        }
        $uid=self::DAEMON_NAME.'_'.$uid;
        $device=$this->storage->getDevice($uid);
        if(is_null($device)) {
            $device=$pkt->getDeviceObject();
            $device->update($pkt);
            $this->storage->setDevice($uid, $device);
        } else {
            $device->update($pkt);
            $this->storage->setDevice($uid, $device);
            $actions=$device->getActions();
            if (!is_null($actions)) {
                $data=['module'=>self::DAEMON_NAME,'uid'=>$uid,'data'=>$actions];
                file_get_contents($this->process_url.'?'.http_build_query($data));
            }
        }
    }

    public function finish() {
        return;
    }

}
