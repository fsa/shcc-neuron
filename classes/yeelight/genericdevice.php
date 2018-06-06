<?php

namespace Yeelight;

class GenericDevice {

    const MIN_EFFECT_DURATION=30;

    private $location;
    private $id;
    private $model;
    private $fw_ver;
    private $support;
    private $power;
    private $bright;
    private $color_mode;
    private $ct;
    private $rgb;
    private $hue;
    private $sat;
    private $name;
    private $socket;
    private $persistent;
    private $message_id=1;

    public function __construct($persistent=false) {
        $this->persistent=$persistent;
    }

    public function __sleep() {
        return array('location','id','model','fw_ver','support','power','bright','color_mode','ct','rgb','hue','sat','name');
    }

    public function __destruct() {
        $this->closeSocket();
    }

    public function setPersistent(bool $value) {
        $this->persistent=$value;
    }

    public function updateState($params) {
        foreach ($params as $param=> $value) {
            switch ($param) {
                case "Location":
                    $this->location=$value;
                    break;
                case "id":
                    $this->id=$value;
                    break;
                case "model":
                    $this->model=$value;
                    break;
                case "fw_ver":
                    $this->fw_ver=$value;
                    break;
                case "support":
                    $this->support=explode(' ',$value);
                    break;
                case "power":
                    $this->power=$value;
                    break;
                case "bright":
                    $this->bright=$value;
                    break;
                case "color_mode":
                    $this->color_mode=$value;
                    break;
                case "ct":
                    $this->ct=$value;
                    break;
                case "rgb":
                    $this->rgb=dechex($value);
                    break;
                case "hue":
                    $this->hue=$value;
                    break;
                case "sat":
                    $this->sat=$value;
                    break;
                case "name":
                    $this->name=$value;
                    break;
                default:
            }
        }
    }

    private function getSocket() {
        if (!is_null($this->socket)) {
            return $this->socket;
        }
        $addr=parse_url($this->location);
        $socket=stream_socket_client("tcp://".$addr['host'].":".$addr['port'],$errno,$errstr);
        stream_set_timeout($socket,3);
        #stream_set_blocking($socket,false);
        if (!$socket) {
            throw new Exception("$errstr ($errno)");
        }
        $this->socket=$socket;
        return $socket;
    }

    public function getEffect(int $duration) {
        if ($duration>0 and $duration<30) {
            throw new Exception('Duration must be greater than 30.');
        }
        return ($duration==0)?'sudden':'smooth';
    }

    public function actionSetCtAbx(int $ct_value,int $duratin=0) {
        return $this->sendCommand('set_ct_abx',[$ct_value,$this->getEffect($duratin),$duratin]);
    }

    public function actionSetRGB(string $rgb,int $duratin=0) {
        return $this->sendCommand('set_rgb',[hexdec($rgb),$this->getEffect($duratin),$duratin]);
    }

    public function actionSetHSV(int $hue,int $sat,int $duratin=0) {
        return $this->sendCommand('set_hsv',[$hue,$sat,$this->getEffect($duratin),$duratin]);
    }

    public function actionSetBright(int $bright,int $duratin=0) {
        return $this->sendCommand('set_bright',[$bright,$this->getEffect($duratin),$duratin]);
    }

    public function actionSetPower(bool $on,int $duratin=0,$mode=0) {
        $param=[];
        $param[]=$on?'on':'off';
        $param[]=$this->getEffect($duratin);
        $param[]=$duratin;
        if ($mode!=0) {
            $param[]=$mode;
        }
        return $this->sendCommand('set_power',$param);
    }

    public function actionToggle() {
        return $this->sendCommand('toggle');
    }

    public function actionSetDefault() {
        return $this->sendCommand('set_default');
    }
    
    public function actionStartCF(int $count, int $action, string $flow_expression) {
        return $this->sendCommand('start_cf',[$count,$action,$flow_expression]);
    }

    /**
     * flow_expression helper
     * @param int $duration
     * @param int $mode
     * @param string $value
     * @param int $brightness
     * @return string
     * @throws Exception
     */
    public function changingState(int $duration, int $mode,string $value, int $brightness): string {
        if($mode<>1 and $mode<>2 and $mode<>7) {
            throw new Exception('Mode error: 1-color, 2-color temperature, 7-sleep');
        }
        $dig_value=($mode==1)?hexdec($value):(int) $value;
        return "$duration,$mode,$dig_value,$brightness";
    }

    public function actionStopCF() {
        return $this->sendCommand('stop_cf');
    }
    
    public function actionSetScene(string $class,array $params) {
        return $this->sendCommand("set_scene",$params);
    }
    
    private function sendCommand(string $method,array $params=[]) {
        $id=$this->message_id++;
        $cmd=[
            'id'=>$id,
            'method'=>$method,
            'params'=>$params
        ];
        $cmd=json_encode($cmd)."\r\n";
        $socket=$this->getSocket();
        stream_socket_sendto($socket,$cmd);
        # TODO: 3 ответа от start_cf
        $result=stream_socket_recvfrom($this->socket,1024);
        $result.=stream_socket_recvfrom($this->socket,1024);
        if (!$this->persistent) {
            fclose($this->socket);
            $this->socket=null;
        }
        return $result;
    }

    public function closeSocket() {
        if (!is_null($this->socket)) {
            fclose($this->socket);
            $this->socket=null;
        }
    }

}
