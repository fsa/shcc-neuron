<?php

namespace Widgets;

use httpResponse;

class LampCT {
    public function show($name, $description) {
        $body=<<< EOS
<label for="{$name}_bright">Яркость</label>
<input type="range" class="custom-range device-bright" min="1" max="100" device_name="{$name}" device_action="bright" id="{$name}_bright">
<label for="{$name}_bright">Цветовая температура</label>
<input type="range" class="custom-range device-ct" min="1700" max="6500" device_name="{$name}" device_action="ct" id="{$name}_ct">
<button type="button" class="btn btn-light device-state" device_name="{$name}" id="{$name}_refresh">Обновить состояние</button>
EOS;
        httpResponse::showCard('<div class="custom-control custom-switch">
<input type="checkbox" class="custom-control-input device-power" device_name="'.$name.'" device_action="power" id="'.$name.'_power">
<label class="custom-control-label" for="'.$name.'_power">'.$description.'</label>
</div>', $body, '<span id="'.$name.'_state"></span>');

    }
}