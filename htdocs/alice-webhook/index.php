<?php

require_once '../common.php';
$request_id=getenv('HTTP_X_REQUEST_ID');
$path=getenv('PATH_INFO');
$dump=[
    'ip'=>getenv('REMOTE_ADDR'),
    'path'=>$path,
    'Authorization'=>getenv('HTTP_AUTHORIZATION'),
    'X-Request-Id' =>$request_id,
    'content'=>file_get_contents('php://input')
];
file_put_contents('json_'.date('Y_m_d').'.txt', print_r($dump, true).PHP_EOL, FILE_APPEND | LOCK_EX);
header('Content-Type: application/json');
$filename=implode('_',explode('/',$path)).'.php';
if(file_exists($filename)) {
    \Auth\Bearer::grantAccess();
    include $filename;
    exit;
}
header(getenv('SERVER_PROTOCOL').' 404 Not Found');
