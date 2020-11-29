<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-29
 */

namespace SmartHome;

interface DeviceInterface {

    /**
     * Инициализация объекта для возможности взаимодействия с устройством
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
     * Возвращает уникальный аппаратный идентификатор устройства
     */
    function getHwid(): string;

    /**
     * Возвращает массив с данными о состоянии устройства
     */
    function getState(): array;

    /**
     * Возвращает дату и время последнего обновления данных устройства в формате timestamp
     */
    function getLastUpdate(): int;

    /**
     * Возвращает список событий, генерируемых устройством
     */
    function getEventsList(): array;

    function __toString(): string;
}
