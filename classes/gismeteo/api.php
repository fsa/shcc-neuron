<?php

namespace Gismeteo;

class Api {

    const GISMETEO_URL='https://api.gismeteo.net/v2/';

    private $api_key;
    private $city_id;

    public function __construct($api_key) {
        $this->api_key=$api_key;
    }

    public function setCityId($id) {
        $this->city_id=$id;
    }

    public function fetchCurrent() {
        if (is_null($this->city_id)) {
            throw new AppException('Не указан ID населённого пункта!');
        }
        $url=self::GISMETEO_URL.'weather/current/'.$this->city_id.'/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Gismeteo-Token: '.$this->api_key]);
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

/*    public function fetchForecast() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::OWM_URL.'/forecast?'.http_build_query($this->getRequestArray()));
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
*/
    
    private function getRequestArray() {
        $result=[];
        if ($this->city_id) {
            $result['id']=$this->city_id;
        }
        return $result;
    }

}
