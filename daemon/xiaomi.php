<?php
$daemon_name='xiaomi';
require_once 'autoloader.php';
require_once './_daemonize.php';

$daemon=new Xiaomi\Daemon();
$daemon->prepare();
do {
    $daemon->iteration();
} while (1);
$daemon->finish();
