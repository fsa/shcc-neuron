<?php

namespace SmartHome;

use Settings;

class MemoryStorage {

    const PROJ='f';
    const CHMOD=0600;
    const MEMSIZE=1048576;

    private $shm;
    private $sem;
    private $lock;

    public function __construct() {
        $memsize=Settings::get('device_memory_storage', self::MEMSIZE);
        $file=ftok(__FILE__, self::PROJ);
        $this->shm=shm_attach($file, $memsize, self::CHMOD);
        if($this->shm===false) {
            throw new UserException('Не удалось инициализировать разделяемую память. Дальнейшая работа с устройствами невозможна.');
        }
        $this->sem=sem_get($file, 1, self::CHMOD);
        $this->lockMemory();
        if (shm_has_var($this->shm, 0)) {
            $this->releaseMemory();
            return;
        }
        $devices=[0=>'device_list'];
        if (!shm_put_var($this->shm, 0, $devices)) {
            throw new UserException('Не удалось записать значение в разделяемую память. Дальнейшая работа с устройствами невозможна.');
        }
        $devices_entity=Devices::getAllDevicesEntity();
        foreach($devices_entity as $hwid=>$entity) {
            $this->setDevice($hwid, $entity);
        }
        $this->releaseMemory();
    }

    public function lockMemory(): void {
        sem_acquire($this->sem);
        $this->lock=true;
    }

    public function releaseMemory(): void {
        sem_release($this->sem);
        $this->lock=null;
    }

    public function setDevice(string $hwid, \SmartHome\DeviceInterface $object): void {
        if (is_null($this->lock)) {
            sem_acquire($this->sem);
        }
        $devices=shm_get_var($this->shm, 0);
        $key=array_search('d_'.$hwid, $devices);
        if ($key===false) {
            $key=sizeof($devices);
            $devices[$key]='d_'.$hwid;
            shm_put_var($this->shm, 0, $devices);
        }
        shm_put_var($this->shm, $key, $object);
        if (is_null($this->lock)) {
            sem_release($this->sem);
        }
    }

    public function getDevice(string $hwid): ?\SmartHome\DeviceInterface {
        if (is_null($this->lock)) {
            sem_acquire($this->sem);
        }
        $devices=shm_get_var($this->shm, 0);
        $key=array_search('d_'.$hwid, $devices);
        $device=($key===false)?null:shm_get_var($this->shm, $key);
        if (is_null($this->lock)) {
            sem_release($this->sem);
        }
        return $device;
    }

    public function existsDevice(string $hwid): bool {
        if (is_null($this->lock)) {
            sem_acquire($this->sem);
        }
        $devices=shm_get_var($this->shm, 0);
        $key=array_search('d_'.$hwid, $devices);
        if (is_null($this->lock)) {
            sem_release($this->sem);
        }
        return $key!==false;
    }

    public function setSensor(string $uid, $value, $ts=null): void {
        if (is_null($this->lock)) {
            sem_acquire($this->sem);
        }
        $devices=shm_get_var($this->shm, 0);
        $key=array_search('s_'.$uid, $devices);
        if ($key===false) {
            $key=sizeof($devices);
            $devices[$key]='s_'.$uid;
            shm_put_var($this->shm, 0, $devices);
        }
        shm_put_var($this->shm, $key, (object) ["value"=>$value, "ts"=>is_null($ts)?time():$ts]);
        if (is_null($this->lock)) {
            sem_release($this->sem);
        }
    }

    public function getSensor(string $uid) {
        if (is_null($this->lock)) {
            sem_acquire($this->sem);
        }
        $devices=shm_get_var($this->shm, 0);
        $key=array_search('s_'.$uid, $devices);
        $sensor=($key===false)?null:shm_get_var($this->shm, $key);
        if (is_null($this->lock)) {
            sem_release($this->sem);
        }
        return $sensor;
    }

    public function existsSensor(string $uid): bool {
        if (is_null($this->lock)) {
            sem_acquire($this->sem);
        }
        $devices=shm_get_var($this->shm, 0);
        $key=array_search('s_'.$uid, $devices);
        if (is_null($this->lock)) {
            sem_release($this->sem);
        }
        return $key!==false;
    }

    public static function getDevicesHwids() {
        $mem=new self;
        $mem->lockMemory();
        $devices=shm_get_var($mem->shm, 0);
        $mem->releaseMemory();
        unset($devices[0]);
        $result=[];
        foreach ($devices as $hwid) {
            if (substr($hwid, 0, 2)=='d_') {
                $result[]=substr($hwid, 2);
            }
        }
        return $result;
    }

}
