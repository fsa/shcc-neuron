<?php

namespace Xiaomi;

class XiaomiPacket {

    private $pkt;
    private $peer;

    public function __construct(string $pkt,string $peer) {
        $this->pkt=json_decode($pkt);
        if (is_null($this->pkt) or !isset($this->pkt->cmd)) {
            var_dump($pkt);
        }
        if (!isset($this->pkt->sid)) {
            $this->pkt->sid=null;
        }
        if (isset($this->pkt->data)) {
            $this->pkt->data=json_decode($this->pkt->data,true);
        } else {
            $this->pkt->data=null;
        }
        $this->peer=new \stdClass;
        $this->peer->host=parse_url($peer,PHP_URL_HOST);
        $this->peer->port=parse_url($peer,PHP_URL_PORT);
    }

    public function get(): \stdClass {
        return $this->pkt;
    }

    public function getCmd(): string {
        return $this->pkt->cmd;
    }

    public function getModel(): string {
        return $this->pkt->model;
    }

    public function getSid() {
        return $this->pkt->sid;
    }

    public function getShortId(): string {
        return $this->pkt->short_id;
    }

    public function getToken(): string {
        return isset($this->pkt->token)?$this->pkt->token:'';
    }

    public function getData(): array {
        return $this->pkt->data;
    }

    public function getPeer(): \stdClass {
        return $this->peer;
    }

    public function getDeviceObject() {
        switch ($this->pkt->model) {
            case "gateway":
                return new Devices\XiaomiGateway;
            case "weather":
            case "weather.v1":
                return new Devices\AqaraWeatherSensor;
            case "sensor_ht":
                return new Devices\XiaomiHTSensor;
            case "motion":
                return new Devices\MotionSensor;
            case "magnet":
            case "sensor_magnet.aq2":
                return new Devices\MagnetSensor;
            case "switch":
                return new Devices\XiaomiSwitch;
            case "sensor_switch.aq2":
            case "sensor_switch.aq3":
                return new Devices\AqaraSwitch;
#            case "sensor_wleak.aq1":
#                return new Devices\AqaraWleakSensor;
#            case "86sw1":
#                return new Devices\AqaraWleakSensor;
#            case "86sw2":
#                return new Devices\AqaraWleakSensor;
#            case "sensor_wleak.aq1":
#                return new Devices\AqaraWleakSensor;
#            case "ctrl_86plug.aq1":
#stdClass Object ( [status] => stdClass Object ( [on] => action ) [inuse] => stdClass Object ( [1] => action ) [power_consumed] => stdClass Object ( [4124] => action ) [load_power] => stdClass Object ( [0.78] => action ) )
#                 return new Devices\AqaraWleakSensor;
            default:
                return new Devices\Unknown();
        }
    }

}
