<?php

namespace OpenWeatherMap\Devices;

use Settings,
    OpenWeatherMap\Api,
    SmartHome\DeviceStorage;

class Current implements \SmartHome\DeviceInterface {

    public $weather;

    /** @var OpenWeatherMap\Api * */
    private $owm;
    private $hwid;
    private $api_key;
    private $city_id;
    private $updated;

    public function __construct() {
        $this->updated=0;
    }

    public function getEventsList(): array {
        return ['temperature', 'humidity', 'pressure', 'wind_speed', 'wind_direction'];
    }

    public function getDescription(): string {
        return 'Текущая погода с OpenWeaterMap.org';
    }

    public function getHwid(): string {
        return $this->hwid;
    }

    public function getState(): array {
        if(is_null($this->weather)) {
            return [];
        }
        return [
            'temperature'=>round($this->getTemperature(),1),
            'temp_feels_like'=>round($this->getTempFeelsLike(),1),
            'humidity'=>round($this->getHumidity()),
            'pressure'=>$this->getPressure(),
            'description'=>$this->weather->weather[0]->description,
            'wind_speed'=>$this->weather->wind->speed,
            'wind_direction'=>$this->weather->wind->deg,
            'wind_direction_string'=>$this->getWindDirection()
                ];    }

    public function __toString(): string {
        if (is_null($this->weather)) {
            return 'Информация о погоде отсутствует';
        }
        return $this->getTemperature().'('.$this->getTempFeelsLike().')&deg;C, '.$this->getHumidity().'%, '.$this->getPressure().'&nbsp;мм.рт.ст., '.$this->weather->weather[0]->description.', ветер '.$this->weather->wind->speed.' м/с, направление '.$this->getWindDirection().' ('.$this->weather->wind->deg.')';
    }

    public function getInitDataList(): array {
        return ['api_key'=>'Ключ API', 'city_id'=>'ID города'];
    }

    public function getInitDataValues(): array {
        return ['api_key'=>$this->api_key, 'city_id'=>$this->city_id];
    }

    public function getLastUpdate(): int {
        return $this->updated;
    }

    public function init($device_id, $init_data): void {
        $this->hwid=$device_id;
        foreach ($init_data as $key=> $value) {
            $this->$key=$value;
        }
        if (is_null($this->api_key)) {
            return;
        }
        if (is_null($this->city_id)) {
            return;
        }
        $this->owm=new Api($this->api_key);
        $this->owm->setCityId($this->city_id);
    }

    public function update(): bool {
        if(is_null($this->owm)) {
            return false;
        }
        $weather=$this->owm->fetchCurrent();
        if (is_null($weather)) {
            return false;
        }
        if ($this->updated==$weather->dt) {
            return true;
        }
        $this->weather=$weather;
        $this->updated=$weather->dt;
        $storage=new DeviceStorage;
        $storage->set($this->hwid, $this);
        $url=Settings::get('url', 'http://127.0.0.1').'/api/events/';
        $events=['temperature'=>$weather->main->temp, 'humidity'=>$weather->main->humidity, 'pressure'=>round($weather->main->pressure*76000/101325, 2), 'wind_speed'=>$this->weather->wind->speed, 'wind_direction'=>$this->weather->wind->deg];
        file_get_contents($url, 0, stream_context_create([
            'http'=>[
                'method'=>'POST',
                'header'=>"Content-Type: application/json; charset=utf-8\r\n",
                'content' => json_encode(['hwid'=>$this->hwid, 'events'=>$events, 'ts'=>$weather->dt])
            ]
        ]));
        return true;
    }

    public function getTemperature() {
        return isset($this->weather->main->temp)?$this->weather->main->temp:null;
    }

    public function getTempFeelsLike() {
        return isset($this->weather->main->temp)?$this->weather->main->feels_like:null;
    }

    public function getHumidity() {
        return isset($this->weather->main->humidity)?$this->weather->main->humidity:null;
    }

    public function getPressure() {
        return isset($this->weather->main->pressure)?round($this->weather->main->pressure*76000/101325, 2):null;
    }

    public function getWindSpeed() {
        if (!isset($this->weather->wind->speed)) {
            return '-';
        }
        return $this->weather->wind->speed;
    }

    public function getWindDirection() {
        if (!isset($this->weather->wind->deg)) {
            return '-';
        }
        $deg=$this->weather->wind->deg;
        if ($deg<22) {
            return 'C';
        } elseif ($deg<68) {
            return 'СЗ';
        } elseif ($deg<112) {
            return 'З';
        } elseif ($deg<158) {
            return 'ЮЗ';
        } elseif ($deg<202) {
            return 'Ю';
        } elseif ($deg<248) {
            return 'ЮВ';
        } elseif ($deg<292) {
            return 'В';
        } elseif ($deg<338) {
            return 'СВ';
        } else {
            return 'С';
        }
    }

}
