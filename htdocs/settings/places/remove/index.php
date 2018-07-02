<?php

$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if(!$id) {
    die;
}
require_once '../../../common.php';
$count=\SmartHome\Places::delete($id);
HTML::showNotification('Удаление объекта',$count?'Объект удалён':'Объект не был удалён','../');