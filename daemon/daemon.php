<?php
if (!sizeof($argv)==2) {
    die('Usage: '.$argv[0].' daemon-class');
}
require_once 'autoloader.php';
$daemon_class=$argv[1];
$daemon=new $daemon_class(\Settings::get('url').'/action/');
$daemon_name=$daemon->getName();
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
    try {
        $daemon->iteration();    
    } catch (Exception $ex) {
        error_log(date('c').PHP_EOL.print_r($ex,true));
        $daemon->finish();
        sleep(15);
        $daemon->prepare();
    }
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
