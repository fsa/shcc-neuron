<?php
use FSA\Neuron\HttpResponse,
    FSA\Neuron\Session;
require_once '../common.php';
$server=new OAuth\Server();
switch (filter_input(INPUT_GET, 'response_type')) {
    case 'code':
        Session::grantAccess();
        HttpResponse::redirection($server->requestTypeCode(Session::getUserId(), ['email']));
        exit;
#    case 'token':
#        HttpResponse::redirection($server->requestTypeToken());
#        exit;
}
switch (filter_input(INPUT_POST, 'grant_type')) {
    case 'authorization_code':
        HttpResponse::setJsonMode();
        HttpResponse::json($server->grantTypeAuthorizationCode());
        exit;
    case 'refresh_token':
        HttpResponse::setJsonMode();
        HttpResponse::json($server->grantTypeRefreshToken());
        exit;
#    case 'password':
#        HttpResponse::json($server->grantTypePassword());
#        exit;
#    case 'client_credentials':
#        HttpResponse::json($server->grantTypeClientCredentials());
#        exit;
}
HttpResponse::error(400);