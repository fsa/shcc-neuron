<?php

namespace SmartHome\Device;

class MemoryStorage {

    const PROJ='f';
    const CHMOD=0600;
    const MEMSIZE=1048576;

    private $shm;
    private $stmt;

    public function __construct() {
        $memsize=\Settings::get('device_memory_storage', self::MEMSIZE);
        $file=ftok(__FILE__, self::PROJ);
        $this->shm=shm_attach($file, $memsize, self::CHMOD);
        if (shm_has_var($this->shm, 0)) {
            return;
        }
        $devices=[0=>'device_list'];
        if (!shm_put_var($this->shm, 0, $devices)) {
            throw new \AppException('Не удалось инициализировать разделяемую память. Дальнейшая работа с устройствами невозможна.');
        }
    }

    public function setDevice(string $uid, \SmartHome\DeviceInterface $object): void {
        $devices=shm_get_var($this->shm, 0);
        $key=array_search($uid, $devices);
        if ($key===false) {
            $key=sizeof($devices);
            $devices[$key]=$uid;
            shm_put_var($this->shm, 0, $devices);
        }
        shm_put_var($this->shm, $key, $object);
    }

    public function getDevice(string $uid): ?\SmartHome\DeviceInterface {
        $devices=shm_get_var($this->shm, 0);
        $key=array_search($uid, $devices);
        if ($key===false) {
            return null;
        }
        return shm_get_var($this->shm, $key);
    }

    public function selectDevices(): void {
        $devices_id=shm_get_var($this->shm, 0);
        natsort($devices_id);
        $this->stmt=$devices_id;
        array_shift($this->stmt);
    }

    public function fetch(): ?object {
        $uid=array_shift($this->stmt);
        if(is_null($uid)) {
            return null;
        }
        $device=$this->getDevice($uid);
        $result=new \stdClass();
        $result->obj=$device;
        $result->uid=$uid;
        $result->name=$device->getDeviceDescription();
        $result->status=$device->getDeviceStatus();
        $date=$device->getLastUpdate();
        $result->updated=$date==0?'Offline':date('d.m.Y H:i:s',$date);
        return $result;
    }

    public function closeCursor(): void {
        $this->stmt=null;
    }

}
