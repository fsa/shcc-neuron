<?php

namespace SmartHome\Module\Tts;

use SmartHome\TtsInterface,
    Tts\Queue;

class Daemon implements \SmartHome\DaemonInterface {

    private $tts_provider;
    private $queue;
    private $last_message_time;
    private $pre_sound;
    private $pre_sound_period;
    private $play_sound_cmd;

    public function __construct($params) {
        if(isset($params['provider'])) {
            $class_name=$params['provider']->class;
            $this->tts_provider=new $class_name((array)$params['provider']->properties);
            if(!($this->tts_provider instanceof TtsInterface)) {
                $this->tts_provider=null;
            }
        }
        $this->pre_sound=$params['pre_sound'];
        $this->play_sound_cmd=$params['play_sound_cmd'];
    }

    public function getName() {
        return 'tts';
    }

    public function prepare() {
        $this->last_message_time=0;
        $this->queue=new Queue;
        $this->queue->getQueue();
        $this->queue->dropOldMessage();
    }

    public function iteration() {
        $message=$this->queue->receiveMessage();
        if($message) {
            $this->playVoice($message);
        } else {
            syslog(LOG_WARNING, __FILE__.':'.__LINE__.'TTS Queue: '.print_r($this->queue, true));
            sleep(30);
            $this->queue=new Queue();
        }
    }

    public function finish() {
        $this->queue->dropQueue();
    }

    private function playVoice($text) {
        if(is_null($this->tts_provider)) {
            return;
        }
        syslog(LOG_DEBUG, __FILE__.':'.__LINE__.'TTS Play Voice: '.$text);
        $voice_file=$this->tts_provider->getVoiceFile($text);
        if(time()-$this->last_message_time>$this->pre_sound_period) {
            $this->playMp3(__DIR__.'/../../../../custom/sound/'.$this->pre_sound);
        }
        $this->playMp3($voice_file);
        $this->last_message_time=time();        
    }

    private function playMp3(string $filename) {
        system(sprintf($this->play_sound_cmd,$filename));
    }

    public static function disable(): void {
        $tts=new Queue;
        $tts->dropQueue();
    }

}
