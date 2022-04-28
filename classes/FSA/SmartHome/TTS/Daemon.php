<?php

namespace FSA\SmartHome\TTS;

use FSA\SmartHome\DaemonInterface;
use SmartHome;

class Daemon implements DaemonInterface
{

    private $tts_provider;
    private $last_message_time;
    private $pre_sound;
    private $pre_sound_period;
    private $play_sound_cmd;
    private $tts;

    public function __construct($callback, $params)
    {
        if (isset($params['provider'])) {
            $class_name = $params['provider']->class;
            $this->tts_provider = new $class_name((array) $params['provider']->properties);
            #if (!($this->tts_provider instanceof ProviderInterface)) {
            #    $this->tts_provider = null;
            #}
        }
        $this->pre_sound = $params['pre_sound'];
        $this->pre_sound_period = $params['pre_sound_period'];
        $this->play_sound_cmd = $params['play_sound_cmd'];
        $this->tts = SmartHome::tts();
    }

    public function getName()
    {
        return 'TTS';
    }

    public function prepare()
    {
        $this->last_message_time = 0;
    }

    public function iteration()
    {
        $json = $this->tts->waitMessage();
        if (!$json) {
            return;
        }
        $msg = json_decode($json[1]);
        if (!isset($msg->ts) or !isset($msg->text)) {
            return;
        }
        if ($msg->ts + 60 < time()) {
            echo 'Dropped message "' . $msg->text . '" from ' . date('c', $msg->ts) . ', now ' . date('c') . PHP_EOL;
            return;
        }
        $this->playVoice($msg->text);
    }

    public function finish()
    {
    }

    private function playVoice($text)
    {
        if (is_null($this->tts_provider)) {
            return;
        }
        $voice_file = $this->tts_provider->getVoiceFile($text);
        if (is_null($voice_file)) {
            echo 'Voice_file is null' . PHP_EOL;
            return;
        }
        if (time() - $this->last_message_time > $this->pre_sound_period) {
            echo 'TTS Attention' . PHP_EOL;
            $this->playMp3(__DIR__ . '/../../../../custom/sound/' . $this->pre_sound);
        }
        echo 'TTS: ' . $text . PHP_EOL;
        $this->playMp3($voice_file);
        $this->last_message_time = time();
    }

    private function playMp3(string $filename)
    {
        system(sprintf($this->play_sound_cmd, $filename));
    }

    public static function disable(): void
    {
    }
}
