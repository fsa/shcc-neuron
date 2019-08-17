<?php

require_once '../../../common.php';
Auth\Internal::grantAccess(['admin']);
$id=filter_input(INPUT_GET, 'id');
$text=filter_input(INPUT_GET, 'text');
if(!$id or !$text) {
    httpResponse::json(['error'=>'Неверные параметры запроса']);
}
if($text=='') {
    httpResponse::json(['error'=>'Заголовок элемента не может быть пустым']);
}
SmartHome\Places::rename($id, $text);
httpResponse::json(['result'=>'ok']);