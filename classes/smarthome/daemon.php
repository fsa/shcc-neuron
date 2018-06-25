<?php

namespace SmartHome;

interface Daemon {
    /**
     * Конструктор класса
     * @param type $precess_url URL для обработки событий
     */
    function __construct($process_url);
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

