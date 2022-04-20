<?php

namespace SmartHome;

use App;

class TtsQueue
{

    const NAME = 'shcc:tts_queue';
    const MAX_SIZE = 10;
    const LOG_NAME = 'shcc:messages';
    const LOG_SIZE = 10;

    public function __construct()
    {
    }

    public function dropQueue()
    {
        App::redis()->del(self::NAME);
    }

    public function addMessage($text)
    {
        App::redis()->lPush(self::NAME, json_encode(['ts' => time(), 'text' => $text]), JSON_UNESCAPED_UNICODE);
        if (App::redis()->lLen(self::NAME) > self::MAX_SIZE) {
            $msg = App::redis()->rPop(self::NAME);
            syslog(LOG_NOTICE, __FILE__ . ':' . __LINE__ . ' TTS Drop queue message: ' . $msg[1]);
        }
    }

    public static function addLogMessage($message)
    {
        $log=['message'=>$message, 'ts'=>time()];
        App::redis()->lPush(self::LOG_NAME, json_encode($log, JSON_UNESCAPED_UNICODE));
        if (App::redis()->lLen(self::LOG_NAME) > self::LOG_SIZE) {
            App::redis()->rPop(self::LOG_NAME);
        }
    }

    public static function getLogMessages()
    {
        return App::redis()->lRange(self::LOG_NAME, 0, -1);
    }
}
