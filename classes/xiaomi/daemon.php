<?php

namespace Xiaomi;

class Daemon implements \SmartHome\Daemon {

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
        $this->storage=new \MemoryStorage();
        $this->devices=$this->storage->getArray(self::DAEMON_NAME);
        $this->socketserver=new SocketServer();
        $this->socketserver->run();
    }

    public function iteration() {
        $pkt=$this->socketserver->getPacket();
        $sid=$pkt->getSid();
        if (is_null($sid)) {
            return;
        }
        if (!isset($this->devices[$sid])) {
            $device=$pkt->getDeviceObject();
            if (is_null($device)) {
                echo 'New device: '.date('c').PHP_EOL.print_r($pkt,true);
            } else {
                $this->devices[$sid]=$device;
            }
        }
        if (isset($this->devices[$sid])) {
            $this->devices[$sid]->update($pkt);
            $this->storage->setVar(self::DAEMON_NAME,$this->devices);
            $actions=$this->devices[$sid]->getActions();
            if (!is_null($actions)) {
                $data=['module'=>self::DAEMON_NAME,'uid'=>$sid,'data'=>$actions];
                file_get_contents($this->process_url.'?'.http_build_query($data));
                #echo date('c').' '.$sid.'=>'.$actions.PHP_EOL;
            }
        }
    }

    public function finish() {
        return;
    }

}
