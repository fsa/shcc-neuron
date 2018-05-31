<?php

namespace Xiaomi\Devices;

abstract class AbstractDevice {

    protected $sid;
    protected $model;
    protected $voltage;
    protected $updated;

    public function __construct() {
        
    }

    public function update(\Xiaomi\XiaomiPacket $pkt) {
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
        $this->updated=date('c');
    }

    protected function setVoltage($value) {
        #$last=$this->voltage;
        $this->voltage=$value/1000;
        /* TODO событие изменения напряжения
          if($this->voltage!=$last) {

          } */
    }

    abstract protected function updateParam($param,$value);
}
