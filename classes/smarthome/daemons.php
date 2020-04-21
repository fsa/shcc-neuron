<?php

namespace SmartHome;

class Daemons {

    public static function getActive() {
        #TODO: получение данных об активных демонах
        return [
             'xiaomi'=>'Xiaomi\Daemon',
             'yeelight'=>'Yeelight\Daemon',
             'miio'=>'miIO\Daemon',
             'tts'=>'Tts\Daemon'
        ];
    }

}