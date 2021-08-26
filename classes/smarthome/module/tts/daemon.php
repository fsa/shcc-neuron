<?php

namespace SmartHome\Module\Tts;

use SmartHome\TtsInterface,
    Tts\Queue,
    DBRedis;

class Daemon implements \SmartHome\DaemonInterface {

    private $tts_provider;
    private $last_message_time;
    private $pre_sound;
    private $pre_sound_period;
    private $play_sound_cmd;

    public function __construct($params) {
        if (isset($params['provider'])) {
            $class_name=$params['provider']->class;
            $this->tts_provider=new $class_name((array) $params['provider']->properties);
            if (!($this->tts_provider instanceof TtsInterface)) {
                $this->tts_provider=null;
            }
        }
        $this->pre_sound=$params['pre_sound'];
        $this->pre_sound_period=$params['pre_sound_pediod'];
        $this->play_sound_cmd=$params['play_sound_cmd'];
    }

    public function getName() {
        return 'tts';
    }

    public function prepare() {
        $this->last_message_time=0;
    }

    public function iteration() {
        $json=DBRedis::brPop(Queue::NAME, 30);
        if (!$json) {
            return;
        }
        $msg=json_decode($json[1]);
        if (!isset($msg->ts) or!isset($msg->text)) {
            return;
        }
        if ($msg->ts+60<time()) {
            syslog(LOG_NOTICE, __FILE__.':'.__LINE__.' Droped message "'.$msg->text.'" from '.date('c', $msg->ts).', now '.date('c'));
            return;
        }
        $this->playVoice($msg->text);
    }

    public function finish() {

    }

    private function playVoice($text) {
        if (is_null($this->tts_provider)) {
            return;
        }
        $voice_file=$this->tts_provider->getVoiceFile($text);
        if (time()-$this->last_message_time>$this->pre_sound_period) {
            $this->playMp3(__DIR__.'/../../../../custom/sound/'.$this->pre_sound);
            #syslog(LOG_INFO, 'TTS Attension');
        }
        $this->playMp3($voice_file);
        $this->last_message_time=time();
        #syslog(LOG_INFO, 'TTS said: '.$text);
    }

    private function playMp3(string $filename) {
        system(sprintf($this->play_sound_cmd, $filename));
    }

    public static function disable(): void {

    }

}
