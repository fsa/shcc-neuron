<?php

namespace FSA\miIOPlugin;

use SmartHome\DeviceStorage,
    miIO\SocketServer,
    miIO\GenericDevice;

class Daemon implements \SmartHome\DaemonInterface {

    const DAEMON_NAME='miIO';

    /**
     *  @var \SmartHome\DeviceStorage
     */
    private $storage;
    private $socket;
    private $events_callback;
    private $tokens=[];

    public function __construct($events, $params) {
        $this->events_callback = $events;
        $this->tokens=$params['tokens'];
    }

    public function getName() {
        return self::DAEMON_NAME;
    }

    public function prepare() {
        $this->storage=new DeviceStorage;
        $this->socket=new SocketServer();
        $this->socket->setBroadcastSocket();
        SocketServer::sendDiscovery();
    }

    public function iteration() {
        $pkt=$this->socket->getPacket();
        if (!$pkt->isMiIOPacket()) {
            return;
        }
        $uid=$pkt->getDeviceId();
        if ($uid=='ffffffffffffffff') {
            return;
        }
        $hwid= self::DAEMON_NAME . ':' . $uid;
        $device=$this->storage->get($hwid);
        if (is_null($device)) {
            $device=new GenericDevice;
            if (isset($this->tokens[$uid])) {
                $device->setDeviceToken($this->tokens[$uid]);
                $device->update($pkt);
            }
            $this->storage->set($hwid, $device);
        } else {
            $device->update($pkt);
            $this->storage->set($hwid, $device);
            $events = $device->getEvents();
            if (!is_null($events)) {
                $callback = $this->events_callback;
                $callback($hwid, $events);
            }
        }
    }

    public function finish() {
        return;
    }

}
