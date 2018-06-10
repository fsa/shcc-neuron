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

    protected function setStatus(string $value) {
        $last=$this->status;
        $this->status=$value;
        if($last!=$value) {
            $this->actions['status']=$value;
        }
    }

}
