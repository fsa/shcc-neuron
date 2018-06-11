<?php

/**
 * Датчик движения Xiaomi
 */

namespace Xiaomi\Devices;

class MagnetSensor extends AbstractDevice {

    private $status;

    protected function updateParam($param,$value) {
        switch ($param) {
            case "status":
                $this->setStatus($value);
                break;
            default:
                echo "$param => $value\n";
        }
    }

    private function setStatus(string $value) {
        $last=$this->status;
        $this->status=$value;
        if($last!=$value) {
            $this->actions['status']=$value;
        }
    }
    
    public function getStatus() {
        return $this->status;
    }

    public function getDeviceName() {
        return "Xiaomi Smart Door and Windows Sensor";
    }
    
    public function __toString() {
        switch ($this->status) {
            case "open":
                return "Открыто";
            case "close":
                return "Закрыто";
        }
        return "Статус ".$this->status;
    }
}
