<?php

$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if(!$id) {
    die;
}
require_once '../../../common.php';
Auth\Session::grantAccess([]);
$count=\SmartHome\Places::delete($id);
httpResponse::storeNotification($count?'Объект удалён':'Объект не был удалён');
httpResponse::redirection('../');