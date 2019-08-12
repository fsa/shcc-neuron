<?php

namespace miIO;

class GenericDevice implements \SmartHome\DeviceInterface {

    private $uid;
    private $ip;
    private $token;
    private $key='';
    private $iv='';
    private $updated;

    public function getDeviceDescription() {
        return "Неизвестное устройство";
    }

    public function getDeviceId() {
        return $this->uid;
    }

    public function getDeviceStatus() {
        return "Неизвестно.";
    }

    public function getInitDataList() {
        return ['ip'=>'IP адрес','token'=>'Токен'];
    }

    public function getInitDataValues() {
        return ['ip'=>$this->ip,'token'=>$this->token];
    }

    public function getLastUpdate() {
        return $this->updated;
    }

    public function getModuleName() {
        return "miio";
    }

    public function init($device_id,$init_data) {
        $this->uid=$device_id;
        foreach ($init_data as $key=> $value) {
            $this->$key=$value;
        }
    }

}
