<?php

namespace Auth;

use httpResponse;

class Bearer {

    private static $access_token;
    private static $user_id;

    public static function grantAccess() {
        $bearer=getenv('HTTP_AUTHORIZATION');
        if (!preg_match('/Bearer\s(\S+)/', $bearer, $matches)) {
            httpResponse::authRequired('Bearer realm="The access token required"');
            exit;
        }
        $token=Server::fetchTokensByAccessToken($matches[1]);
        if (!$token) {
            httpResponse::authRequired('Bearer error="invalid_token",error_description="Invalid access token"');
            exit;
        }
        if($token->expired) {
            httpResponse::authRequired('Bearer error="invalid_token",error_description="The access token expired"');
            exit;
        }
        self::$access_token=$token->access_token;
        self::$user_id=$token->user_id;
    }

    public static function getUserId() {
        return self::$user_id;
    }

    public static function getAccessToken() {
        return self::$access_token;
    }

}
