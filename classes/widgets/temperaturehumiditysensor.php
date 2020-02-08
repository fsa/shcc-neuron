<?php

namespace Widgets;

use HTML,
    SmartHome\Devices;

class TemperatureHumiditySensor {

    public static function show($sensor_name) {
        $sensor=Devices::get($sensor_name);
        HTML::showCard('Кухня', 'Температура: '.round($sensor->getTemperature(), 1).' &deg;C<br>Влажность: '.round($sensor->getHumidity()).'%', date('d.m.Y H:i:s', $sensor->getLastUpdate()));
    }

}
