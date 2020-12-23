<?php

require_once '../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess(['control']);
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