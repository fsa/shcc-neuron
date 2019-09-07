<?php

namespace Xiaomi;

use DB;

class Daemon implements \SmartHome\DaemonInterface {

    const DAEMON_NAME='xiaomi';

    private $storage;
    private $socketserver;
    private $devices;
    private $process_url;
    
    public function __construct($process_url) {
        $this->process_url=$process_url;
    }

    public function getName() {
        return self::DAEMON_NAME;
    }

    public function prepare() {
        $this->storage=new \SmartHome\DeviceMemoryStorage;
        $this->devices=$this->storage->getModuleDevices(self::DAEMON_NAME);
        DB::disconnect();
        $this->socketserver=new SocketServer();
        $this->socketserver->run();
    }

    public function iteration() {
        $pkt=$this->socketserver->getPacket();
        $sid=$pkt->getSid();
        if (is_null($sid)) {
            return;
        }
        if (isset($this->devices[$sid])) {
            $this->devices[$sid]->update($pkt);
            $this->storage->setModuleDevices(self::DAEMON_NAME,$this->devices);
            $actions=$this->devices[$sid]->getActions();
            if (!is_null($actions)) {
                $data=['module'=>self::DAEMON_NAME,'uid'=>$sid,'data'=>$actions];
                file_get_contents($this->process_url.'?'.http_build_query($data));
            }
        } else {
            $device=$pkt->getDeviceObject();
            $this->devices[$sid]=$device;
            $this->storage->setModuleDevices(self::DAEMON_NAME,$this->devices);            
        }
    }

    public function finish() {
        return;
    }

}
