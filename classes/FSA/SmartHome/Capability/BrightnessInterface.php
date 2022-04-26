<?php

namespace FSA\SmartHome\Capability;

interface BrightnessInterface
{
    function setBrightness(int $brightness, int $line = 0): void;
    function getBrightness(int $line = 0): int;
}
