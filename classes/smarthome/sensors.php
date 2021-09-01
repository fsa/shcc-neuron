<?php

namespace SmartHome;

use DB;

class Sensors {

    private const PROPERTIES=[
        'temperature'=>['Температура воздуха', '&deg;C'],
        'humidity'=>['Относительная влажность', '%'],
        'pressure'=>['Атмосферное давление', 'мм.рт.ст.'],
        'wind_speed'=>['Скорость ветра', 'м/с'],
        'wind_direction'=>['Направление ветра', '&deg'],
        'voltage'=>['Напряжение', 'В'],
        'motion'=>['Зафиксировано движение', null],
        'click'=>['Клик', null],
        'double_click'=>['Двойной клик', null],
        'long_press'=>['Длительное нажатие', null],
        'long_press_release'=>['Завершение долгого нажатия', null],
        'alarm'=>['Тревога', null],
        'string'=>['Произвольная строка',null]
    ];

    public static function getPropertyName($property) {
        if (isset(self::PROPERTIES[$property])) {
            return self::PROPERTIES[$property][0];
        }
        return $property;
    }

    public static function getPropertyUnit($property) {
        if (isset(self::PROPERTIES[$property])) {
            return self::PROPERTIES[$property][1];
        }
        return '---';
    }

    public static function getProperties() {
        return self::PROPERTIES;
    }

    public static function storeEvent($device_uid, $property, $value, $ts=null): bool {
        $s=DB::prepare('SELECT id, uid, history FROM sensors WHERE device_property=?');
        $s->execute([$device_uid.'@'.$property]);
        $sensor=$s->fetchObject();
        if (!$sensor) {
            return false;
        }
        SensorStorage::set($sensor->uid, $value, $ts);
        if (is_null($sensor->history)) {
            return false;
        }
        if (is_null($ts)) {
            $ts=time();
        }
        if(is_bool($value)) {
            $value=intval($value);
        }
        #TODO: проверить отсутствие записи с указанным ts
        $s=DB::prepare('INSERT INTO '.$sensor->history.' (sensor_id, value, timestamp) VALUES (?, ?, ?)');
        $datetime=date('c', $ts);
        $s->execute([$sensor->id, $value, $datetime]);
        return true;
    }

    public static function getHistory(string $uid, $from=null, $to=null) {
        $s=DB::prepare('SELECT * FROM sensors WHERE uid=?');
        $s->execute([$uid]);
        $sensor=$s->fetchObject();
        if (!$sensor or is_null($sensor->history)) {
            return [];
        }
        $params=["sensor_id"=>$sensor->id];
        if ($from) {
            $params['from']=date('c', $from);
            if ($to) {
                $period=' AND timestamp BETWEEN :from AND :to';
                $params['to']=date('c', $to);
            } else {
                $period=' AND timestamp>=:from';
            }
        } else {
            $period='';
        }
        $stmt=DB::prepare('SELECT ROUND(EXTRACT(EPOCH FROM timestamp)*1000) AS ts,value FROM '.$sensor->history.' WHERE sensor_id=:sensor_id'.$period.' ORDER BY timestamp');
        $stmt->execute($params);
        return ['name'=>$sensor->description, 'unit'=>self::PROPERTIES[$sensor->property][1], 'data'=>$stmt->fetchAll(\PDO::FETCH_NUM)];
    }

    public static function getHistoryJson(string $uid, $from=null, $to=null) {
        $s=DB::prepare('SELECT * FROM sensors WHERE uid=?');
        $s->execute([$uid]);
        $sensor=$s->fetchObject();
        if (!$sensor or is_null($sensor->history)) {
            return [];
        }
        $params=["sensor_id"=>$sensor->id];
        if ($from) {
            $params['from']=date('c', $from);
            if ($to) {
                $period=' AND timestamp BETWEEN :from AND :to';
                $params['to']=date('c', $to);
            } else {
                $period=' AND timestamp>=:from';
            }
        } else {
            $period='';
        }
        $stmt=DB::prepare('SELECT json_build_object(\'name\', \''.$sensor->description.'\',\'unit\', \''.self::PROPERTIES[$sensor->property][1].'\',\'data\', (SELECT array_agg(array[ts, value]) FROM (SELECT ROUND(EXTRACT(EPOCH FROM timestamp)*1000) AS ts, value FROM '.$sensor->history.' WHERE sensor_id=:sensor_id'.$period.' ORDER BY timestamp) AS data))');
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public static function getAll() {
        $s=DB::query('SELECT * FROM sensors ORDER BY uid');
        $s->setFetchMode(\PDO::FETCH_CLASS, Entity\Sensor::class);
        return $s;
    }

}
