<?php

namespace Xiaomi\Devices;

class AqaraWirelessRemoteSwitch extends AbstractDevice implements \SmartHome\DeviceActionInterface {

    protected function updateParam($param,$value) {
        switch ($param) {
            case "channel_0":
                $this->actions['channel_0']=$value;
                break;
            case "channel_1":
                $this->actions['channel_1']=$value;
                break;
            case "dual_channel":
                $this->actions['dual_channel']=$value;
                break;
            default:
                $this->showUnknownParam($param, $value);
        }
    }

    public function getDescription(): string {
        return "Aqara Wireless Remote Switch";
    }

    public function getState(): array {
        return [
            'voltage'=>$this->voltage
                ];
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
        return ['click'=>'Нажатие','double_click'=>'Двойной клик','long_click'=>'Долгое нажатие'];
    }

}