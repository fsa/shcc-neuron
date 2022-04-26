<?php

namespace SmartHome\Capability;

interface ColorHsvInterface {

    function setHSV($hue, $sat, $value);

    function getHSV(): array;
}
