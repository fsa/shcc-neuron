<?php

class Sound {

    const COMMAND='mpg123';
    const SOUND='notification.mp3';
    #const SOUND='dingdong.mp3';

    private static $_instance;
    private $tts_provider;

    private function __construct() {
        $tts=file_get_contents(__DIR__.'/../config/tts.conf');
        $this->tts_provider=unserialize($tts);
        $this->playMp3(__DIR__.'/../sound/'.self::SOUND);
    }

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance=new self;
        }
        return self::$_instance;
    }

    public static function say($text) {
        self::getInstance()->playVoice($text);
    }
    
    private function playVoice($text) {
        $filename=$this->tts_provider->getVoiceFile($text);
        $this->playMp3($filename);
    }

    private function playMp3(string $filename) {
        system(self::COMMAND.' '.$filename);
    }

}
