<?php

namespace Widgets;

use httpResponse;

class LampCT {
    public function show($lamp_name) {
        $test=<<< EOS
<label for="{$lamp_name}_bright">Яркость</label>
<input type="range" class="custom-range device-bright" min="1" max="100" device_name="{$lamp_name}" id="{$lamp_name}_bright">
<label for="{$lamp_name}_bright">Цветовая температура</label>
<input type="range" class="custom-range device-ct" min="1700" max="6500" device_name="{$lamp_name}" id="{$lamp_name}_ct">
<button type="button" class="btn btn-light device-state" device_name="{$lamp_name}" id="{$lamp_name}_refresh">Обновить состояние</button>
EOS;
        httpResponse::showCard('<div class="custom-control custom-switch">
<input type="checkbox" class="custom-control-input device-power" device_name="'.$lamp_name.'" id="'.$lamp_name.'_power">
<label class="custom-control-label" for="'.$lamp_name.'_power">Светильник</label>
</div>', $test, '<span id="'.$lamp_name.'_state">Нет данных</span>');

    }
}