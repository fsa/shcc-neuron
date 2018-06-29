<?php

namespace Xiaomi\Devices;

abstract class AbstractDevice implements \SmartHome\DeviceInterface, \SmartHome\SensorsInterface {

    protected $sid;
    protected $model;
    protected $voltage;
    protected $updated;
    protected $actions;

    public function __construct() {
        $this->actions=[];
        $this->updated=0;
    }

    public function init($device_id,$init_data) {
        $this->sid=$device_id;
        foreach ($init_data as $key=> $value) {
            $this->$key=$value;
        }
    }

    public function update(\Xiaomi\XiaomiPacket $pkt) {
        $this->actions['cmd']=$pkt->getCmd();
        $this->sid=$pkt->getSid();
        $this->model=$pkt->getModel();
        foreach ($pkt->getData() as $param=> $value) {
            switch ($param) {
                case "voltage":
                    $this->setVoltage($value);
                    break;
                default:
                    $this->updateParam($param,$value);
            }
        }
        $this->updated=time();
    }

    protected function setVoltage($value) {
        $last=$this->voltage;
        $this->voltage=$value/1000;
        if ($this->voltage!=$last) {
            $this->actions['voltage']=$this->voltage;
        }
    }

    public function getActions() {
        if (sizeof($this->actions)<2) {
            return null;
        }
        $actions=json_encode($this->actions);
        $this->actions=[];
        return $actions;
    }

    public function getModuleName() {
        return 'xiaomi';
    }

    public function getDeviceId() {
        return $this->sid;
    }

    public function getLastUpdate(): int {
        return $this->updated;
    }

    public function getVoltage() {
        return $this->voltage;
    }

    abstract protected function updateParam($param,$value);

    abstract public function getDeviceName();

    abstract public function __toString();
}
