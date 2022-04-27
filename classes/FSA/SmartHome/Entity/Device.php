<?php

namespace FSA\SmartHome\Entity;

class Device
{
    const TABLE_NAME = 'devices';
    public $uid;
    public $description;
    public $plugin;
    public $hwid;
    public $class;
    public $properties;
}
