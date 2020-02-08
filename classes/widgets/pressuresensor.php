<?php

namespace Widgets;

use HTML,
    SmartHome\Devices;

class PressureSensor {

    public static function show($sensor_name) {
        $sensor=Devices::get($sensor_name);
        HTML::showCard('Атмосферное давление', round($sensor->getPressure()).' мм.рт.ст.');
    }

}
