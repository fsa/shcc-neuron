<?php
if (sizeof($argv)!=2) {
    die('Usage: '.$argv[0].' module_name'.PHP_EOL);
}
require_once 'autoloader.php';
$module=$argv[1];
$url=Settings::get('url');
$response=file_get_contents($url.'/api/daemon/', 0, stream_context_create([
    'http'=>[
        'method'=>'POST',
        'header'=>"Content-Type: application/json; charset=utf-8\r\n",
        'content'=>json_encode(['module'=>$module])
    ]
]));
if($response===false) {
    echo "Error getting module daemon \"$module\" state.".PHP_EOL;
    exit(1);
}
$state=json_decode($response);
if($state===false) {
    echo "Module daemon \"$module\" json responce error.".PHP_EOL;
    exit(2);
}
if(!isset($state->daemon)) {
    echo "Responce error getting module daemon \"$module\".".PHP_EOL;
    exit(3);
}
if(!$state->daemon) {
    echo "Module daemon \"$module\" is disabled.".PHP_EOL;
    exit(0);
}
$daemon_class=$state->class;
if(!class_exists($daemon_class)) {
    echo "Daemon class \"$daemon_class\" not exists.".PHP_EOL;
    exit(0);
}
$params=Settings::get(strtolower($module),[]);
$params['process_url']=$url.'/api/events/';
$daemon=new $daemon_class($params);
$daemon_name=$daemon->getName();
echo "Starting '$module' module daemon.".PHP_EOL;
$daemon->prepare();
while (1) {
    try {
        $daemon->iteration();
    } catch (Exception $ex) {
        error_log(date('c').PHP_EOL.print_r($ex,true));
        break;
    }
}
$daemon->finish();
