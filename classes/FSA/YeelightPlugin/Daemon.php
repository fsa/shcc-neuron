<?php

namespace FSA\YeelightPlugin;

use FSA\Yeelight\{Socket, GenericDevice};
use SmartHome\DeviceStorage;

class Daemon implements \SmartHome\DaemonInterface
{

    const DAEMON_NAME = 'Yeelight';

    /**
     *  @var \SmartHome\DeviceStorage
     */
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
        $this->storage = new DeviceStorage;
        $this->socket = new Socket();
        $this->socket->run();
        $this->socket->sendDiscover();
    }

    public function iteration()
    {
        $pkt = $this->socket->getPacket();
        $p = $pkt->getParams();
        if (isset($p['id'])) {
            $hwid = self::DAEMON_NAME . ':' . $p['id'];
            $device = $this->storage->get($hwid);
            if (is_null($device)) {
                $device = new GenericDevice();
            }
            $device->updateState($p);
            $this->storage->set($hwid, $device);
            $events = $device->getActions();
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
