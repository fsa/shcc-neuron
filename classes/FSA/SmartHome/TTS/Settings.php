<?php

namespace FSA\SmartHome\TTS;

use App;

class Settings {

    public static function setProvider(string $class_name, array $params) {
        App::setVarJson('TTS:Provider', ['class'=>$class_name, 'properties'=>$params]);
    }

    public static function getProvider() {
        return App::getVarJson('TTS:Provider');
    }

    public static function dropProvider() {
        App::dropVar('TTS:Provider');
    }

}
