<?php

namespace SmartHome;

interface DeviceInterface {

    /**
     * Минимальная инициализация объекта для возможности управления устройством
     * @param string $device_id
     * @param array $init_data
     */
    function init($device_id, $init_data): void;
    
    /**
     * Возвращает описание устройства
     */
    function getDescription(): string;

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
    function getId(): string;

    /**
     * Возвращает наименование модуля устройства
     */
    function getModuleName(): string;

    /**
     * Возвращает массив с данными о состоянии устройства
     */
    function getState(): array;

    /**
     * Возвращает детальную информацию о состоянии устройства
     */
    function getStateString(): string;

    /**
     * Возвращает дату и время последнего обновления данных устройства в формате timestamp
     */
    function getLastUpdate(): int;
}
