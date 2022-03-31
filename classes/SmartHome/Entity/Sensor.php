<?php

namespace SmartHome\Entity;

use FSA\Neuron\Entity;

class Sensor extends Entity {

    const TABLENAME='sensors';

    public $id;
    public $uid;
    public $description;
    public $property;
    public $device_property;
    public $history;

    protected function getColumnValues(): array {
        $values=get_object_vars($this);
        if(!$values['history']) {
            $values['history']=null;
        }
        #TODO проверить существование таблицы с именем $values['history'];
        return $values;
    }

}
