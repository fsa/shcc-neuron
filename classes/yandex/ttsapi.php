<?php

namespace Yandex;

use Settings;

/**
 * https://tts.voicetech.yandex.net/generate? 
  key=<API‑ключ>
  & text=<текст>
  & format=<mp3|wav|opus>
  & [quality=<hi|lo>]
  & lang=<ru-RU|en-US|uk-UK|tr-TR>
  & speaker=<jane|oksana|alyss|omazh|zahar|ermil>
  & [speed=<скорость речи>]
  & [emotion=<good|neutral|evil>]
 */
class TtsApi implements \Tts\TtsInterface {

    const YANDEX_TTS_API_URL='https://tts.voicetech.yandex.net/generate';
    const CHMOD=0750;

    private $params;

    public function __construct($api_key) {
        $this->params['key']=$api_key;
        $this->params['format']='mp3';
        $this->params['lang']='ru_RU';
        $this->params['speaker']='jane';
        $this->params['emotion']='neutral';
    }
    
    public function setSpeaker($name) {
        switch ($name) {
            case "jane":
            case "oksana":
            case "alyss":
            case "omazh":
            case "zahar":
            case "ermil":
                $this->params['speaker']=$name;
                break;
            default:
                throw new Exception('Неверно указано имя говорящего.');
        }
    }
    
    public function setEmotion($name) {
        switch ($name) {
            case "good":
            case "neutral":
            case "evil":
                $this->params['emotion']=$name;
                break;
            default:
                throw new Exception('Неверно указано имя эмоции.');
        }
    }

    public function getParams(): array {
        return $this->params;
    }

    public function getUrl($text) {
        $params=$this->params;
        $params['text']=$text;
        return self::YANDEX_TTS_API_URL.'?'.http_build_query($params);
    }

    public function requestApi($filename,$text) {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename).'/',self::CHMOD,true);
        }
        file_put_contents($filename,file_get_contents($this->getUrl($text)));
    }

    public function getVoiceFile($text) {
        $filename=Settings::get('cache_dir','/var/cache/neohome').'/yandex/'.$this->params['speaker'].'/'.$this->params['lang'].'/'.$this->params['emotion'].'/'.md5($text).'.'.$this->params['format'];
        if (!file_exists($filename)) {
            $this->requestApi($filename,$text);
        }
        $realpath=realpath($filename);
        if(!$realpath) {
            throw new Exception('Не найден файл для воспроизведения');
        }
        return $realpath;
    }

}
