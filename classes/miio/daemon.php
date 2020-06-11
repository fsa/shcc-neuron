<?php

namespace miIO;

use DB;

class Daemon implements \SmartHome\DaemonInterface {

    const DAEMON_NAME='miio';

    /**
     *  @var \SmartHome\Device\MemoryStorage
     */
    private $storage;
    private $socketserver;
    private $process_url;
    private $tokens=[];

    public function __construct($process_url) {
        $this->process_url=$process_url;
    }

    public function getName() {
        return self::DAEMON_NAME;
    }

    public function prepare() {
        $this->storage=new \SmartHome\Device\MemoryStorage;
        $this->tokens=Tokens::getTokens();
        $this->socketserver=new SocketServer();
        $this->socketserver->setBroadcastSocket();
        SocketServer::sendDiscovery();
        DB::disconnect();
    }

    public function iteration() {
        $pkt=$this->socketserver->getPacket();
        if (!$pkt->isMiIOPacket()) {
            return;
        }
        $uid=$pkt->getDeviceId();
        if ($uid=='ffffffffffffffff') {
            return;
        }
        $this->storage->lockMemory();
        $device=$this->storage->getDevice(self::DAEMON_NAME.'_'.$uid);
        if(is_null($device)) {
            $device=new GenericDevice;
            if (isset($this->tokens[$uid])) {
                $device->setDeviceToken($this->tokens[$uid]);
                $device->update($pkt);
            }
            $this->storage->setDevice(self::DAEMON_NAME.'_'.$uid, $device);
            $this->storage->releaseMemory();
        } else {
            $device->update($pkt);
            $this->storage->setDevice(self::DAEMON_NAME.'_'.$uid, $device);
            $this->storage->releaseMemory();
            $actions=$device->getActions();
            if (!is_null($actions)) {
                $data=['module'=>self::DAEMON_NAME, 'uid'=>$uid, 'data'=>$actions];
                file_get_contents($this->process_url.'?'.http_build_query($data));
            }
        }
    }

    public function finish() {
        return;
    }

}
