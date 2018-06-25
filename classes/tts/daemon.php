<?php

namespace Tts;

class Daemon implements \SmartHome\Daemon {

    const PLAY_SOUND_CMD='mpg123 -q %s';
    const PRE_SOUND='notification.mp3';
    const PRE_SOUND_PERIOD=5;

    private $tts_provider;
    private $queue;
    private $last_message_time;
    private $pre_sound;
    private $play_sound_cmd;

    public function __construct($precess_url) {
        $tts=file_get_contents(__DIR__.'/../../config/tts.conf');
        $this->tts_provider=unserialize($tts);
        $settings=\Settings::get('tts');
        $this->pre_sound=isset($settings->pre_sound)?$settings->pre_sound:self::PRE_SOUND;
        $this->play_sound_cmd=isset($settings->play_sound_cmd)?$settings->play_sound_cmd:self::PLAY_SOUND_CMD;
    }

    public function getName() {
        return 'tts';
    }

    public function prepare() {
        $this->last_message_time=0;
        $this->queue=new Queue;
    }

    public function iteration() {
        $message=$this->queue->receiveMessage();
        if($message) {
            $this->playVoice($message);
        } else {
            error_log(print_r($this->queue,true));
            sleep(30);
            $this->queue=new Queue();
        }
    }

    public function finish() {
        
    }

    private function playVoice($text) {
        $filename=$this->tts_provider->getVoiceFile($text);
        if(time()-$this->last_message_time>self::PRE_SOUND_PERIOD) {
            $this->playMp3(__DIR__.'/../../sound/'.$this->pre_sound);
        }
        $this->playMp3($filename);
        $this->last_message_time=time();        
    }

    private function playMp3(string $filename) {
        system(sprintf($this->play_sound_cmd,$filename));
    }
}
