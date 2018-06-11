<?php

/**
 * Датчики температуры, влажности и давления Xiaomi и Aqara
 */

namespace Xiaomi\Devices;

class XiaomiSwitch extends AbstractDevice {

    protected function updateParam($param,$value) {
        switch ($param) {
            case "status":
                $this->actions['status']=$value;
                break;
            default:
                echo "$param => $value\n";
        }
    }

    public function getDeviceName() {
        return "Xiaomi Smart Wireless Switch";
    }

    public function __toString() {
        return "Была онлайн ".date('d.m.Y H:i:s',$this->updated);
    }
}