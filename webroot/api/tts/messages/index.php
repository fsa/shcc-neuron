<?php

require_once '../../../common.php';
httpResponse::setJsonExceptionHandler();
Auth\Session::grantAccess(['control']);
$log=Tts\Log::getLastMessages();
httpResponse::json($log);