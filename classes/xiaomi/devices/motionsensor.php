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
        $this->lastMotion=$value;
        $this->actions['motion']=$value;
    }
    
    public function getLastMotion() {
        return $this->lastMotion;
    }

    public function getDeviceName() {
        return "Xiaomi Smart IR Human Body Sensor";
    }

}
