<?php

namespace FSA\SmartHome\Entity;

use FSA\Neuron\SQLEntityInterface;

class Sensor implements SQLEntityInterface
{
    const TABLE_NAME = 'sensors';
    public $id;
    public $uid;
    public $description;
    public $property;
    public $device_property;
    public $history;

    public function getProperties(): array
    {
        return get_object_vars($this);
    }
}
