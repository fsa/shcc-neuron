<?php

require_once '../../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess(['control']);
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
httpResponse::json($state);
