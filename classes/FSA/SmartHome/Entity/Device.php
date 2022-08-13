<?php

namespace FSA\SmartHome\Entity;

use FSA\Neuron\SQLEntityInterface;

class Device implements SQLEntityInterface
{
    const TABLE_NAME = 'devices';
    const UID = 'uid';
    public $uid;
    public $description;
    public $plugin;
    public $hwid;
    public $class;
    public $properties;

    public function __construct()
    {
        if(!is_null($this->properties)) {
            $this->properties = json_decode($this->properties, true);
        }
    }

    public function getProperties(): array
    {
        $properties = get_object_vars($this);
        $properties['properties'] = json_encode($this->properties, JSON_UNESCAPED_UNICODE);
        return $properties;
    }
}
