<?php

namespace FSA\SmartHome\Capability;

interface ColorRgbInterface
{
    function setRGB(int $value, int $line = 0);
    function getRGB(int $line = 0): int;
}
