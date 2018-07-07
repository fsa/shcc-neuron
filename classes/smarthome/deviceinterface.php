<?php

namespace SmartHome;

interface DeviceInterface {

    /**
     * Минимальная инициализация объекта для возможности управления устройством
     * @param type $device_id
     * @param type $init_data
     */
    function init($device_id,$init_data);
    
    /**
     * Возвращает описание устройства
     */
    function getDeviceDescription();

    /**
     * Возвращает список свойств объекта, которые будут присвоены при минимальной инициализации
     */
    function getInitDataList();
    
    /**
     * Возвращает текущеие свойства объекта, которые нужны при инициализации
     */
    function getInitDataValues();
    /**
     * Возвращает идентификатор устройства, уникальный внутри модуля
     */
    function getDeviceId();

    /**
     * Возвращает наименование устройства
     */
    function getModuleName();

    /**
     * Возврщает детальную информацию о состоянии устройства
     */
    function getDeviceStatus();

    /**
     * Возвращает дату последнего обновления данных устройства
     */
    function getLastUpdate();
}
