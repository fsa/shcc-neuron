<?php

namespace Widgets;

use httpResponse;

class TemperatureHumiditySensor {

    public static function show($sensor) {
        httpResponse::showCard($sensor->place_name, '<span style="font-size: 2rem;">'.round($sensor->getTemperature(), 1).'&deg;C, '.round($sensor->getHumidity()).'%'.'</span>', date('d.m.Y H:i:s', $sensor->getLastUpdate()));
    }

}
