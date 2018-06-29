<?php

/**
 * Беспроводная кнопка
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

    public function getDeviceStatus() {
        $result=[];
        if($this->updated) {
            $result[]="Была онлайн ".date('d.m.Y H:i:s',$this->updated);
        }
        if ($this->voltage) {
            $result[]=sprintf('Батарея CR2032: %.3f В.',$this->voltage);
        }
        return join(' ',$result);
    }

    public function getDeviceIndicators(): array {
        return [];
    }

    public function getDeviceMeters(): array {
        return [];
    }

}
