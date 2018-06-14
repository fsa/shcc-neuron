<?php
$daemon_name='yeelight';
require_once 'autoloader.php';
require_once './_daemonize.php';

$daemon=new Yeelight\Daemon();
$daemon->prepare();
do {
    $daemon->iteration();
} while (1);
