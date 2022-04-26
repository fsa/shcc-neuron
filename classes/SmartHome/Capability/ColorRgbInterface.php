<?php

namespace SmartHome\Capability;

interface ColorRgbInterface {

    function setRGB(int $value);

    function getRGB(): int;
}
