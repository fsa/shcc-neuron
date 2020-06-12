<?php

namespace Widgets;

use httpResponse;

class LampCT {
    public function show($title, $device_name) {
        $body=<<< EOS
<label for="{$device_name}_bright">Яркость</label>
<input type="range" class="custom-range action-integer" min="1" max="100" device_name="{$device_name}" device_action="bright" device_property="bright" id="{$device_name}_bright">
<label for="{$device_name}_ct">Цветовая температура</label>
<input type="range" class="custom-range action-integer" min="1700" max="6500" device_name="{$device_name}" device_action="ct" device_property="ct" for="{$device_name}_ct">
<!--button type="button" class="btn btn-light action-state" device_name="{$device_name}" id="{$device_name}_refresh">Обновить состояние</button-->
EOS;
        httpResponse::showCard('<div class="custom-control custom-switch">
<input type="checkbox" class="custom-control-input action-boolean" device_name="'.$device_name.'" device_action="power" device_property="power" id="'.$device_name.'_power">
<label class="custom-control-label" for="'.$device_name.'_power">'.$title.'</label>
</div>', $body, '<span device_name="'.$device_name.'" device_property="last_update"></span>');

    }
}