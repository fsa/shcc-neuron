<?php
$pid_file='/tmp/my_pid_file.pid';

$child_pid=pcntl_fork();
if ($child_pid==-1) {
    die;
} elseif ($child_pid) {
    echo "Daemon started.";
    exit;
}
posix_setsid();
file_put_contents($pidgile,getmypid());
$baseDir=dirname(__FILE__);
ini_set('error_log',$baseDir.'/log/error.log');
fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN=fopen('/dev/null','r');
$STDOUT=fopen($baseDir.'/log/application.log','ab');
$STDERR=fopen($baseDir.'/log/daemon.log','ab');
while (!$stop_server) {
    //TODO: делаем что-то
}

function isDaemonActive($pid_file) {
    if (is_file($pid_file)) {
        $pid=file_get_contents($pid_file);
        //проверяем на наличие процесса
        if (posix_kill($pid,0)) {
            //демон уже запущен
            return true;
        } else {
            //pid-файл есть, но процесса нет 
            if (!unlink($pid_file)) {
                //не могу уничтожить pid-файл. ошибка
                exit(-1);
            }
        }
    }
    return false;
}

if (isDaemonActive($pid_file)) {
    echo 'Daemon already active.';
    exit;
}