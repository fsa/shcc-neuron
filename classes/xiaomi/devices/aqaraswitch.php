<?php

/**
 * Беспроводная кнопка Aqara
 * Код не тестировался
 */

namespace Xiaomi\Devices;

class AqaraSwitch extends AbstractDevice implements \SmartHome\DeviceActionInterface {

    protected function updateParam($param,$value) {
        switch ($param) {
            case "status":
                $this->actions['status']=$value;
                break;
            default:
                $this->showUnknownParam($param, $value);
        }
    }

    public function getDescription(): string {
        return "Aqara Smart Wireless Switch";
    }

    public function getState(): array {
        return ['voltage'=>$this->voltage];
    }

    public function getStateString(): string {
        $result=[];
        if($this->updated) {
            $result[]="Была онлайн ".date('d.m.Y H:i:s',$this->updated);
        }
        if ($this->voltage) {
            $result[]=sprintf('Батарея CR2032: %.3f В.',$this->voltage);
        }
        return join(' ',$result);
    }

    public function getDeviceActions(): array {
        return ['click'=>'Нажатие','double_click'=>'Двойной клик'];
    }

}
