<?php

/**
 * Датчик двери/окна Xiaomi
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
        if ($last!=$value) {
            $this->actions['status']=$value;
            $this->actions['alarm']=$value!='close';
        }
    }

    public function getStatus() {
        return $this->status;
    }

    public function getDeviceName() {
        return "Xiaomi Smart Door and Windows Sensor";
    }

    public function getDeviceStatus() {
        $result=[];
        switch ($this->status) {
            case null:
                break;
            case "open":
                $result[]="Открыто.";
                break;
            case "close":
                $result[]="Закрыто.";
                break;
            default:
                $result[]="Статус ".$this->status.'.';
        }
        if ($this->voltage) {
            $result[]=sprintf('Батарея CR2032: %.3f В.',$this->voltage);
        }
        return join(' ',$result);
    }

    public function getDeviceIndicators(): array {
        return ['alarm'=>'Открытие'];
    }

    public function getDeviceMeters(): array {
        return [];
    }

}
