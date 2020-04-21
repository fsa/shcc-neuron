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
        start();
        break;
    default:
        die('Usage: daemonctl.php start|stop|restart'.PHP_EOL);
}

function start() {
    SmartHome\Devices::refreshMemoryDevices();
    $daemons=SmartHome\Daemons::getActive();
    $daemons['TTS']='Tts';
    foreach ($daemons as $name=> $class) {
        if (class_exists($class)) {
            echo "Starting '$name' daemon.".PHP_EOL;
            exec("php -f daemon.php \"$class\"");
        }
    }
}

function stop() {
    $dir=__DIR__.'/pid/';
    $files=scandir($dir);
    foreach ($files as $pidfile) {
        if (substr($pidfile,-4,4)!='.pid') {
            continue;
        }
        $name=substr($pidfile,0,-4);
        $pid=file_get_contents($dir.$pidfile);
        echo "Stoping daemon '$name'.".PHP_EOL;
        if (posix_kill($pid,SIGKILL)) {
            echo "Daemon $pid stopped.".PHP_EOL;
        }
        if (!unlink($dir.$pidfile)) {
            echo "Error deleting PID file ".$pidfile;
        }
    }
}
