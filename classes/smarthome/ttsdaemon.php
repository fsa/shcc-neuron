<?php

namespace SmartHome;

class TtsDaemon implements Daemon {

    const COMMAND='mpg123 -q';
    const PRE_SOUND='notification.mp3';
    const PRE_SOUND_PERIOD=5;

    private $tts_provider;
    private $queue;
    private $last_message_time;

    public function __construct($precess_url) {
        $this->queue=new TtsQueue;
        $tts=file_get_contents(__DIR__.'/../../config/tts.conf');
        $this->tts_provider=unserialize($tts);
    }

    public function getName() {
        return 'tts';
    }

    public function iteration() {
        $message=$this->queue->receiveMessage();
        $this->playVoice($message);
    }

    public function finish() {
        
    }

    public function prepare() {
        $this->last_message_time=0;
    }

    private function playVoice($text) {
        $filename=$this->tts_provider->getVoiceFile($text);
        if(time()-$this->last_message_time>self::PRE_SOUND_PERIOD) {
            $this->playMp3(__DIR__.'/../../sound/'.self::PRE_SOUND);
        }
        $this->playMp3($filename);
        $this->last_message_time=time();        
    }

    private function playMp3(string $filename) {
        system(self::COMMAND.' '.$filename);
    }
}
