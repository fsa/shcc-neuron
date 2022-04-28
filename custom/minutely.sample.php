<?php

#use SmartHome\Devices;

# Имя устройства с данными о погоде
#const WEATHER_DEVICE = 'openweather_city';

# Географические координаты для вычисления времени восходе и закате солнца
const LAT = 55.02316;
const LON = 82.94132;

require_once 'lib.php';
# Включение и выключение ночного режима
if ($time >= '23:00' or $time < '10:00') {
    if (!$night) {
        setVar('System:NightMode', 1);
        say('Перехожу в ночной режим');
        SmartHome::tts()->mute();
    }
} else {
    if ($night) {
        setVar('System:NightMode', 0);
        if (!$security) {
            SmartHome::tts()->unmute();
        }
        if ($hour < 12) {
            say('Доброе утро');
        }
        say('Ночной режим выключен');
    }
}
# Закат, восход и солнце в зените
$sun = new SmartHome\SunInfo(LAT, LON);
$sun_info = $sun->getText($time);
if ($sun_info) {
    say($sun_info);
}
# Текущее время
if ($minute == 0) {
    say('Текущее время ' . $time);
}
# Озвучивание погоды
/*
if ($minute == 0) {
    $owm = getDevice(WEATHER_DEVICE);
    $weather = $owm->getWeather();
    # Озвучивать погоду, если данные не старше 15 минут (900 секунд)
    if ($weather and time() - $weather->dt < 900) {
        say('Температура воздуха ' . LangTools::ruDegC($weather->main->temp));
    }
}
*/
# Получение текущего прогноза погоды
/*
if (($minute + 1) % 30 == 0) {
    $owm = getDevice(WEATHER_DEVICE);
    if ($owm->update()) {
        Devices::processEvents($owm->getHwid(), $owm->getEvents(), $owm->getWeather()->dt);
        setDevice(WEATHER_DEVICE, $owm);
    }
}
*/

# C.A.T.S.
#if ($time=='18:55') {
#    say('Заканчивается чемпионат в котиках');
#}
