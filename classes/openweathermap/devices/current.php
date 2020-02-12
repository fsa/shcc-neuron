<?php

namespace OpenWeatherMap\Devices;

use Settings,
    OpenWeatherMap\Api,
    AppException,
    SmartHome\Device\MemoryStorage;

class Current implements \SmartHome\DeviceInterface, \SmartHome\SensorsInterface {

    public $weather;

    /** @var OpenWeatherMap\Api * */
    private $owm;
    private $uid;
    private $api_key;
    private $city_id;
    private $updated;

    public function __construct() {
        $this->updated=0;
    }

    public function getDeviceIndicators(): array {
        return [];
    }

    public function getDeviceMeters(): array {
        return ['temperature'=>'Температура воздуха, &deg;C', 'humidity'=>'Относительная влажность, %', 'pressure'=>'Атмосферное давление, мм.рт.ст.', 'wind_speed'=>'Скорость ветра, м/с', 'wind_direction'=>'Направление ветра, &deg;'];
    }

    public function getDeviceDescription(): string {
        return 'Текущая погода с OpenWeaterMap.org';
    }

    public function getDeviceId(): string {
        return $this->uid;
    }

    public function getDeviceStatus(): string {
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

    public function getModuleName(): string {
        return 'openweathermap';
    }

    public function init($device_id, $init_data): void {
        $this->uid=$device_id;
        foreach ($init_data as $key=> $value) {
            $this->$key=$value;
        }
        $owm_settings=Settings::get('openweathermap');
        if (is_null($this->api_key)) {
            throw new AppException('Не указан ключ API');
        }
        if (is_null($this->city_id)) {
            throw new AppException('Не указан ID города.');
        }
        $this->owm=new Api($this->api_key);
        $this->owm->setCityId($this->city_id);
    }

    public function update(): bool {
        $weather=$this->owm->fetchCurrent();
        if (is_null($weather)) {
            return false;
        }
        if ($this->updated==$weather->dt) {
            return true;
        }
        $this->weather=$weather;
        $this->updated=$weather->dt;
        $url=Settings::get('url').'/action/';
        $actions=['temperature'=>$weather->main->temp, 'humidity'=>$weather->main->humidity, 'pressure'=>round($weather->main->pressure*76000/101325, 2), 'wind_speed'=>$this->weather->wind->speed, 'wind_direction'=>$this->weather->wind->deg];
        $data=['module'=>$this->getModuleName(), 'uid'=>$this->uid, 'data'=>json_encode($actions), 'ts'=>$weather->dt];
        file_get_contents($url.'?'.http_build_query($data));
        $storage=new MemoryStorage;
        $storage->lockMemory();
        $storage->setDevice($this->uid, $this);
        $storage->releaseMemory();
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
