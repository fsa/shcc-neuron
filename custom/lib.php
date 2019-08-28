<?php
/**
 * Не редактируйте данный файл. Он может быть изменён при обновлении системы.
 */
if (!is_array(spl_autoload_functions())) {
    set_include_path(get_include_path().':'.__DIR__.'/../classes/');
    spl_autoload_extensions('.php');
    spl_autoload_register();
    date_default_timezone_set(\Settings::get('timezone'));
}
require_once 'functions.php';
