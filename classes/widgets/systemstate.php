<?php

namespace Widgets;

use httpResponse;

class SystemState {

    public static function show() {
        httpResponse::showCard('Состояние системы', '<span id="system_state"></span>');
    }
}
