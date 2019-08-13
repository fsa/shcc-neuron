<?php

require_once '../common.php';
$request_id=getenv('HTTP_X_REQUEST_ID');
$request_content=file_get_contents('php://input');
$path=getenv('PATH_INFO');
$dump=[
    'ip'=>getenv('REMOTE_ADDR'),
    'path'=>$path,
    'Authorization'=>getenv('HTTP_AUTHORIZATION'),
    'X-Request-Id' =>$request_id,
    'content'=>$request_content
];
file_put_contents('json_'.date('Y_m_d').'.txt', print_r($dump, true).PHP_EOL, FILE_APPEND | LOCK_EX);
$filename=implode('_',explode('/',$path)).'.php';
if(file_exists($filename)) {
    include $filename;
    exit;
}
httpResponse::error(404);
