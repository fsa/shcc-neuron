<?php

namespace SmartHome;

class IndicatorHistory {

    /**
     * Сохраняет данные в историю датчиков контроля
     * @param type $sensors массив сенсоров устройства для сохранения в памяти
     * @param type $data ассоциативный массив имя_сенсора->значение
     */
    public static function addRecords($sensors,$data) {
        $stmt=\DB::prepare('INSERT INTO indicator_history (indicator_id,place_id,value) VALUES (?,?,?)');
        foreach ($sensors as $sensor) {
            if (isset($data->{$sensor->property})) {
                $stmt->execute([$sensor->id,$sensor->place_id, $data->{$sensor->property}?'true':'false']);
            }
        }
        $stmt->closeCursor();
    }

}
