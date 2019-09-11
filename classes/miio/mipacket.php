<?php

namespace miIO;

class MiPacket {
    
    private $magic='2131';
    private $length;
    private $device_id;
    private $timestamp;
    private $checksum;
    private $data;
    private $token;
    private $key;
    private $iv;
    private $remote_peer;

    public function __construct(string $pkt=null, string $peer=null) {
        if(!is_null($pkt)) {
            $this->parseMessage(bin2hex($pkt));
            $this->setDeviceAddr($peer);
        }
    }
    
    public function getDeviceId(): string {
        return $this->device_id;
    }

    public function setDeviceId(string $id): void {
        $this->device_id=$id;
    }

    public function getDeviceTimestamp(): int {
        return hexdec($this->timestamp);
    }

    public function getDeviceToken(): ?string {
        return $this->token;
    }

    public function isMiIOPacket(): bool {
        return $this->magic=='2131';
    }

    public function isHelloPacket(): bool {
        return $this->length=='0020';
    }

    public function setDeviceAddr($peer): void {
        $this->remote_peer=$peer;
    }

    public function getDeviceAddr(): string {
        return $this->remote_peer;
    }

    public function parseMessage($msg): void {
        $this->magic=substr($msg,0,4);
        $this->length=substr($msg,4,4);
        $this->device_id=substr($msg,8,16);
        $this->timestamp=substr($msg,24,8);
        $this->checksum=substr($msg,32,32);
        if (($this->length=='0020')&&(strlen($msg)==64)) {
            $token=substr($msg,32,32);
            if(is_null($this->token) and $token!='00000000000000000000000000000000') {
                $this->setToken($token);
            }
        } else {
            $this->data=strlen($msg)>64?substr($msg,64, hexdec($this->length)*2-64):'';
        }
    }
    
    # Методы для работы с кодированными сообщенями

    public function setToken($token): void {
        $this->token=$token;
        $this->key=md5(hex2bin($this->token));
        $this->iv=md5(hex2bin($this->key.$this->token));
    }

    private function encryptMessage($data): string {
        return bin2hex(openssl_encrypt($data,'AES-128-CBC',hex2bin($this->key),OPENSSL_RAW_DATA,hex2bin($this->iv)));
    }

    public function decryptMessage(): string {
        return openssl_decrypt(hex2bin($this->data),'AES-128-CBC',hex2bin($this->key),OPENSSL_RAW_DATA,hex2bin($this->iv));
    }

    public function buildMessage(string $msg, int $time): string {
        $this->data=$this->encryptMessage($msg);
        $this->length=sprintf('%04x',(int)strlen($this->data)/2+32);
        $this->timestamp=sprintf('%08x',$time);
        $packet=$this->magic.$this->length.$this->device_id.$this->timestamp.$this->token.$this->data;
        $this->checksum=md5(hex2bin($packet));
        $packet=$this->magic.$this->length.$this->device_id.$this->timestamp.$this->checksum.$this->data;
        return hex2bin($packet);
    }

    public static function getHelloMessage(): string {
        return hex2bin('21310020ffffffffffffffffffffffffffffffffffffffffffffffffffffffff');
    }

    public function getDeviceObject() {
        $obj=new UnknownDevice;
        $obj->update($this);
        return $obj;
    }

}
