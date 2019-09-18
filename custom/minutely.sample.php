<?php

require_once 'lib.php';
# Включение и выключение ночного режима
if ($time>='23:00' or $time<'10:00') {
    if (!$night) {
        setVar('System.NightMode',1);
        say('Перехожу в ночной режим');
    }
} else {
    if ($night) {
        setVar('System.NightMode',0);
        $mute=$security;
        say('Доброе утро');
        say('Ночной режим выключен');
    }
}
# Текущее время
if ($minute==0) {
    say('Новосибирское время '.$time);
}
# Получение текущего прогноза погоды и его озвучивание
if ($minute==0) {
    if (!$mute) {
        $owm=new OpenWeatherMap\Api(\Settings::get('openweathermap')->api_key);
        $owm->setCityId(\Settings::get('openweathermap')->city_id);
        $weather=$owm->fetchCurrent();
        if (!is_null($weather)) {
            say('Температура воздуха '.Tts\Tools::ruDegC($weather->main->temp));
        }
    }
}
# Голосовое сообщение в определённое время (C.A.T.S.)
if ($time=='18:55') {
    say('Заканчивается чемпионат в котиках.');
}

