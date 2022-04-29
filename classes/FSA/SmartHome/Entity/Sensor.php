<?php

namespace FSA\SmartHome\Entity;

class Sensor
{
    const TABLENAME = 'sensors';
    public $id;
    public $uid;
    public $description;
    public $property;
    public $device_property;
    public $history;
}
