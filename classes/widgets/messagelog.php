<?php

namespace Widgets;

use httpResponse,
    Tts\Log;

class MessageLog {

    public static function show() {
        $log=Log::getLastMessages();
        httpResponse::showCard('Последние голосовые сообщения', join('<br>', $log));
    }

}
