<?php

/**
 * SHCC 0.7.0
 * 2020-12-24
 */

namespace SmartHome\Entity;

class Sensor extends \Entity {

    const TABLENAME='sensors';

    public $id;
    public $uid;
    public $description;
    public $property;
    public $device_property;
    public $history;

    protected function getColumnValues(): array {
        $values=get_object_vars($this);
        #TODO проверить существование таблицы с именем $values['history'];
        return $values;
    }

}
