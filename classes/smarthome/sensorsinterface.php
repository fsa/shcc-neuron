<?php

namespace SmartHome;

interface SensorsInterface {

    /**
     * Возвращает список измерительных приборов устойства
     */
    function getDeviceMeters(): array;

    /**
     * Возвращает список датчиков устройства
     */
    function getDeviceIndicators(): array;
}
