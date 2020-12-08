<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-28
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
        'wind_direction'=>['Направление ветра', '&deg'],
        'voltage'=>['Напряжение', 'В']
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
        $s=DB::prepare('SELECT id, uid, history FROM meters WHERE device_property=?');
        $s->execute([$property]);
        $row=$s->fetch(PDO::FETCH_OBJ);
        if (!$row) {
            return false;
        }
        $mem=new MemoryStorage;
        $mem->setSensor($row->uid, $value, $ts);
        if ($row->history!='t') {
            return false;
        }
        if (is_null($ts)) {
            $s=DB::prepare('INSERT INTO meter_history (meter_id, value) VALUES (?, ?)');
            $s->execute([$row->id, $value]);
        } else {
            #TODO: проверить отсутствие записи с указанным ts
            $s=DB::prepare('INSERT INTO meter_history (meter_id, value, timestamp) VALUES (?, ?, ?)');
            $datetime=date('c', $ts);
            $s->execute([$row->id, $value, $datetime]);
        }
        return true;
    }

    public static function getMeters() {
        $s=DB::query('SELECT * FROM meters ORDER BY uid');
        $s->setFetchMode(PDO::FETCH_CLASS, Entity\Meter::class);
        return $s;
    }
}
