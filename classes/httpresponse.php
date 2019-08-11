<?php

class httpResponse {

    public static function redirect($location, $code=302, $message=null) {
        if (is_null($message)) {
            $codes=[
                301=>'Moved Permanently',
                302=>'Moved Temporarily'
            ];
            $message=$codes[$code];
        }
        $header=sprintf('%s %d %s', getenv('SERVER_PROTOCOL'), $code, $message);
        header($header, true);
        header("Location: $location");
        printf('%s<br>Location: <a href="%s">%s</a>', $header, $location, $location);
        exit;
    }

    public static function error($code, $message=null) {
        if (is_null($message)) {
            $codes=[
                400=>'Bad Request',
                401=>'Unauthorized',
                403=>'Forbidden',
                404=>'Not Found'
            ];
            $message=$codes[$code];
        }
        $header=sprintf('%s %d %s', getenv('SERVER_PROTOCOL'), $code, $message);
        header($header, true);
        echo $message;
        exit;
    }

    public static function authRequired($www_authenticate_header) {
        header('WWW-Authenticate: '.$www_authenticate_header);
        header(getenv('SERVER_PROTOCOL').' 401 Unauthorized', true);
    }

    public static function json($response) {
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

}
