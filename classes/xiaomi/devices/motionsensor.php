<?php

/**
 * Датчик движения Xiaomi
 */

namespace Xiaomi\Devices;

class MotionSensor extends AbstractDevice {

    private $lastMotion;

    protected function updateParam($param,$value) {
        switch ($param) {
            case "status":
                if ($value=='motion') {
                    $this->setLastMotion(0);
                } else {
                    #TODO другие значения
                }
                break;
            case "no_motion":
                $this->setLastMotion(intval($value));
                break;
            default:
                echo "$param => $value\n";
        }
    }

    private function setLastMotion(int $value) {
        $this->lastMotion=$value==0?0:time()-$value;
        $this->actions['motion']=$value;
        $this->actions['alarm']=$value==0;
    }

    public function getLastMotion() {
        return $this->lastMotion;
    }

    public function getDeviceDescription() {
        return "Xiaomi Smart IR Human Body Sensor";
    }

    public function getDeviceStatus() {
        $result=[];
        if (!is_null($this->lastMotion)) {
            if ($this->lastMotion==0) {
                $result[]='Зафиксировано движение.';
            } else {
                $result[]='Движение отсутствует. Последнее движение было '.date('d.m.Y H:i:s',$this->lastMotion).'.';
            }
        }
        if ($this->voltage) {
            $result[]=sprintf('Батарея CR2032: %.3f В.',$this->voltage);
        }
        return join(' ',$result);
    }

    public function getDeviceIndicators(): array {
        return ['alarm'=>'Зафиксировано движение'];
    }

    public function getDeviceMeters(): array {
        return [];
    }

}
