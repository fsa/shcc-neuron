<?php

require_once '../../../common.php';
Auth\Internal::grantAccess(['admin']);
$id=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) {
    httpResponse::json(['error'=>'Неверные параметры запроса']);
}
try {
    SmartHome\Places::delete($id);
} catch (Exception $ex) {
    httpResponse::json(['error'=>'Ошибка при выполнении запроса на удаление элемента']);
}
httpResponse::json(['result'=>'ok']);