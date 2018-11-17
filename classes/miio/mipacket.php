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
}
