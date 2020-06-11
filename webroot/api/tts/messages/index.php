<?php

require_once '../../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess(['control']);
$log=Tts\Log::getLastMessages();
httpResponse::json($log);