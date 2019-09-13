<?php

require_once '../../common.php';
httpResponse::setJsonExceptionHanler();
Auth\Internal::grantAccess(['admin']);
$uid=filter_input(INPUT_GET, 'uid');
$token=filter_input(INPUT_GET, 'token');
if(!$uid or !$token) {
    throw new AppException('Ошибка в параметрах');
}
$result=miIO\Tokens::updateToken($uid, $token);
httpResponse::json(['result'=>$result]);