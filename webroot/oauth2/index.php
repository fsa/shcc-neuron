<?php
require_once '../common.php';
$server=new Auth\Server();
if($server->getResponseType()) {
    exit;
}
if($server->getGrantType()) {
    exit;
}
httpResponse::error(400);