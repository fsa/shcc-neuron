<?php

namespace FSA\XiaomiPlugin;

use SmartHome;
use SmartHome\DaemonInterface;

class Daemon implements DaemonInterface
{

    const DAEMON_NAME = 'Xiaomi';

    /**
     *  @var \SmartHome\DeviceStorage
     */
    private $storage;
    private $socket;
    private $ip;
    private $port;
    private $events_callback;

    public function __construct($events, $params)
    {
        $this->events_callback = $events;
        $this->ip = $params['ip'];
        $this->port = $params['port'];
    }

    public function getName()
    {
        return self::DAEMON_NAME;
    }

    public function prepare()
    {
        $this->storage = SmartHome::deviceStorage();
        $this->socket = new Socket($this->ip, $this->port);
        $this->socket->run();
    }

    public function iteration()
    {
        $pkt = $this->socket->getPacket();
        $sid = $pkt->getSid();
        if (is_null($sid)) {
            return;
        }
        /** @var FSA\SmartHome\DeviceInterface $device */
        $device = $this->storage->get(self::DAEMON_NAME, $sid);
        if (is_null($device)) {
            $device = $pkt->getDeviceObject();
            $device->update($pkt);
            $this->storage->set(self::DAEMON_NAME, $sid, $device);
        } else {
            $device->update($pkt);
            $this->storage->set(self::DAEMON_NAME, $sid, $device);
            $events = $device->getEvents();
            if ($events) {
                $callback = $this->events_callback;
                $callback($sid, $events);
            }
        }
    }

    public function finish()
    {
    }
}
