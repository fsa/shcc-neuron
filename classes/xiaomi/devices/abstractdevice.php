<?php

namespace Xiaomi\Devices;

abstract class AbstractDevice {

    protected $sid;
    protected $model;
    protected $voltage;
    protected $updated;
    protected $actions;

    public function __construct() {
        $this->actions=[];
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
        $this->updated=date('Y-m-d H:i:sP');
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
    
    public function getDeviceId() {
        return $this->sid;
    }

    public function getLastUpdate() {
        return $this->updated;
    }

    abstract protected function updateParam($param,$value);

    abstract public function getDeviceName();
}
