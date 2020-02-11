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
        return ['temperature'=>'Температура воздуха, &deg;C', 'humidity'=>'Относительная влажность, %', 'pressure'=>'Атмосферное давление, мм.рт.ст.'];
    }

    public function getDeviceDescription(): string {
        return 'Текущая погода с OpenWeaterMap.org';
    }

    public function getDeviceId(): string {
        return $this->uid;
    }

    public function getDeviceStatus(): string {
        return is_null($this->weather)?'Информация о погоде отсутствует':'Данные о погоде загружены';
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
        $actions=['temperature'=>$weather->main->temp, 'humidity'=>$weather->main->humidity, 'pressure'=>round($weather->main->pressure*76000/101325, 2)];
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

    public function getHumidity() {
        return isset($this->weather->main->humidity)?$this->weather->main->humidity:null;
    }

    public function getPressure() {
        return isset($this->weather->main->pressure)?round($this->weather->main->pressure*76000/101325, 2):null;
    }

}
