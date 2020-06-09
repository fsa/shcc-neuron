<?php

namespace Widgets;

use httpResponse;

class LampCT {
    public function show($title, $device_name) {
        $body=<<< EOS
<label for="{$device_name}_bright">Яркость</label>
<input type="range" class="custom-range device-bright" min="1" max="100" device_name="{$device_name}" device_action="bright" id="{$device_name}_bright">
<label for="{$device_name}_bright">Цветовая температура</label>
<input type="range" class="custom-range device-ct" min="1700" max="6500" device_name="{$device_name}" device_action="ct" id="{$device_name}_ct">
<button type="button" class="btn btn-light device-state" device_name="{$device_name}" id="{$device_name}_refresh">Обновить состояние</button>
EOS;
        httpResponse::showCard('<div class="custom-control custom-switch">
<input type="checkbox" class="custom-control-input device-power" device_name="'.$device_name.'" device_action="power" id="'.$device_name.'_power">
<label class="custom-control-label" for="'.$device_name.'_power">'.$title.'</label>
</div>', $body, '<span id="'.$device_name.'_state"></span>');

    }
}