<?php

namespace Widgets;

use httpResponse;

class ClimatSensor {
    public function show($title, $device_name) {
        $body=<<< EOS
<span style="font-size: 2rem;" class="device-state-off" device_name="{$device_name}"><span device_name="{$device_name}" device_property="temperature">-</span>&deg;C, <span device_name="{$device_name}" device_property="humidity">-</span>%</span>
EOS;
        httpResponse::showCard($title, $body, '<span device_name="'.$device_name.'" device_property="last_update"></span> <span id="'.$device_name.'_state"></span>');

    }
}