<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-27
 */

namespace SmartHome;

use DB,
    PDO;

class Meters {

    private const UNITS=[
        'temperature'=>['Температура воздуха', '&deg;C'],
        'humidity'=>['Относительная влажность', '%'],
        'pressure'=>['Атмосферное давление', 'мм.рт.ст.'],
        'wind_speed'=>['Скорость ветра', 'м/с'],
        'wind_direction'=>['Направление ветра', '&deg']
    ];

    public static function getUnitName($property) {
        if (isset(self::UNITS[$property])) {
            return self::UNITS[$property][0];
        }
        return $property;
    }

    public static function getUnit($property) {
        if (isset(self::UNITS[$property])) {
            return self::UNITS[$property][1];
        }
        return '---';
    }

    public static function getUnits() {
        return self::UNITS;
    }

    public static function storeEvent($property, $value, $ts=null): bool {
        $s=DB::prepare('SELECT id FROM meters WHERE device_property=? AND history=true');
        $s->execute([$property]);
        $id=$s->fetch(PDO::FETCH_COLUMN);
        if (!$id) {
            return false;
        }
        if (is_null($ts)) {
            $s=DB::prepare('INSERT INTO meter_history (meter_id, value) VALUES (?, ?)');
            $s->execute([$id, $value]);
        } else {
            #TODO: проверить отсутствие записи с указанным ts
            $s=DB::prepare('INSERT INTO meter_history (meter_id, value, timestamp) VALUES (?, ?, ?)');
            $datetime=date('c', $ts);
            $s->execute([$id, $value, $datetime]);
        }
        return true;
    }

}
