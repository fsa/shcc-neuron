<?php

/**
 * SHCC 0.7.0
 * 2020-12-24
 */

namespace SmartHome\Entity;

class Meter extends \Entity {

    const TABLENAME='meters';

    public $id;
    public $uid;
    public $place_id;
    public $description;
    public $unit;
    public $device_property;
    public $history;

    protected function getColumnValues(): array {
        $values=get_object_vars($this);
        $values['history']=$this->history?'true':'false';
        return $values;
    }

}
