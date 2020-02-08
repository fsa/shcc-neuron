<?php

namespace Widgets;

use HTML,
    SmartHome\Devices;

class TemperatureHumiditySensor {

    public static function show($sensor_name) {
        $sensor=Devices::get($sensor_name);
        HTML::showCard('Кухня', '<span style="font-size: 3rem;">'.round($sensor->getTemperature(), 1).' &deg;C, '.round($sensor->getHumidity()).'%', date('d.m.Y H:i:s', $sensor->getLastUpdate()).'</span>');
    }

}
