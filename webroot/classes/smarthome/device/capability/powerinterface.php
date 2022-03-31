<?php

namespace SmartHome\Device\Capability;

interface PowerInterface {

    function setPowerOn(int $line=0);

    function setPowerOff(int $line=0);

    function setPower(bool $value, int $line=0);
    
    function getPower(): bool;
}
