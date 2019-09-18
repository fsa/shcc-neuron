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
                404=>'Not Found',
                429=>'Too Many Requests'
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

    public static function json($response, $options=JSON_UNESCAPED_UNICODE) {
        header('Content-Type: application/json');
        echo json_encode($response, $options);
        exit;
    }
    
    public static function setJsonExceptionHanler() {
        set_exception_handler([__CLASS__,'JsonException']);
    }

    public static function JsonException($ex) {
        error_log($ex, 0);
        self::json(['error'=>'Internal Server Error']);
    }

}
