<?php

namespace FSA\SmartHome\Entity;

class Sensor
{
    const TABLE_NAME = 'sensors';
    public $id;
    public $uid;
    public $description;
    public $property;
    public $device_property;
    public $history;
}
