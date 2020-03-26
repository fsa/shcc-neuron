<?php

namespace Widgets;

use httpResponse;

class PressureSensor {

    public static function show($sensor) {
        httpResponse::showCard('Атмосферное давление', '<span style="font-size: 2rem;">'.round($sensor->getPressure()).' мм.рт.ст.</span>');
    }

}
