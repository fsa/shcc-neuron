<?php

namespace Widgets;

use App;

class SystemState {

    public static function show() {
        App::response()->showCard('Состояние системы', '<span messages="state"></span>');
    }
}
