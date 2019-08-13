<?php

namespace Yandex\SmartHome;

class DeviceState implements \JsonSerializable {
    
    private $id;
    private $capabilities;
    
    public function __construct(string $id) {
        $this->id=$id;
    }
    
    public function jsonSerialize() {
        if(is_null($this->id) or is_null($this->capabilities)) {
            throw new \AppException('Не заданы все требуемые параметры устройства: id, type, capabilities');
        }
        return [
            "id"=>$this->id,
            "capabilities"=>$this->capabilities
        ];
    }

    public function setId(string $id=null) {
        $this->id=$id;
    }


    public function addCapabilitie(Capabilities\State $state) {
        $this->capabilities[]=$state;
    }
}