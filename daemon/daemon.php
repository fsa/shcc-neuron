<?php
if (!sizeof($argv)==3) {
    die('Usage: '.$argv[0].' name namespace');
}
require_once 'autoloader.php';
$daemon_name=$argv[1];
$daemon_class=$argv[2].'\\Daemon';
$daemon=new $daemon_class;
$baseDir=dirname(__FILE__);
$pid_file=$baseDir.'/pid/'.$daemon_name.'.pid';
if (isDaemonActive($pid_file)) {
    echo "Daemon \"$daemon_name\" already active.".PHP_EOL;
    exit;
}
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

$daemon->prepare();
$stop_server=false;
while (!$stop_server) {
    $daemon->iteration();
}
$daemon->finish();
unlink($pid_file);
exit;

function isDaemonActive($pid_file) {
    if (!is_file($pid_file)) {
        return false;
    }
    $pid=file_get_contents($pid_file);
    if (posix_kill($pid,0)) {
        return true;
    }
    if (!unlink($pid_file)) {
        exit(-1);
    }
    return false;
}
