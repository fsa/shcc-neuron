<?php

class httpResponse {

    const MODE_JSON=1;
    const MODE_HTML=2;
    const MODE_HTML_POPUP=3;

    private static $template;
    private static $last_modified;
    private static $etag;
    private static $title;
    private static $popup_id=0;
    private static $mode;

    # Ответ HTML
    public static function getTemplate() {
        if (is_null(self::$template)) {
            self::$template=new Templates\ContentPage;
        }
        return self::$template;
    }

    public static function setTemplate(string $template) {
        self::$template=new $template;
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
        self::$mode=self::MODE_HTML_POPUP;
        $template=self::getTemplate();
        $template->title=$title;
        $template->site_info=Settings::get('site');
        if (!is_null(self::$last_modified)) {
            header("Last-Modified: ".substr(gmdate('r', self::$last_modified), 0, -5).'GMT');
        }
        if (!is_null(self::$etag)) {
            header("ETag: ".self::$etag);
        }
        self::disableBrowserCache();
        $template->Header();
        $notification=filter_input(INPUT_COOKIE, 'notification');
        if ($notification) {
            setcookie('notification', '', time()-3600, '/');
            $template->Popup('popupNotify', $notification, 'Информация');
        }
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
        $template->Popup('popupMessage'.self::$popup_id, $message, $title, $style);
        self::$popup_id++;
    }

    public static function showMessagePage(string $message, string $title, string $style=null) {
        $template=new \Templates\Message();
        $template->style=$style;
        $template->title=$title;
        $template->site_info=Settings::get('site');
        $template->message=$message;
        self::disableBrowserCache();
        $template->show();
    }

    public static function showError(string $message) {
        switch (self::$mode) {
            case self::MODE_JSON:
                self::json(['error'=>$message]);
                exit;
            case self::MODE_HTML_POPUP:
                self::showPopup($message, 'Ошибка', 'danger');
                self::showHtmlFooter();
                return;
            default:
                self::showMessagePage($message, 'Ошибка', 'danger');
        }
        exit;
    }

    public static function showInformation(string $message) {
        switch (self::$mode) {
            case self::MODE_JSON:
                self::json(['error'=>$message]);
                exit;
            case self::MODE_HTML_POPUP:
                self::showPopup($message, 'Информация', 'info');
                return;
            default:
                self::showMessagePage($message, 'Информация', 'info');
        }
    }

    public static function showAccessError(bool $is_logged) {
        switch (self::$mode) {
            case self::MODE_JSON:
                self::json(['error'=>$is_logged?'Доступ запрещён.':'Не выполнен вход.']);
                exit;
            default:
                if($is_logged) {
                    self::showError('Доступ запрещён.');
                } else {
                    self::showLoginForm(getenv('REQUEST_METHOD')=='GET'?getenv('REQUEST_URI'):'/');
                }
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
    public static function HtmlException($ex) {
        if (Settings::get('debug', false)) {
            $message='<pre>'.$ex.'</pre>';
        } else {
            error_log($ex, 0);
            $message='Произошла программная ошибка на сервере.';
        }
        self::showError($message);
    }

    public static function JsonException($ex) {
        error_log($ex, 0);
        self::showError('Произошла программная ошибка на сервере.');
    }

    # Установка режимов и подключение обработчиков ошибок
    public static function setModeHtml() {
        self::$mode=self::MODE_HTML;
        set_exception_handler([__CLASS__, 'HtmlException']);
    }

    public static function setModeJson() {
        self::$mode=self::MODE_JSON;
        set_exception_handler([__CLASS__, 'JsonException']);
    }

}
