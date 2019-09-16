<?php

namespace Xiaomi\Devices;

abstract class AbstractDevice implements \SmartHome\DeviceInterface {

    protected $sid;
    protected $model;
    protected $voltage;
    protected $updated;
    protected $actions;

    public function __construct() {
        $this->actions=[];
        $this->updated=0;
    }

    public function init($device_id,$init_data): void {
        $this->sid=$device_id;
        foreach ($init_data as $key=> $value) {
            $this->$key=$value;
        }
    }
    
    public function getInitDataList(): array {
        return [];
    }

    public function getInitDataValues(): array {
        return [];
    }
    
    public function update(\Xiaomi\XiaomiPacket $pkt) {
        $this->actions=[];
        $this->actions['cmd']=$pkt->getCmd();
        $this->sid='xiaomi_'.$pkt->getSid();
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
        return json_encode($this->actions);
    }

    public function getModuleName(): string {
        return 'xiaomi';
    }

    public function getDeviceId(): string {
        return $this->sid;
    }

    public function getLastUpdate(): int {
        return $this->updated;
    }

    public function getVoltage() {
        return $this->voltage;
    }
    
    protected function showUnknownParam($param, $value) {
        printf('%s=>{%s=%s}',$this->getDeviceId(),$param,$value);
    }

    abstract protected function updateParam($param,$value);
}
