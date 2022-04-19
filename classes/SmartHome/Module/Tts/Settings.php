<?php

namespace SmartHome\Module\Tts;

use SmartHome\Vars;

class Settings {

    public static function setProvider(string $class_name, array $params) {
        Vars::setJson('TTS:Provider', ['class'=>$class_name, 'properties'=>$params]);
    }

    public static function getProvider() {
        return Vars::getJson('TTS:Provider');
    }

    public static function dropProvider() {
        Vars::drop('TTS:Provider');
    }

}
