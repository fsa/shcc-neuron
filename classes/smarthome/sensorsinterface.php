<?php

namespace SmartHome;

interface SensorsInterface {

    function getDeviceMeters(): array;

    function getDeviceIndicators(): array;
}
