<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-29
 * Датчик движения Xiaomi
 */

namespace Xiaomi\Devices;

class MotionSensor extends AbstractDevice {

    private $lastMotion;

    protected function updateParam($param, $value) {
        switch ($param) {
            case "status":
                if ($value=='motion') {
                    $this->setLastMotion(0);
                } else {
                    $this->showUnknownParam($param, $value);
                }
                break;
            case "no_motion":
                $this->setLastMotion(intval($value));
                break;
            default:
                $this->showUnknownParam($param, $value);
        }
    }

    private function setLastMotion(int $value) {
        $this->lastMotion=$value==0?0:time()-$value;
        $this->events['last_motion']=$value;
        $this->events['motion']=$value==0;
    }

    public function getLastMotion() {
        return $this->lastMotion;
    }

    public function getDescription(): string {
        return "Xiaomi Smart IR Human Body Sensor";
    }

    public function getState(): array {
        return [
            'last_motion'=>$this->lastMotion,
            'voltage'=>$this->voltage
        ];
    }

    public function __toString(): string {
        $result=[];
        if (!is_null($this->lastMotion)) {
            if ($this->lastMotion==0) {
                $result[]='Зафиксировано движение.';
            } else {
                $result[]='Движение отсутствует. Последнее движение было '.date('d.m.Y H:i:s', $this->lastMotion).'.';
            }
        }
        if ($this->voltage) {
            $result[]=sprintf('Батарея CR2450: %.3f В.', $this->voltage);
        }
        return join(' ', $result);
    }

    public function getEventsList(): array {
        return ['motion', 'last_motion', 'voltage'];
    }

}
