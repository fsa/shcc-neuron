<?php

/**
 * Шлюз Xiaomi
 */

namespace Xiaomi\Devices;

class XiaomiGateway extends AbstractDevice {

    private $ip;
    private $bright;
    private $rgb;
    private $token;
    private $key;
    private $illumination;

    public function update(\Xiaomi\XiaomiPacket $pkt) {
        $token=$pkt->getToken();
        if ($token!='') {
            $this->token=$token;
        }
        parent::update($pkt);
    }
    
    public function setKey($key) {
        $this->key=$key;
    }

    public function prepareCommand(array $cmd_data) {
        if(is_null($this->token)) {
            return false;
        }
        $cmd_data['key']=$this->makeSignature();
        $data=['cmd'=>'write'];
        $data['sid']=$this->sid;
        $data['short_id']=0;
        $data['model']=$this->model;
        $data['data']=json_encode($cmd_data);
        return json_encode($data);
    }
    
    protected function updateParam($param,$value) {
        switch ($param) {
            case "illumination":
                $this->setIllumination($value);
                break;
            case "rgb":
                $this->setRgb($value);
                break;
            case "ip":
                $this->ip=$value;
                break;
            default:
                echo "$param => $value\n";
        }
    }

    private function setIllumination($value) {
        $this->illumination=$value;
    }

    private function setRgb($value) {
        $parts=str_split(dechex($value),2);
        if (sizeof($parts)!=4) {
            $this->bright=0;
            $this->rgb="000000";
            return;
        }
        $this->bright=hexdec($parts[0]);
        $this->rgb=$parts[1].$parts[2].$parts[3];
    }
    

    private function makeSignature() {
        $iv=hex2bin('17996d093d28ddb3ba695a2e6f58562e');
        $bin_data=base64_decode(openssl_encrypt($this->token,'AES-128-CBC',$this->key,OPENSSL_ZERO_PADDING,$iv));
        return bin2hex($bin_data);
    }

}
