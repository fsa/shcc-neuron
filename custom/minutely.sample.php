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
# Закат, восход и солнце в зените
$sun=new SmartHome\SunInfo;
$sun_info=$sun->getText($time);
if($sun_info) {
    say($sun_info);
}

# Текущее время
if ($minute==0) {
    say('Текущее время '.$time);
}
# Получение текущего прогноза погоды и озвучивание температуры
# Требуется создание устройства openweathermap
#if ($minute%30==0) {
#    $owmd=getDevivce('openweathermap');
#    $owmyes=$owmd->update();
#}
#if ($minute==0) {
#    if ($owmyes) {
#        say('Температура воздуха '.Tts\Tools::ruDegC($owmd->weather->main->temp));
#    }
#}

# C.A.T.S.
#if ($time=='18:55') {
#    say('Заканчивается чемпионат в котиках');
#}
