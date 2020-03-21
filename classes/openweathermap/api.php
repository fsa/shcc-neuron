<?php

namespace OpenWeatherMap;

class Api {

    const OWM_URL='https://api.openweathermap.org/data/2.5/';
    const DEFAULT_LANG='ru';

    private $api_key;
    private $city_name;
    private $city_id;
    private $lat;
    private $lon;
    private $zip;
    private $lang=self::DEFAULT_LANG;

    public function __construct($api_key) {
        $this->api_key=$api_key;
    }

    public function setCityName($name) {
        $this->city_name=$name;
    }

    public function setCityId($id) {
        $this->city_id=$id;
    }

    public function setCoords($lat,$lon) {
        $this->lat=$lat;
        $this->lon=$lon;
    }

    public function setZip($zip) {
        $this->zip=$zip;
    }
    
    public function setLang($lang) {
        $this->lang=$lang;
    }

    public function fetchCurrent() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::OWM_URL.'weather?'.http_build_query($this->getRequestArray()));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $weather = curl_exec($ch);
        curl_close($ch);
        if($weather===false) {
            return null;
        }
        return json_decode($weather);
    }

    public function fetchForecast() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::OWM_URL.'forecast?'.http_build_query($this->getRequestArray()));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $weather = curl_exec($ch);
        curl_close($ch);
        if($weather===false) {
            return null;
        }
        if($weather===false) {
            return null;
        }
        return json_decode($weather);
    }

    private function getRequestArray() {
        $result=[];
        if ($this->city_id) {
            $result['id']=$this->city_id;
        }
        if ($this->city_name) {
            $result['q']=$this->city_name;
        }
        if ($this->lat) {
            $result['lat']=$this->lat;
            $result['lon']=$this->lon;
        }
        if ($this->zip) {
            $result['zip']=$this->zip;
        }
        $result['APPID']=$this->api_key;
        $result['units']='metric';
        $result['lang']='ru';
        return $result;
    }

}
