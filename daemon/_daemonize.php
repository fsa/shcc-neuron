<?php
/**
 * Daemonize
 * $daemon_name
 */
$baseDir=dirname(__FILE__);
$pid_file=$baseDir.'/pid/'.$daemon_name.'.pid';
$child_pid=pcntl_fork();
if ($child_pid==-1) {
    die;
} elseif ($child_pid) {
    echo "Daemon \"$daemon_name\" started.".PHP_EOL;
    exit;
}
posix_setsid();
file_put_contents($pid_file,getmypid());
ini_set('error_log',$baseDir.'/log/error.log');
fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN=fopen('/dev/null','r');
$STDOUT=fopen($baseDir.'/log/'.$daemon_name.'.log','ab');
$STDERR=fopen($baseDir.'/log/'.$daemon_name.'_error.log','ab');
pcntl_signal(SIGTERM, 'sigterm_handler');
pcntl_signal(SIGQUIT, 'sigterm_handler');

function sigterm_handler() {
    global $daemon_name;
    if (isDaemonActive()) {
        echo "Daemon \"$daemon_name\" already active.".PHP_EOL;
        exit;
    }
}

function isDaemonActive($pid_file) {
    global $pid_file;
    if (is_file($pid_file)) {
        $pid=file_get_contents($pid_file);
        if (posix_kill($pid,0)) {
            return true;
        } else {
            if (!unlink($pid_file)) {
                exit(-1);
            }
        }
    }
    return false;
}
