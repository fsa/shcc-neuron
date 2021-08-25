<?php

require_once '../common.php';
httpResponse::setJsonExceptionHandler();
Session::grantAccess(['control']);
$request=file_get_contents('php://input');
$json=json_decode($request);
$response=[];
if (isset($json->sensors)) {
    $response['sensors']=[];
    foreach ($json->sensors as $uid) {
        $sensor=SmartHome\Sensors::get($uid);
        if(is_null($sensor)) {
            continue;
        }
        $sensor->uid=$uid;
        $response['sensors'][]=$sensor;
    }
}
if (isset($json->devices)) {
    $response['devices']=[];
    foreach ($json->devices as $device_name) {
        $device=SmartHome\Devices::get($device_name);
        if($device) {
            $response['devices'][]=['name'=>$device_name, 'state'=>$device->getState(), 'last_update'=>$device->getLastUpdate()];
        } else {
            $response['devices'][]=['name'=>$device_name, 'state'=>null, 'last_update'=>null];
        }
    }
}
if (isset($json->messages)) {
    $response['messages']=[];
    foreach ($json->messages as $messages) {
        switch ($messages) {
            case 'state':
                $response['messages'][]=['name'=>'state', 'content'=>getState()];
                break;
            case 'tts':
                $response['messages'][]=['name'=>'tts', 'content'=>Tts\Log::getLastMessages()];
                break;
        }
    }
}
if (count($response)>0) {
    httpResponse::json($response);
} else {
    httpResponse::error(404);
}

function getState() {
    $state=[];
    if (SmartHome\Vars::get('System.NightMode')) {
        $state[]='Включен ночной режим.';
    }
    if (SmartHome\Vars::get('System.SecurityMode')) {
        $state[]='Включен режим охраны.';
    }
    if (sizeof($state)==0) {
        $state[]='Система работает в обычном режиме.';
    }
    return $state;
}