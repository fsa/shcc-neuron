<?php

namespace SmartHome\Device\Capability;

interface ColorHsvInterface {

    function setHSV($hue, $sat, $value);

    function getHSV(): array;
}
