<?php

namespace SmartHome\Device\Capability;

interface ModeInterface {

    /**
     * 
     * @param string $value heat, cool, auto, eco, dry, fan_only
     */
    function setThermostat(string $value);

    /**
     * 
     * @param string $value auto, low, medium, high
     */
    function setFanSpeed(string $value);
}
