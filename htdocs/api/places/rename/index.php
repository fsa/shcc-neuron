<?php

require_once '../../../common.php';
Auth\Session::grantAccess([]);
$id=filter_input(INPUT_GET, 'id');
$text=filter_input(INPUT_GET, 'text');
if(!$id or !$text) {
    httpResponse::json(['error'=>'Неверные параметры запроса']);
}
if($text=='') {
    httpResponse::json(['error'=>'Заголовок элемента не может быть пустым']);
}
try {
    $rows=SmartHome\Places::rename($id, $text);    
} catch (Exception $ex) {
    httpResponse::json(['error'=>'Не удалось переименовать элемент']);
}
if($rows==0) {
    httpResponse::json(['error'=>'Элемент для переименования не найден. Обновие страницу.']);
}
httpResponse::json(['result'=>'ok']);