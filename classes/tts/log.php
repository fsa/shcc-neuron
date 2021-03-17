<?php

/**
 * SHCC 0.7.0
 * 2020-11-28
 */

namespace Tts;

class Log {

    const PROJ='f';
    const CHMOD=0600;
    const MEMSIZE=2048;
    const LOGSIZE=10;

    private static $_instance=null;
    private $shm;

    public static function newMessage($message) {
        self::getInstance()->addMessage($message);
    }

    public static function getLastMessages($num=10) {
        return self::getInstance()->getMessages();
    }

    public static function getInstance(): self {
        if (self::$_instance===null) {
            self::$_instance=new self;
        }
        return self::$_instance;
    }

    public function __construct() {
        $file=ftok(__FILE__, self::PROJ);
        $this->shm=shm_attach($file, self::MEMSIZE, self::CHMOD);
        if ($this->shm===false) {
            throw new \AppException('Не удалось инициализировать лог TTS сообщений.');
        }
        if (shm_has_var($this->shm, 0)) {
            return;
        }
        $log=[];
        if (!shm_put_var($this->shm, 0, $log)) {
            throw new \AppException('Не удалось добавить сообщение лог TTS.');
        }
    }

    public function addMessage($message) {
        $log=shm_get_var($this->shm, 0);
        $log[]=date('H:i:s').' '.$message;
        if (count($log)>self::LOGSIZE) {
            array_shift($log);
        }
        shm_put_var($this->shm, 0, $log);
    }

    public function getMessages(): array {
        return shm_get_var($this->shm, 0);
    }

}
