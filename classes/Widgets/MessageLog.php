<?php

namespace Widgets;

use App;

class MessageLog {

    public static function show() {
        App::response()->showCard('Последние голосовые сообщения', '<span messages="tts"></span>');
    }

}
