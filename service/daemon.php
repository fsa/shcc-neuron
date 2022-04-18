<?php

if (sizeof($argv)!=2) {
    die('Usage: '.$argv[0].' module_name'.PHP_EOL);
}
require '../vendor/autoload.php';
$module=$argv[1];
if(!$url=getenv('SERVER_URL')) {
    $url='http://127.0.0.1';
}
$response=file_get_contents($url.'/api/daemon/', 0, stream_context_create([
    'http'=>[
        'method'=>'POST',
        'header'=>"Content-Type: application/json; charset=utf-8\r\n",
        'content'=>json_encode(['module'=>$module])
    ]
        ]));
if ($response===false) {
    echo "Error getting module daemon \"$module\" state.".PHP_EOL;
    exit(1);
}
$state=json_decode($response);
if (isset($state->error)) {
    echo $state->error.PHP_EOL;
    exit(2);
}
if ($state===false) {
    echo "Module daemon \"$module\" json response error.".PHP_EOL;
    exit(3);
}
if (!isset($state->daemon)) {
    echo "Response error getting module daemon \"$module\".".PHP_EOL;
    exit(4);
}
if (!$state->daemon) {
    echo "Module daemon \"$module\" is disabled.".PHP_EOL;
    exit(0);
}
openlog("shcc@$module", LOG_PID|LOG_ODELAY, LOG_USER);
$daemon_class=$state->class;
if (!class_exists($daemon_class)) {
    echo "Daemon class \"$daemon_class\" not exists.".PHP_EOL;
    exit(0);
}
if (isset($state->timezone)) {
    date_default_timezone_set($state->timezone);
}
$params=(array)$state->settings;
$params['events_url']=$url.'/api/events/';
$daemon=new $daemon_class($params);
$daemon_name=$daemon->getName();
echo "Starting '$module' module daemon.".PHP_EOL;
$daemon->prepare();
while (1) {
    try {
        $daemon->iteration();
    } catch (Exception $ex) {
        syslog(LOG_ERR, print_r($ex, true));
        break;
    }
}
$daemon->finish();
