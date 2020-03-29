<?php

namespace SmartHome;

use Settings;

class SunInfo {

    private $sun_info;

    public function __construct() {
        $home=Settings::get('home');
        $this->sun_info=date_sun_info(time(),$home['lat'],$home['lon']);
    }

    public function getText(string $time): string {
        foreach ($this->sun_info as $name=> $value) {
            if ($value and $time==date('H:i',$value)) {
                switch ($name) {
                    case "sunrise":
                        return "Восход солнца";
                    case "sunset":
                        return "Закат солнца";
                    case "transit":
                        return "Солнце в зените";
                }
            }
        }
        return false;
    }
    
    public function getSunset() {
        return $this->sun_info['sunset'];
    }

    public function getSunrise() {
        return $this->sun_info['sunrise'];
    }
}
