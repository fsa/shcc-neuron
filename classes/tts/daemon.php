<?php

namespace Tts;

class Daemon implements \SmartHome\DaemonInterface {

    const PLAY_SOUND_CMD='mpg123 -q %s';
    const PRE_SOUND='notification.mp3';
    const PRE_SOUND_PERIOD=5;

    private $tts_provider;
    private $queue;
    private $last_message_time;
    private $pre_sound;
    private $play_sound_cmd;

    public function __construct($process_url) {
        $tts_config_file=__DIR__.'/../../config/tts.conf';
        if(file_exists($tts_config_file)) {
            $tts=file_get_contents($tts_config_file);
            if($tts!==false) {
                $this->tts_provider=unserialize($tts);
            }
            if(!($this->tts_provider instanceof TtsInterface)) {
                $this->tts_provider=null;
            }
        }
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
        $this->queue->dropOldMessage();
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
        if(is_null($this->tts_provider)) {
            return;
        }
        $voice_file=$this->tts_provider->getVoiceFile($text);
        if(time()-$this->last_message_time>self::PRE_SOUND_PERIOD) {
            $this->playMp3(__DIR__.'/../../sound/'.$this->pre_sound);
        }
        $this->playMp3($voice_file);
        $this->last_message_time=time();        
    }

    private function playMp3(string $filename) {
        system(sprintf($this->play_sound_cmd,$filename));
    }
}
