<?php

namespace SmartHome;

interface DeviceInterface {

    /**
     * Минимальная инициализация объекта для возможности управления устройством
     * @param string $device_id
     * @param array $init_data
     */
    function init($device_id,$init_data): void;
    
    /**
     * Возвращает описание устройства
     */
    function getDeviceDescription(): string;

    /**
     * Возвращает список свойств объекта, которые будут присвоены при минимальной инициализации
     */
    function getInitDataList(): array;
    
    /**
     * Возвращает текущеие свойства объекта, которые нужны при инициализации
     */
    function getInitDataValues(): array;
    /**
     * Возвращает идентификатор устройства, уникальный внутри модуля
     */
    function getDeviceId(): string;

    /**
     * Возвращает наименование модуля устройства
     */
    function getModuleName(): string;

    /**
     * Возвращает детальную информацию о состоянии устройства
     */
    function getDeviceStatus(): string;

    /**
     * Возвращает дату и время последнего обновления данных устройства в формате timestamp
     */
    function getLastUpdate(): int;
}
