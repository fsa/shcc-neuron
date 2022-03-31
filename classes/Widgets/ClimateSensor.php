<?php

namespace Widgets;

use FSA\Neuron\HttpResponse;

class ClimateSensor {
    public function show($title, $device_name) {
        $body=<<< EOS
<span style="font-size: 2rem;" class="device-state-off" device_name="{$device_name}"><span device_name="{$device_name}" device_property="temperature">-</span>&deg;C, <span device_name="{$device_name}" device_property="humidity">-</span>%</span>
EOS;
        HttpResponse::showCard($title, $body, '<span device_name="'.$device_name.'" device_property="last_update"></span>');

    }
}