<?php

namespace Xiaomi;

class Daemon {

    private $storage;
    private $socketserver;
    private $devices;

    public function prepare() {
        $this->storage=new \MemoryStorage();
        $this->devices=$this->storage->getArray('xiaomi');
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
            $this->storage->setVar('xiaomi',$this->devices);
            $actions=$this->devices[$sid]->getActions();
            if (!is_null($actions)) {
                $data=['module'=>'xiaomi','uid'=>$sid,'data'=>$actions];
                file_get_contents(\Settings::get('url').'/action/?'.http_build_query($data));
                #echo date('c').' '.$sid.'=>'.$actions.PHP_EOL;
            }
        }
    }

    public function finish() {
        return;
    }

}
