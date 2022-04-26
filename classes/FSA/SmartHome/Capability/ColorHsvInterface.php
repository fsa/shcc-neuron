<?php

namespace FSA\SmartHome\Capability;

interface ColorHsvInterface
{
    function setHSV($hue, $sat, $value, int $line = 0);
    function getHSV(int $line = 0): array;
}
