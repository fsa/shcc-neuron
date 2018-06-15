<?php

require_once 'autoloader.php';
if (sizeof($argv)==2) {
    $cmd=$argv[1];
} else {
    $cmd='';
}
switch ($cmd) {
    case "start":
        start();
        break;
    case "stop":
        stop();
        break;
    case "restart":
        stop();
        sleep(2);
        start();
        break;
    default:
        die('Usage: daemonctl.php start|stop|restart'.PHP_EOL);
}

function start() {
    $stmt=DB::query('SELECT name, namespace FROM modules WHERE daemon_disabled=0 AND disabled=0');
    $daemons=$stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    foreach ($daemons as $name=>$namespace) {
        $daemon_class=$namespace.'\\Daemon';
        if (class_exists($daemon_class)) {
            echo "Starting module daemon '$name'.".PHP_EOL;
            exec("php -f daemon.php \"$daemon_class\"");
        }
    }
}

function stop() {
    $dir=__DIR__.'/pid/';
    $files=scandir($dir);
    foreach ($files as $pidfile) {
        if(substr($pidfile,-4,4)!='.pid') {
            continue;
        }
        $name=substr($pidfile,0,-4);
        $pid=file_get_contents($dir.$pidfile);
        echo "Stoping module daemon '$name'.".PHP_EOL;
        if (posix_kill($pid,SIGKILL)) {
            pcntl_signal_dispatch();
            echo "Daemon $pid stopped.".PHP_EOL;
        } else {
            if (!unlink($dir.$pidfile)) {
                echo "Error deleting PID file ".$pidfile;
            }
        }
    }
}
