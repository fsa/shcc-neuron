<?php

class httpResponse {

    private static $template;
    private static $context;
    private static $last_modified;
    private static $etag;
    private static $title;
    private static $message_id;

    # Ответ HTML
    public static function getTemplate() {
        if (is_null(self::$template)) {
            self::$template=new Templates\ContentPage;
        }
        return self::$template;
    }

    public static function setTemplate(object $template) {
        self::$template=$template;
    }

    public static function setContext(array $context) {
        self::$context=$context;
    }

    public static function setLastModified(string $timestamp) {
        self::$last_modified=strtotime($timestamp);
    }

    public static function setETag(string $etag) {
        self::$etag;
    }

    public static function addHeader(string $header) {
        $template=self::getTemplate();
        $template->header.=$header.PHP_EOL;
    }

    public static function addDescription($description) {
        self::addHeader("<meta name=\"description\" content=\"$description\">");
    }

    public static function getTitle() {
        $template=self::getTemplate();
        return $template->title;
    }

    public static function showHtmlHeader($title=null) {
        $template=self::getTemplate();
        $template->title=$title;
        $template->context=self::$context;
        if (isset(self::$last_modified)) {
            header("Last-Modified: ".substr(gmdate('r', self::$last_modified), 0, -5).'GMT');
        }
        if (isset(self::$etag)) {
            header("ETag: ".self::$etag);
        }
        self::disableBrowserCache();
        $notification=filter_input(INPUT_COOKIE,'notification');
        if($notification) {
            $template->notify=$notification;
            setcookie('notification','',time()-3600,'/');
        }
        $template->Header();
        set_exception_handler([__CLASS__, 'HtmlException']);
        self::$message_id=0;
    }

    public static function showHtmlFooter() {
        $template=self::getTemplate();
        $template->Footer();
    }

    public static function storeNotification(string $message) {
        setcookie('notification', $message, 0, '/');
    }

    public static function showLoginForm(string $redirect_url=null) {
        $template=new \Templates\Login();
        $template->title='Вход в систему';
        $template->context=self::$context;
        if ($redirect_url) {
            $template->redirect_uri=$redirect_url;
            $template->url='/login/';
        }
        self::disableBrowserCache();
        $template->show();
    }

    public static function __callStatic($name, $args) {
        if (substr($name, 0, 4)!='show') {
            throw new Exception('Call to undefined method '.__CLASS__.'::'.$name);
        }
        $method_name=substr($name, 4);
        return self::getTemplate()->$method_name(...$args);
    }

    public static function disableBrowserCache() {
        header("Cache-Control: no-store, no-cache, must-revalidate");
    }

    # Информация для пользователя
    public static function showPopup(string $message, string $title, string $style=null) {
        $template=self::getTemplate();
        $template->Popup($message, $title, $style);
    }

    public static function showMessagePage(string $message, string $title, string $style=null) {
        $template=new \Templates\Message();
        $template->style=$style;
        $template->title=$title;
        $template->context=self::$context;
        $template->message=$message;
        self::disableBrowserCache();
        $template->show();
    }

    public static function showError(string $message) {
        if(isset(self::$message_id)) {
            self::showPopup('popupMessage'.self::$message_id, $message, 'Ошибка', 'danger');
            self::showHtmlFooter();
        } else {
            self::showMessagePage($message, 'Ошибка', 'danger');
        }
        exit;
    }

    public static function showInformation(string $message) {
        if(isset(self::$message_id)) {
            self::showPopup('popupMessage'.self::$message_id, $message, 'Информация', 'info');
            self::$message_id++;
        } else {
            self::showMessagePage($message, 'Информация', 'info');
        }
    }

    # Ответ JSON
    public static function json($response, $options=JSON_UNESCAPED_UNICODE) {
        header('Content-Type: application/json');
        echo json_encode($response, $options);
        exit;
    }

    # Коды ответов <>200
    public static function redirection($location, $code=302, $message=null) {
        if (is_null($message)) {
            $codes=[
                301=>'Moved Permanently',
                302=>'Moved Temporarily',
                304=>'Not Modified'
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
                429=>'Too Many Requests',
                500=>'Internal Server Error',
                503=>'Service Unavailable'
            ];
            $message=$codes[$code];
        }
        $header=sprintf('%s %d %s', getenv('SERVER_PROTOCOL'), $code, $message);
        header($header, true);
        echo $message;
        exit;
    }

    # Exceptions handlers
    public static function HtmlPageException($ex) {
        $class=get_class($ex);
        $class_parts=explode('\\', $class);
        if(end($class_parts)=='UserException') {
            $message=(string)$ex;
        } else if(end($class_parts)=='AuthException') {
            self::showLoginForm(getenv('REQUEST_METHOD')=='GET'?getenv('REQUEST_URI'):'/');
            exit;
        } else if(end($class_parts)=='AccessException') {
            $message='Доступ запрещён. <a href="/">Перейти на главную страницу</a>.';
        } else if (getenv('DEBUG')) {
            $message=(string)$ex;
            error_log($ex, 0);
        } else {
            error_log($ex, 0);
            httpResponse::error(500);
        }
        self::showMessagePage($message, 'Ошибка', 'danger');
        exit;
    }

    public static function HtmlException($ex) {
        if (getenv('DEBUG')) {
            $message=(string)$ex;
        } else {
            error_log($ex, 0);
            $message='Произошла программная ошибка на сервере.';
        }
        self::showPopup($message, '500', 'danger');
        self::showHtmlFooter();
        exit;
    }

    public static function JsonException($ex) {
        $class=get_class($ex);
        $class_parts=explode('\\', $class);
        if(end($class_parts)=='UserException') {
            self::json(['error'=>(string)$ex]);
            exit;
        } else if(end($class_parts)=='AuthException') {
            httpResponse::error(401);
        } else if(end($class_parts)=='AccessException') {
            httpResponse::error(403);
        } else {
            error_log($ex, 0);
            httpResponse::error(500);
        }
    }

    # Подключение обработчиков ошибок
    public static function setExceptionHandler() {
        set_exception_handler([__CLASS__, 'HtmlPageException']);
    }

    public static function setJsonExceptionHandler() {
        set_exception_handler([__CLASS__, 'JsonException']);
    }

}
