<?php

require_once '../../../common.php';
Auth\Internal::grantAccess(['admin']);
$text=filter_input(INPUT_GET, 'text');
$parent=filter_input(INPUT_GET, 'parent');
if(!$text or !$parent) {
    httpResponse::json(['error'=>'Неверные параметры запроса']);
}
if($text=='') {
    httpResponse::json(['error'=>'Заголовок элемента не может быть пустым']);
}
httpResponse::json(SmartHome\Places::create($text, $parent));