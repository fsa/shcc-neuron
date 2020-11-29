<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-29
 * Беспроводная кнопка Aqara
 * Код не тестировался
 */

namespace Xiaomi\Devices;

class AqaraSwitch extends AbstractDevice {

    protected function updateParam($param,$value) {
        switch ($param) {
            case "status":
                $this->events[$this->buttonClicks($value)]=true;
                $this->events['status']=$value;
                break;
            default:
                $this->showUnknownParam($param, $value);
        }
    }

    private function buttonClicks($value) {
        switch ($value) {
            case 'click':
            case 'double_click':
                return $value;;
            case 'long_click_press':
                return 'long_press';
            case 'long_click_release':
                return 'long_press_release';
        }
        return $value;
    }

    public function getDescription(): string {
        return "Aqara Smart Wireless Switch";
    }

    public function getState(): array {
        return ['voltage'=>$this->voltage];
    }

    public function getEventsList(): array {
        return ['click', 'double_click', 'long_press', 'long_press_release', 'voltage'];
    }

    public function __toString(): string {
        $result=[];
        if($this->updated) {
            $result[]="Была онлайн ".date('d.m.Y H:i:s',$this->updated);
        }
        if ($this->voltage) {
            $result[]=sprintf('Батарея CR2032: %.3f В.',$this->voltage);
        }
        return join(' ',$result);
    }

}
