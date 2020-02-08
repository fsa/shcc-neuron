<?php

namespace Widgets;

use HTML;

class MessageLog {

    public static function show() {
        $log=\Tts\Log::getLastMessages();
        $log_message=[];
        foreach ($log AS $row) {
            $log_message[]=sprintf('%s %s', date('H:i:s', strtotime($row->timestamp)), $row->text);
        }
        HTML::showCard('Последние голосовые сообщения', join('<br>', $log_message));
    }

}
