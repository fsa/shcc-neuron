<?php

namespace Widgets;

use HTML;

class Weather {

    public static function show($sensor) {
        HTML::showCard($sensor->place_name, sprintf('<span style="font-size: 2rem;">%d &deg;C (%d&deg;C), %d%%, %d м/с (%s)</span>', $sensor->getTemperature(), $sensor->getTempFeelsLike(), $sensor->getHumidity(), $sensor->getWindSpeed(), $sensor->getWindDirection()), date('d.m.Y H:i:s', $sensor->getLastUpdate()));
    }

}
