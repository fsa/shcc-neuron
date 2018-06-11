<?php

class HTML {

    private static $_instance=null;

    private function __clone() {
        
    }

    private function __construct() {
        
    }

    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance=new \Templates\HTML();
            self::$_instance->header='';
        }
        return self::$_instance;
    }

    public static function setTemplate($template) {
        self::$_instance=new $template;
    }

    public static function getTitle() {
        $html=self::getInstance();
        return $html->title;
    }

    public static function showPageHeader($title=null,$timestamp=false) {
        $html=self::getInstance();
        if (!$timestamp) {
            $timestamp=filemtime(filter_input(INPUT_SERVER,'SCRIPT_FILENAME'));
        }
        header("HTTP/1.0 200 OK");
        header("Content-Type: text/html; charset=utf-8");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s \G\M\T",$timestamp));
        self::disableBrowserCache();
        $html->title=$title;
        $html->Header();
    }

    public static function showPageFooter() {
        $html=self::getInstance();
        $html->Footer();
    }

    public static function addHeader($data) {
        $html=self::getInstance();
        $html->header.=$data.PHP_EOL;
    }

    public static function addDescription($description) {
        self::addHeader("<meta name=\"description\" content=\"$description\">");
    }

    public static function addKeyword($keywords) {
        self::addHeader("<meta name=\"keywords\" content=\"$keywords\">");
    }

    public static function showException($message) {
        $title='Ошибка';
        $message="<p>$message</p>".PHP_EOL;
        if (headers_sent()) {
            self::showPopup($title,$message);
            self::showPageFooter();
            return;
        }
        $html=new \Templates\Message();
        $html->style='danger';
        $html->title=$title;
        $html->message=$message;
        $html->show();
    }

    public static function showPopup($title,$message) {
        $html=new \Templates\Popup();
        $html->title=$title;
        $html->message="<h1>$title</h1>".PHP_EOL."$message";
        $html->show();
    }

    public static function showNotification($title,$message,$url=null) {
        $html=new \Templates\Message();
        $message="<p>$message</p>".PHP_EOL;
        if (!is_null($url)) {
            $html->header='<meta http-equiv="Refresh" content="5;URL='.$url.'">';
            $message.="<p><a href=\"$url\">Продолжить.</a></p>";
        }
        Header("Content-Type: text/html; charset=utf-8");
        self::disableBrowserCache();
        $html->title=$title;
        $html->message=$message;
        $html->show();
        die;
    }

    public static function disableBrowserCache() {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Expires: ".date("r"));
        header("Pragma: no-cache"); // HTTP/1.0
    }

    public static function isSecure() {
        return getenv('HTTPS')!==false;
    }

    public static function redirect($location,$response_code=303) {
        header("Location: $location",true,$response_code);
        echo "<a href=\"$location\">$location</a>";
        exit;
    }

    public static function error404() {
        header(filter_input(INPUT_SERVER,'SERVER_PROTOCOL')." 404 Not Found",true,404);
        echo 'File not found';
        exit;
    }

    public static function showLoginForm($url=false,$redirect_url=false,$title=false) {
        $html=new \Templates\Login();
        $html->title=$title?$title:'Вход в систему';
        $html->url=$url?$url:'/login/';
        $html->redirect_url=$redirect_url?$redirect_url:'/';
        Header("Content-Type: text/html; charset=utf-8");
        self::disableBrowserCache();
        $html->show();
    }

    public static function Exception($ex) {
        if ($ex instanceof AppException) {
            $message=$ex->getMessage();
        } else {
            $message=$ex;
            if (ini_get('display_errors')!=1) {
                error_log($ex,0);
                $message='Извините. Произошла программная ошибка. Если вы часто видите это сообщение, сообщите о нём администратору сайта.';
            }
        }
        self::showException($message);
    }

}
