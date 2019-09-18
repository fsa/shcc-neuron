<?php

require_once '../../../common.php';
Auth\Internal::grantAccess(['admin']);
$text=filter_input(INPUT_GET, 'text');
$parent=filter_input(INPUT_GET, 'parent', FILTER_VALIDATE_INT);
if($parent===false) {
    $parent=null;
}
if(!$text) {
    httpResponse::json(['error'=>'Неверные параметры запроса']);
}
if($text=='') {
    httpResponse::json(['error'=>'Заголовок элемента не может быть пустым']);
}
httpResponse::json(SmartHome\Places::create($text, $parent));