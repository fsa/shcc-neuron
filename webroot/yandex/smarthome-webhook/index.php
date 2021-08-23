<?php

require_once '../../common.php';
httpResponse::setJsonExceptionHandler();
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
syslog(LOG_DEBUG, __FILE__.':'.__LINE__.' Yandex Alice: '.json_encode($dump, JSON_UNESCAPED_UNICODE));
$filename=implode('_',explode('/',$path)).'.php';
if(file_exists($filename)) {
    include $filename;
    exit;
}
httpResponse::error(404);
