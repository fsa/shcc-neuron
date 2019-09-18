<?php

$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if(!$id) {
    die;
}
require_once '../../../common.php';
Auth\Internal::grantAccess(['admin']);
$count=\SmartHome\Places::delete($id);
HTML::storeNotification('Удаление объекта',$count?'Объект удалён':'Объект не был удалён');
httpResponse::redirect('../');