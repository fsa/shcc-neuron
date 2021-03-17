<?php

require_once '../../common.php';
httpResponse::setJsonExceptionHandler();
$request=file_get_contents('php://input');
$json=json_decode($request);
$response=[];
if (isset($json->sensors)) {
    $response['sensors']=[];
    $mem=new \SmartHome\MemoryStorage;
    foreach ($json->sensors as $sensor) {
        $meter=$mem->getSensor($sensor);
        if(is_null($meter)) {
            continue;
        }
        $meter->uid=$sensor;
        $response['sensors'][]=$meter;
    }
}
if (count($response)>0) {
    httpResponse::json($response);
} else {
    httpResponse::error(404);
}