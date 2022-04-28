<?php

namespace FSA\SmartHome;

interface DaemonInterface
{
    /**
     * Конструктор класса
     * @param callable $event 
     * @param array $params массив параметров настроек демона
     */
    function __construct(callable $event, array $params);
    /**
     * Возвращает имя демона
     */
    function getName();
    /**
     * Подготовительные операции перед запуском цикла обработки данных
     */
    function prepare();
    /**
     * Тело цикла обработки данных
     */
    function iteration();
    /**
     * Операции, выполняемые при остановке цикла
     */
    function finish();
}
