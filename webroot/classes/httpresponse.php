<?php

class httpResponse {

    const HTTP_STATUS_CODES=[
        301=>'Moved Permanently',
        302=>'Moved Temporarily',
        304=>'Not Modified',
        400=>'Bad Request',
        401=>'Unauthorized',
        402=>'Payment Required',
        403=>'Forbidden',
        404=>'Not Found',
        405=>'Method Not Allowed',
        429=>'Too Many Requests',
        500=>'Internal Server Error',
        503=>'Service Unavailable'
    ];

    private static $template;
    private static $context;
    private static $last_modified;
    private static $etag;
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
        self::$etag=$etag;
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
        $notification=filter_input(INPUT_COOKIE, 'notification');
        if ($notification) {
            $template->notify=$notification;
            setcookie('notification', '', time()-3600, '/');
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
        if (isset(self::$message_id)) {
            self::showPopup('popupMessage'.self::$message_id, $message, 'Ошибка', 'danger');
            self::showHtmlFooter();
        } else {
            self::showMessagePage($message, 'Ошибка', 'danger');
        }
        exit;
    }

    public static function showInformation(string $message) {
        if (isset(self::$message_id)) {
            self::showPopup('popupMessage'.self::$message_id, $message, 'Информация', 'info');
            self::$message_id++;
        } else {
            self::showMessagePage($message, 'Информация', 'info');
        }
    }

    # Ответ JSON

    public static function json($response, $options=JSON_UNESCAPED_UNICODE) {
        header('Content-Type: application/json;charset=UTF-8');
        echo json_encode($response, $options);
        exit;
    }

    public static function jsonString(?string $response) {
        if (is_null($response)) {
            self::error(404);
        }
        header('Content-Type: application/json;charset=UTF-8');
        echo $response;
        exit;
    }

    # Коды ответов <>200

    public static function redirection($location, $code=302, $message=null) {
        if (is_null($message)) {
            $message=self::HTTP_STATUS_CODES[$code];
        }
        $header=sprintf('%s %d %s', getenv('SERVER_PROTOCOL'), $code, $message);
        header($header, true);
        header("Location: $location");
        printf('%s<br>Location: <a href="%s">%s</a>', $header, $location, $location);
        exit;
    }

    public static function error($code, $message=null) {
        if (is_null($message)) {
            $message=self::HTTP_STATUS_CODES[$code];
        }
        header(sprintf('%s %d %s', getenv('SERVER_PROTOCOL'), $code, $message), true, $code);
        echo $message;
        exit;
    }

    public static function errorJson($code, $response, $options=JSON_UNESCAPED_UNICODE) {
        header(sprintf('%s %d %s', getenv('SERVER_PROTOCOL'), $code, self::HTTP_STATUS_CODES[$code]), true, $code);
        header('Content-Type: application/json;charset=UTF-8');
        header('Cache-Control: no-store');
        header('Pragma: no-cache');
        if(is_string($response)) {
            echo $response;
        } else {
            echo json_encode($response, $options);
        }
        exit;
    }

    # Exceptions handlers

    public static function HtmlPageException($ex) {
        $class=get_class($ex);
        $class_parts=explode('\\', $class);
        if (end($class_parts)=='UserException') {
            self::showMessagePage($ex->getMessage(), 'Ошибка', 'primary');
        } else if (end($class_parts)=='AppException') {
            $code=$ex->getCode();
            switch ($code) {
                case 401:
                    self::showLoginForm(getenv('REQUEST_METHOD')=='GET'?getenv('REQUEST_URI'):'/');
                    exit;
                case 403:
                    self::showMessagePage('У вас отсутствуют необходиме права доступа. <a href="/">Перейти на главную страницу</a>.', 'Доступ запрещён', 'danger');
                    exit;
                case 400:
                case 402:
                case 403:
                case 404:
                case 405:
                case 429:
                    self::showMessagePage($ex->getMessage()??self::HTTP_STATUS_CODES[$code], $code, 'warning');
                    exit;
                default:
                    self::showMessagePage($ex->getMessage(), 'Программная ошибка', 'danger');
                    exit;
            }
        } else if (getenv('DEBUG')) {
            error_log($ex, 0);
            self::showMessagePage('<pre>'.(string) $ex.'</pre>', 'Отладочная информация об ошибке', 'danger');
        } else {
            error_log($ex, 0);
            self::error(500);
        }
        exit;
    }

    public static function HtmlException($ex) {
        if (getenv('DEBUG')) {
            $message=(string) $ex;
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
        if (end($class_parts)=='UserException') {
            self::json(['error'=>$ex->getMessage()]);
            exit;
        } else if (end($class_parts)=='AppException') {
            switch ($ex->getCode()) {
                case 400:
                case 401:
                case 402:
                case 403:
                case 404:
                case 405:
                case 429:
                    self::errorJson($ex->getCode(), $ex->getMessage());
                    exit;
            }
            self::json(['error'=>'Server error: '.$ex->getMessage()]);
            exit;
        } else {
            error_log($ex, 0);
            self::error(500);
        }
    }

    # Подключение обработчиков ошибок

    public static function setHtmlExceptionHandler(array $context=null) {
        self::$context=$context;
        set_exception_handler([__CLASS__, 'HtmlPageException']);
    }

    public static function setJsonExceptionHandler() {
        set_exception_handler([__CLASS__, 'JsonException']);
    }

}
