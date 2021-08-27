<?php

namespace Tts;

use DBRedis;

class Queue {

    const NAME='shcc:tts_queue';
    const MAX_SIZE=10;
    const LOG_NAME='shcc:messages';
    const LOG_SIZE=10;

    public function __construct() {
    }

    public function dropQueue() {
        DBRedis::del(self::NAME);
    }

    public function addMessage($text) {
        DBRedis::lPush(self::NAME, json_encode(['ts'=>time(), 'text'=>$text]));
        if(DBRedis::lLen(self::NAME)>self::MAX_SIZE) {
            $msg=DBRedis::rPop(self::NAME);
            syslog(LOG_NOTICE, __FILE__.':'.__LINE__.' TTS Drop queue message: '.$msg[1]);
        }
    }

    public static function addLogMessage($message) {
        DBRedis::lPush(self::LOG_NAME, date('H:i').' '.$message);
        if (DBREdis::lLen(self::LOG_NAME)>self::LOG_SIZE) {
            DBRedis::rPop(self::LOG_NAME);
        }
    }

    public static function getLogMessages() {
        return DBRedis::lRange(self::LOG_NAME, 0, -1);
    }
}
