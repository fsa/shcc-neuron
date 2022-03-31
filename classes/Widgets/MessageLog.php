<?php

namespace Widgets;

use FSA\Neuron\HttpResponse;

class MessageLog {

    public static function show() {
        HttpResponse::showCard('Последние голосовые сообщения', '<span messages="tts"></span>');
    }

}
