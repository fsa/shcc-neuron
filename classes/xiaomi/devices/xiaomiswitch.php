<?php

/**
 * Беспроводная кнопка
 */

namespace Xiaomi\Devices;

class XiaomiSwitch extends AbstractDevice implements \SmartHome\DeviceActionInterface {

    protected function updateParam($param,$value) {
        switch ($param) {
            case "status":
                $this->actions['status']=$value;
                break;
            default:
                echo "$param => $value\n";
        }
    }

    public function getDeviceDescription(): string {
        return "Xiaomi Smart Wireless Switch";
    }

    public function getDeviceStatus(): string {
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
        #TODO проверить имён корректность событий
        return ['click'=>'Нажатие','double_click'=>'Двойной клик','long_click_press'=>'Долгое нажатие','long_click_release'=>'Завершение долгого нажатия'];
    }

}
