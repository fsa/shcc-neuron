<?php

namespace Widgets;

use HTML;

class Weather {

    public static function show($sensor) {
        HTML::showCard($sensor->place_name, sprintf('<span style="font-size: 2rem;"><span class="text-nowrap">%d (%d)&deg;C</span>, %d%%, <span class="text-nowrap">%d м/с (%s)</span></span>', $sensor->getTemperature(), $sensor->getTempFeelsLike(), $sensor->getHumidity(), $sensor->getWindSpeed(), $sensor->getWindDirection()), date('d.m.Y H:i:s', $sensor->getLastUpdate()));
    }

}
