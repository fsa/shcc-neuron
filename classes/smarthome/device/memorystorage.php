<?php

namespace SmartHome\Device;

use Settings;

class MemoryStorage {

    const PROJ='f';
    const CHMOD=0600;
    const MEMSIZE=1048576;

    private $shm;
    private $sem;
    private $stmt;
    private $lock;

    public function __construct() {
        $memsize=Settings::get('device_memory_storage', self::MEMSIZE);
        $file=ftok(__FILE__, self::PROJ);
        $this->shm=shm_attach($file, $memsize, self::CHMOD);
        $this->sem=sem_get($file, 1, self::CHMOD);
        if (shm_has_var($this->shm, 0)) {
            return;
        }
        sem_acquire($this->sem);
        $devices=[0=>'device_list'];
        if (!shm_put_var($this->shm, 0, $devices)) {
            throw new \AppException('Не удалось инициализировать разделяемую память. Дальнейшая работа с устройствами невозможна.');
        }
        sem_release($this->sem);
    }

    public function lockMemory(): void {
        sem_acquire($this->sem);
        $this->lock=true;
    }

    public function releaseMemory(): void {
        sem_release($this->sem);
        $this->lock=null;
    }

    public function setDevice(string $uid, \SmartHome\DeviceInterface $object): void {
        if(is_null($this->lock)) {
            sem_acquire($this->sem);
        }
        $devices=shm_get_var($this->shm, 0);
        $key=array_search($uid, $devices);
        if ($key===false) {
            $key=sizeof($devices);
            $devices[$key]=$uid;
            shm_put_var($this->shm, 0, $devices);
        }
        shm_put_var($this->shm, $key, $object);
        if(is_null($this->lock)) {
            sem_release($this->sem);
        }
    }

    public function getDevice(string $uid): ?\SmartHome\DeviceInterface {
        if(is_null($this->lock)) {
            sem_acquire($this->sem);
        }
        $devices=shm_get_var($this->shm, 0);
        $key=array_search($uid, $devices);
        $device=($key===false)?null:shm_get_var($this->shm, $key);
        if(is_null($this->lock)) {
            sem_release($this->sem);
        }
        return $device;
    }

    public function existsDevice(string $uid): bool {
        if(is_null($this->lock)) {
            sem_acquire($this->sem);
        }
        $devices=shm_get_var($this->shm, 0);
        $key=array_search($uid, $devices);
        if(is_null($this->lock)) {
            sem_release($this->sem);
        }
        return $key!==false;
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
