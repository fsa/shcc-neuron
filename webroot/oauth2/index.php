<?php
require_once '../common.php';
$server=new Auth\Server();
switch (filter_input(INPUT_GET, 'response_type')) {
    case 'code':
        Session::grantAccess();
        httpResponse::redirection($server->requestTypeCode(Session::getUser(), ['email']));
        exit;
#    case 'token':
#        httpResponse::redirection($server->requestTypeToken());
#        exit;
}
switch (filter_input(INPUT_POST, 'grant_type')) {
    case 'authorization_code':
        #httpResponse::setJsonExceptionHandler();
        httpResponse::json($server->grantTypeAuthorizationCode());
        exit;
    case 'refresh_token':
        httpResponse::json($server->grantTypeRefreshToken());
        exit;
#    case 'password':
#        httpResponse::json($server->grantTypePassword());
#        exit;
#    case 'client_credentials':
#        httpResponse::json($server->grantTypeClientCredentials());
#        exit;
}
httpResponse::error(400);