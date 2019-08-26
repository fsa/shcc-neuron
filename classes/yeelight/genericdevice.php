<?php

namespace Yeelight;

class GenericDevice implements \SmartHome\DeviceInterface, \SmartHome\Device\Capability\PowerInterface, \SmartHome\Device\Capability\ColorHsvInterface, \SmartHome\Device\Capability\ColorRgbInterface, \SmartHome\Device\Capability\ColorTInterface {

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
    private $updated;
    private $message_id=1;
    private $actions;

    public function __construct() {
        $this->updated=0;
    }

    public function __sleep() {
        return array('location', 'id', 'model', 'fw_ver', 'support', 'power', 'bright', 'color_mode', 'ct', 'rgb', 'hue', 'sat', 'name', 'updated');
    }

    public function __destruct() {
        $this->disconnect();
    }

    public function init($device_id, $init_data) {
        $parts=explode('_', $device_id, 2);
        $this->model=$parts[0];
        $this->id=$parts[1];
        foreach ($init_data as $key=> $value) {
            $this->$key=$value;
        }
    }

    public function getInitDataList() {
        return ['location'=>'IP адрес'];
    }

    public function getInitDataValues() {
        return ['location'=>$this->location];
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
                    $this->support=explode(' ', $value);
                    break;
                case "power":
                    $this->setPowerValue($value);
                    break;
                case "bright":
                    $this->setBrightValue($value);
                    break;
                case "color_mode":
                    $this->setColorModeValue($value);
                    break;
                case "ct":
                    $this->setCtValue($value);
                    break;
                case "rgb":
                    $this->setRGBValue(dechex($value));
                    break;
                case "hue":
                    $this->setHueValue($value);
                    break;
                case "sat":
                    $this->setSatValue($value);
                    break;
                case "name":
                    $this->name=$value;
                    break;
                default:
            }
        }
        $this->updated=time();
    }

    private function setPowerValue($value) {
        if ($this->power==$value) {
            return;
        }
        $this->power=$value;
        $this->actions['power']=$value;
    }

    private function setBrightValue($value) {
        if ($this->bright==$value) {
            return;
        }
        $this->bright=$value;
        $this->actions['bright']=$value;
    }

    private function setColorModeValue($value) {
        if ($this->color_mode==$value) {
            return;
        }
        $this->color_mode=$value;
        $this->actions['color_mode']=$value;
    }

    private function setCtValue($value) {
        if ($this->ct==$value) {
            return;
        }
        $this->ct=$value;
        $this->actions['ct']=$value;
    }

    private function setRGBValue($value) {
        if ($this->rgb==$value) {
            return;
        }
        $this->rgb=$value;
        $this->actions['rgb']=$value;
    }

    private function setHueValue($value) {
        if ($this->hue==$value) {
            return;
        }
        $this->hue=$value;
        $this->actions['hue']=$value;
    }

    private function setSatValue($value) {
        if ($this->sat==$value) {
            return;
        }
        $this->sat=$value;
        $this->actions['sat']=$value;
    }

    public function getActions() {
        if (sizeof($this->actions)==0) {
            return null;
        }
        $actions=json_encode($this->actions);
        $this->actions=[];
        return $actions;
    }

    private function getSocket() {
        if (!is_null($this->socket)) {
            return $this->socket;
        }
        $addr=parse_url($this->location);
        $socket=stream_socket_client("tcp://".$addr['host'].":".$addr['port'], $errno, $errstr);
        stream_set_timeout($socket, 3);
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

    public function sendGetProp(array $params): int {
        return $this->sendCommand('get_prop', $params);
    }

    public function sendSetCtAbx(int $ct_value, int $duratin=0): int {
        return $this->sendCommand('set_ct_abx', [$ct_value, $this->getEffect($duratin), $duratin]);
    }

    public function sendBgSetCtAbx(int $ct_value, int $duratin=0): int {
        return $this->sendCommand('bg_set_ct_abx', [$ct_value, $this->getEffect($duratin), $duratin]);
    }

    public function sendSetRGB(string $rgb, int $duratin=0): int {
        return $this->sendCommand('set_rgb', [hexdec($rgb), $this->getEffect($duratin), $duratin]);
    }

    public function sendBgSetRGB(string $rgb, int $duratin=0): int {
        return $this->sendCommand('bg_set_rgb', [hexdec($rgb), $this->getEffect($duratin), $duratin]);
    }

    public function sendSetHSV(int $hue, int $sat, int $duratin=0): int {
        return $this->sendCommand('set_hsv', [$hue, $sat, $this->getEffect($duratin), $duratin]);
    }

    public function sendBgSetHSV(int $hue, int $sat, int $duratin=0): int {
        return $this->sendCommand('bg_set_hsv', [$hue, $sat, $this->getEffect($duratin), $duratin]);
    }

    public function sendSetBright(int $bright, int $duratin=0): int {
        return $this->sendCommand('set_bright', [$bright, $this->getEffect($duratin), $duratin]);
    }

    public function sendBgSetBright(int $bright, int $duratin=0): int {
        return $this->sendCommand('bg_set_bright', [$bright, $this->getEffect($duratin), $duratin]);
    }

    public function sendSetPower(bool $on, int $duratin=0, $mode=0): int {
        $param=[];
        $param[]=$on?'on':'off';
        $param[]=$this->getEffect($duratin);
        $param[]=$duratin;
        if ($mode!=0) {
            $param[]=$mode;
        }
        return $this->sendCommand('set_power', $param);
    }

    public function sendBgSetPower(bool $on, int $duratin=0, $mode=0): int {
        $param=[];
        $param[]=$on?'on':'off';
        $param[]=$this->getEffect($duratin);
        $param[]=$duratin;
        if ($mode!=0) {
            $param[]=$mode;
        }
        return $this->sendCommand('bg_set_power', $param);
    }

    public function sendToggle(): int {
        return $this->sendCommand('toggle');
    }

    public function sendBgToggle(): int {
        return $this->sendCommand('bg_toggle');
    }

    public function sendSetDefault(): int {
        return $this->sendCommand('set_default');
    }

    public function sendBgSetDefault(): int {
        return $this->sendCommand('bg_set_default');
    }

    public function sendStartCF(int $count, int $action, string $flow_expression): int {
        return $this->sendCommand('start_cf', [$count, $action, $flow_expression]);
    }

    public function sendBgStartCF(int $count, int $action, string $flow_expression): int {
        return $this->sendCommand('bg_start_cf', [$count, $action, $flow_expression]);
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
    public function changingState(int $duration, int $mode, string $value, int $brightness): string {
        if ($mode<>1 and $mode<>2 and $mode<>7) {
            throw new Exception('Mode error: 1-color, 2-color temperature, 7-sleep');
        }
        $dig_value=($mode==1)?hexdec($value):(int) $value;
        return "$duration,$mode,$dig_value,$brightness";
    }

    public function sendStopCF(): int {
        return $this->sendCommand('stop_cf');
    }

    public function sendBgStopCF(): int {
        return $this->sendCommand('bg_stop_cf');
    }

    public function sendSetScene(array $params): int {
        return $this->sendCommand("set_scene", $params);
    }

    public function sendBgSetScene(array $params): int {
        return $this->sendCommand("bg_set_scene", $params);
    }

    public function sendCronAdd(int $type, int $value): int {
        return $this->sendCommand('cron_add', [$type, $value]);
    }

    public function sendCronGet(int $type): int {
        return $this->sendCommand('cron_get', [$type]);
    }

    public function sendCronDel(int $type): int {
        return $this->sendCommand('cron_del', [$type]);
    }

    public function sendSetAdjust(string $action, string $prop): int {
        return $this->sendCommand('set_adjust', [$action, $prop]);
    }

    public function sendBgSetAdjust(string $action, string $prop): int {
        return $this->sendCommand('bg_set_adjust', [$action, $prop]);
    }

    public function sendSetMusic(string $host='', int $port=0): int {
        if ($host=='') {
            return $this->sendCommand('set_music', [0]);
        } else {
            return $this->sendCommand('set_music', [1, $host, $port]);
        }
    }

    public function sendSetName(string $name): int {
        return $this->sendCommand('set_name', [$name]);
    }

    public function sendDevToggle(): int {
        return $this->sendCommand('dev_toggle');
    }

    private function sendCommand(string $method, array $params=[]): int {
        $id=$this->message_id++;
        $cmd=[
            'id'=>$id,
            'method'=>$method,
            'params'=>$params
        ];
        $cmd=json_encode($cmd)."\r\n";
        $socket=$this->getSocket();
        stream_socket_sendto($socket, $cmd);
        return $id;
    }

    public function getResponse(): string {
        return stream_get_contents($this->socket);
    }

    public function disconnect() {
        if (!is_null($this->socket)) {
            fclose($this->socket);
            $this->socket=null;
        }
    }

    public function getLastUpdate() {
        return $this->updated;
    }

    public function getModuleName() {
        return 'yeelight';
    }

    public function getDeviceId() {
        return $this->model.'_'.$this->id;
    }

    public function getDeviceDescription() {
        switch ($this->model) {
            case "lamp":
                return "Настольная лампа";
            case "mono":
                return "Лампа";
            case "color":
                return "RGB-лампа";
            case "stripe":
                return "Светодиодная лента";
            case "ceiling":
                return "Потолочный светильник";
            case "bslamp":
            case "bslamp1":
                return "Прикроватный светильник";
            default:
                return $this->model;
        }
    }

    public function getDeviceStatus() {
        return $this->power=="on"?"Включена":"Выключена";
    }

    public function setPower(bool $value) {
        $this->sendSetPower($value);
    }

    public function setPowerOff() {
        $this->sendSetPower(false);
    }

    public function setPowerOn() {
        $this->sendSetPower(true);
    }

    public function setColorTemperature(int $ct_value) {
        $this->sendSetCtAbx($ct_value);
    }

    public function setHSV($hue, $sat, $value) {
        $this->sendSetHSV($hue, $sat);
        $this->sendSetBright($value);
    }

    public function setRGB(int $rgb) {
        $this->sendSetRGB(dechex($rgb));
    }

}
