<?php

namespace FSA\SmartHome;

class Sensor
{
    private const PROPERTIES = [
        'temperature' => ['Температура воздуха', '&deg;C'],
        'humidity' => ['Относительная влажность', '%'],
        'pressure' => ['Атмосферное давление', 'мм.рт.ст.'],
        'wind_speed' => ['Скорость ветра', 'м/с'],
        'wind_direction' => ['Направление ветра', '&deg'],
        'voltage' => ['Напряжение', 'В'],
        'motion' => ['Зафиксировано движение', null],
        'click' => ['Клик', null],
        'double_click' => ['Двойной клик', null],
        'long_press' => ['Длительное нажатие', null],
        'long_press_release' => ['Завершение долгого нажатия', null],
        'alarm' => ['Тревога', null],
        'string' => ['Произвольная строка', null]
    ];

    public static function getPropertyName($property)
    {
        if (isset(self::PROPERTIES[$property])) {
            return self::PROPERTIES[$property][0];
        }
        return $property;
    }

    public static function getPropertyUnit($property)
    {
        if (isset(self::PROPERTIES[$property])) {
            return self::PROPERTIES[$property][1];
        }
        return '---';
    }

    public static function getProperties()
    {
        return self::PROPERTIES;
    }
}
