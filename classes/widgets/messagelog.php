<?php

namespace Widgets;

use HTML;

class MessageLog {

    public static function show() {
        $log=\Tts\Log::getLastMessages();
        HTML::showCard('Последние голосовые сообщения', join('<br>', $log));
    }

}
