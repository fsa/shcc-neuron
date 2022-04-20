<?php

/**
 * Не редактируйте данный файл. Он может быть изменён при обновлении системы.
 */
if (!spl_autoload_functions()) {
    require_once '../vendor/autoload.php';
    App::init('shcc@cli');
}
require_once 'functions.php';
