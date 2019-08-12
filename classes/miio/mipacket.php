<?php

namespace miIO;

class MiPacket {

    private $magic='2131';
    private $length='';
    private $device_id='';
    private $timestamp='';
    private $checksum='';
    private $data;
    private $token='';
    private $firstSet=true;
    private $timeDiff=0;
    private $remote_ip;
    private $remote_port;

    public function getRemoteIp() {
        return $this->remote_ip;
    }
    
    public function getRemotePort() {
        return $this->remote_port;
    }
    
    public function getDeviceId() {
        return $this->device_id;
    }
    
    public function setRemoteAddr($ip,$port=false) {
        $this->remote_ip=$ip;
        if($port) {
            $this->remote_port=$port;
        }
    }

    public function parseMessage($msg) {
        $this->magic=substr($msg,0,4);
        $this->length=substr($msg,4,4);
        $this->device_id=substr($msg,8,16);
        $this->timestamp=substr($msg,24,8);
        $this->checksum=substr($msg,32,32);
        if (($this->length=='0020')&&(strlen($msg)==64)) {
            if($this->token=='') {
                $this->setToken(substr($msg,32,32));
            }
            $timeDiff=hexdec($this->timestamp)-time();
            if ($this->firstSet&&$timeDiff!=0) {
                $this->timeDiff=$timeDiff;
            }
            if ($this->firstSet) {
                $this->firstSet=false;
            }
        } else {
            $data_length=strlen($msg)-64;
            if ($data_length>0) {
                $this->data=substr($msg,64,$data_length);
            }
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
