<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-27
 */

namespace Xiaomi\Devices;

class AqaraWirelessRemoteSwitch extends AbstractDevice implements \SmartHome\DeviceActionInterface {

    protected function updateParam($param,$value) {
        switch ($param) {
            case "channel_0":
                $this->actions[$this->oneButtonEvent($value).'@left']=1;
                break;
            case "channel_1":
                $this->actions[$this->oneButtonEvent($value).'@right']=1;
                break;
            case "dual_channel":
                $this->actions[$this->doubleButtonsEvent($value).'@both']=1;
                break;
            default:
                $this->showUnknownParam($param, $value);
        }
    }

    private function oneButtonEvent($value) {
        if($value=='long_click') {
            return 'long_press';
        }
        return $value;
    }

    private function doubleButtonsEvent($value) {
        switch ($value) {
            case 'both_click':
                return 'click';
            case 'double_both_click':
                return 'double_click';
            case 'long_both_click':
                return 'long_press';
        }
        return $value;
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

    public function getDeviceEvents(): array {
        return [
            'click@left',
            'double_click@left',
            'long_press@left',
            'click@right',
            'double_click@right',
            'long_press@right',
            'click@both',
            'double_click@both',
            'long_press@both',
            'voltage'
        ];
    }

}