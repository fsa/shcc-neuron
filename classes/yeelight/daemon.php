<?php

namespace Yeelight;

class Daemon {

    private $storage;
    private $socketserver;
    private $devices;

    public function prepare() {
        $this->storage=new \MemoryStorage();
        $this->devices=$this->storage->getArray('yeelight');
        $this->socketserver=new SocketServer();
        $this->socketserver->run();
        $this->socketserver->sendDiscover();
    }

    public function iteration() {
        $pkt=$this->socketserver->getPacket();
        $p=$pkt->getParams();
        if (isset($p['id'])) {
            $id=$p['id'];
            if (!isset($this->devices[$id])) {
                $this->devices[$id]=new GenericDevice();
            }
            $this->devices[$id]->updateState($p);
            $this->storage->setVar('yeelight',$this->devices);
            $actions=$this->devices[$id]->getActions();
            if (!is_null($actions)) {
                $data=['module'=>'yeelight','uid'=>$id,'data'=>$actions];
                file_get_contents(\Settings::get('url').'/action/?'.http_build_query($data));
                #echo date('c').' '.$id.'=>'.$actions.PHP_EOL;
            }
        }
    }

    public function finish() {
        return;
    }

}
