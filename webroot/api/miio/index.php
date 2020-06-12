<?php

require_once '../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess([]);
$uid=filter_input(INPUT_GET, 'uid');
$token=filter_input(INPUT_GET, 'token');
if(!$uid or !$token) {
    httpResponse::showError('Ошибка в параметрах');
}
$result=miIO\Tokens::updateToken($uid, $token);
httpResponse::json(['result'=>$result]);