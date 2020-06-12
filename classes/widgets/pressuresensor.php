<?php

namespace Widgets;

use httpResponse;

class PressureSensor {

    public static function show($title, $device_name) {
        httpResponse::showCard($title, '<span style="font-size: 2rem;"><span device_name="'.$device_name.'" device_property="pressure">-</span> мм.рт.ст.</span>');
    }

}
