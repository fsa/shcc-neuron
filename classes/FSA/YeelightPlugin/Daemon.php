<?php

namespace FSA\YeelightPlugin;

use FSA\SmartHome\DaemonInterface;
use SmartHome;

class Daemon implements DaemonInterface
{
    const DAEMON_NAME = 'Yeelight';

    private $storage;
    private $socket;
    private $events_callback;

    public function __construct($events, $params)
    {
        $events_callback = $events;
    }

    public function getName()
    {
        return self::DAEMON_NAME;
    }

    public function prepare()
    {
        $this->storage = SmartHome::deviceStorage();
        $this->socket = new Socket();
        $this->socket->run();
        $this->socket->sendDiscover();
    }

    public function iteration()
    {
        $pkt = $this->socket->getPacket();
        $p = $pkt->getParams();
        if (isset($p['id'])) {
            $hwid = $p['id'];
            /** @var FSA\SmartHome\DeviceInterface $device */
            $device = $this->storage->get(self::DAEMON_NAME . ':' . $hwid);
            if (is_null($device)) {
                $device = new Devices\LED();
            }
            $device->updateState($p);
            $this->storage->set(self::DAEMON_NAME . ':' . $hwid, $device);
            $events = $device->getEvents();
            if (!is_null($events)) {
                $callback = $this->events_callback;
                $callback($hwid, $events);
            }
        }
    }

    public function finish()
    {
        return;
    }
}
