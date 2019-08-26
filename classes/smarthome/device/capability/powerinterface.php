<?php

namespace SmartHome\Device\Capability;

interface PowerInterface {

    function setPowerOn();

    function setPowerOff();

    function setPower(bool $value);
}
