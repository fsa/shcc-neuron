<?php
/**
 * Не редактируйте данный файл. Он может быть изменён при обновлении системы.
 */
if (!is_array(spl_autoload_functions())) {
    set_include_path(get_include_path().':'.__DIR__.'/../classes/');
    spl_autoload_extensions('.php');
    spl_autoload_register();
    openlog("shcc@cli", LOG_PID|LOG_ODELAY, LOG_USER);
}
require_once 'functions.php';
