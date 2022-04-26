<?php

namespace SmartHome\Capability;

interface BrightnessInterface {

    function setBrightness(int $brightness): void;

    function getBrightness(): int;
}
