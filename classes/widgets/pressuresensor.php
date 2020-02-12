<?php

namespace Widgets;

use HTML;

class PressureSensor {

    public static function show($sensor) {
        HTML::showCard('Атмосферное давление', '<span style="font-size: 2rem;">'.round($sensor->getPressure()).' мм.рт.ст.</span>');
    }

}
