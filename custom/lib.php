<?php

/**
 * Не редактируйте данный файл. Он может быть изменён при обновлении системы.
 */
if (!spl_autoload_functions()) {
    require_once '../vendor/autoload.php';
    ini_set('syslog.filter', 'raw');
    openlog("shcc@cli", LOG_PID | LOG_ODELAY, LOG_USER);
}
require_once 'functions.php';
