<?php

namespace miIO;

class GenericDevice implements \SmartHome\DeviceInterface {

    private $uid;
    private $ip;
    private $token;
    private $key='';
    private $iv='';
    private $updated;

    public function getDeviceDescription(): string {
        return "Неизвестное устройство";
    }

    public function getDeviceId(): string {
        return $this->uid;
    }

    public function getDeviceStatus(): string {
        return "Неизвестно.";
    }

    public function getInitDataList(): array {
        return ['ip'=>'IP адрес','token'=>'Токен'];
    }

    public function getInitDataValues(): array {
        return ['ip'=>$this->ip,'token'=>$this->token];
    }

    public function getLastUpdate(): int {
        return $this->updated;
    }

    public function getModuleName(): string {
        return "miio";
    }

    public function init($device_id,$init_data): void {
        $this->uid=$device_id;
        foreach ($init_data as $key=> $value) {
            $this->$key=$value;
        }
    }

}
