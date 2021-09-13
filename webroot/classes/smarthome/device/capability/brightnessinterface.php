<?php

namespace SmartHome\Device\Capability;

interface BrightnessInterface {

    function setBrightness(int $brightness): void;

    function getBrightness(): int;
}
