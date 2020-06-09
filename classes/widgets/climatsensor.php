<?php

namespace Widgets;

use httpResponse;

class ClimatSensor {
    public function show($title, $device_name) {
        $body=<<< EOS
<span style="font-size: 2rem;" class="device-state-off" device_name="{$device_name}"><span device_name="{$device_name}" id="{$device_name}_temperature">-</span>&deg;C, <span device_name="{$device_name}" id="{$device_name}_humidity">-</span>%</span>
<br>
<button type="button" class="btn btn-light device-state" device_name="{$device_name}" id="{$device_name}_refresh">Обновить состояние</button>
EOS;
        httpResponse::showCard($title, $body, '<span id="'.$device_name.'_state"></span>');

    }
}