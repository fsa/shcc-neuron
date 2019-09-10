<?php

namespace miIO;

class UnknownDevice implements \SmartHome\DeviceInterface {

    private $uid;
    private $token;
    private $updated;
    private $mipacket;

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
        return ['token'=>'Токен'];
    }

    public function getInitDataValues(): array {
        return ['token'=>$this->token];
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
    
    public function update(MiPacket $pkt) {
        $this->mipacket=$pkt;
        $this->uid=$pkt->getDeviceId();
        $this->updated=time();
    }

}
