<?php

namespace Yandex\SmartHome;

class DeviceInfo implements \JsonSerializable {
    
    private $id;
    private $name;
    private $description;
    private $room;
    private $type;
    private $custom_data;
    private $capabilities;
    private $device_info;
    
    public function __construct(string $id) {
        $this->id=$id;
    }
    
    public function jsonSerialize() {
        if(is_null($this->id) or is_null($this->type) or is_null($this->capabilities)) {
            throw new \AppException('Не заданы все требуемые параметры устройства: id, type, capabilities');
        }
        $result=[
            "id"=>$this->id,
            "type"=>$this->type,
            "capabilities"=>$this->capabilities
        ];
        if(!is_null($this->name)) {
            $result['name']=$this->name;
        }
        if(!is_null($this->description)) {
            $result['description']=$this->description;
        }
        if(!is_null($this->room)) {
            $result['room']=$this->room;
        }
        if(!is_null($this->custom_data)) {
            $result['custom_data']=$this->custom_data;
        }
        if(!is_null($this->device_info)) {
            $result['device_info']=$this->device_info;
        }
        return $result;
    }

    public function setId(string $id=null) {
        $this->id=$id;
    }

    public function setName(string $name) {
        $this->name=$name;
    }

    public function setDescription(string $description) {
        $this->name=$description;
    }
    
    public function setRoom(string $room) {
        $this->room=$room;
    }
    
    public function setType(string $type) {
        switch ($type) {
            case 'light':
            case 'socket':
            case 'switch':
            case 'thermostat':
            case 'thermostat.ac':
            case 'media_device':
            case 'media_device.tv':
            case 'cooking':
            case 'cooking.kettle':
            case 'other':
                $this->type='devices.types.'.$type;
                break;
            default:
                throw new \AppException('Неверный тип устройства');
        }
    }
    
    public function setDeviceManufacturer(string $manufacturer) {
        $this->device_info['manufacturer']=$manufacturer;
    }

    public function setDeviceModel(string $model) {
        $this->device_info['model']=$model;
    }

    public function setDeviceHWVersion(string $hw_version) {
        $this->device_info['hw_version']=$hw_version;
    }

    public function setDeviceSWVersion(string $sw_version) {
        $this->device_info['sw_version']=$sw_version;
    }
    
    public function setCapabilities(array $capabilities) {
        $this->capabilities=$capabilities;
    }

    public function addCapabilitie(Capabilities\Description $description) {
        $this->capabilities[]=$description;
    }
}