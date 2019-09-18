<?php

namespace Tts;

use DB,
    PDO;

class Log {

    public static function newMessage($message) {
        $s=DB::prepare('INSERT INTO tts_log (text) VALUES (?)');
        $s->execute([$message]);
    }
    
    public static function getLastMessages($num=10) {
        $s=DB::prepare('SELECT * FROM tts_log ORDER BY timestamp DESC LIMIT ?');
        $s->execute([$num]);
        return $s->fetchAll(PDO::FETCH_OBJ);
    }

}
