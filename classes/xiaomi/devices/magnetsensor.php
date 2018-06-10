<?php

/**
 * Датчик движения Xiaomi
 */

namespace Xiaomi\Devices;

class MagnetSensor extends AbstractDevice {
    
    private $status;
    private $lastActivity;

    protected function updateParam($param,$value) {
        switch ($param) {
            case "status":
                $this->setStatus($value);
                break;
            default:
                echo "$param => $value\n";
        }
    }
    
    protected function setStatus(string $value) {
        $this->lastActivity=date('c');
        $this->status=$value;
    }

}
