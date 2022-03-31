<?php

namespace Widgets;

use FSA\Neuron\HttpResponse;

class SystemState {

    public static function show() {
        HttpResponse::showCard('Состояние системы', '<span messages="state"></span>');
    }
}
