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

    # Методы для работы с кодированными сообщенями

    public function setToken($token) {
        $this->token=$token;
        $this->key=md5(hex2bin($this->token));
        $this->iv=md5(hex2bin($this->key.$this->token));
    }

    private function encrypt($data) {
        return bin2hex(openssl_encrypt($data,'AES-128-CBC',hex2bin($this->key),OPENSSL_RAW_DATA,hex2bin($this->iv)));
    }

    public function decryptMessage() {
        return openssl_decrypt(hex2bin($this->data),'AES-128-CBC',hex2bin($this->key),OPENSSL_RAW_DATA,hex2bin($this->iv));
    }

    public function buildMessage($msg) {
        $this->data=$this->encrypt($msg);
        $this->length=sprintf('%04x',(int)strlen($this->data)/2+32);
        $this->timestamp=sprintf('%08x',time()+$this->timeDiff);
        $packet=$this->magic.$this->length.$this->device_id.$this->timestamp.$this->token.$this->data;
        $this->checksum=md5(hex2bin($packet));
        $packet=$this->magic.$this->length.$this->device_id.$this->timestamp.$this->checksum.$this->data;
        return $packet;
    }

}
