<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-28
 */

namespace SmartHome;

use DB,
    PDO;

class Indicators {

    private const STATES=[
        'motion'=>'Зафиксировано движение',
        'click'=>'Клик',
        'double_click'=>'Двойной клик',
        'long_press'=>'Длительное нажатие',
        'long_press_release'=>'Завершение долгого нажатия'
    ];

    public static function getStateName($property) {
        if (isset(self::STATES[$property])) {
            return self::STATES[$property];
        }
        return $property;
    }

    public static function getStates() {
        return self::STATES;
    }

    public static function storeEvent($property, $value, $ts=null): bool {
        $s=DB::prepare('SELECT id, uid, history FROM indicators WHERE device_property=?');
        $s->execute([$property]);
        $row=$s->fetch(PDO::FETCH_COLUMN);
        if (!$row) {
            return false;
        }
        $mem=new MemoryStorage;
        $mem->setSensor($row->uid, $value, $ts);
        if ($row->history!='t') {
            return false;
        }
        if (is_null($ts)) {
            $s=DB::prepare('INSERT INTO indicator_history (indicator_id, value) VALUES (?, ?)');
            $s->execute([$id, $value]);
        } else {
            #TODO: проверить отсутствие записи с указанным ts
            $s=DB::prepare('INSERT INTO indicator_history (indicator_id, value, timestamp) VALUES (?, ?, ?)');
            $datetime=date('c', $ts);
            $s->execute([$id, $value, $datetime]);
        }
        return true;
    }

}
