<?php

class HTML {

    private static $_instance=null;
    private static $keywords=[];
    private static $timestamp=null;
    private static $title;
    private static $header_shown=false;

    private function __clone() {
        
    }

    private function __construct() {
        
    }

    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance=new Templates\HTML();
        }
        return self::$_instance;
    }

    public static function setTemplate($name) {
        $name='Templates\\'.$name;
        self::$_instance=new $name();
    }

    public static function getTitle() {
        return self::$title;
    }

    public static function showPageHeader($title=null) {
        $html=self::getInstance();
        self::$header_shown=true;
        if (!self::$timestamp) {
            self::$timestamp=filemtime(getenv('SCRIPT_FILENAME'));
        }
        $html->title=($title?$title.' :: ':'').Settings::get('site')->title;
        self::$title=$title?$title:Settings::get('site')->title;
        header("HTTP/1.0 200 OK");
        header("Content-Type: text/html; charset=utf-8");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s \G\M\T", self::$timestamp));
        self::disableBrowserCache();
        $html->Header();
        $title_notification=filter_input(INPUT_COOKIE,'message_title');
        if(!$title_notification) {
            return;
        }
        $text_notification=filter_input(INPUT_COOKIE,'message_text');
        setcookie('message_title','',time()-3600,'/');
        setcookie('message_text','',time()-3600,'/');
        $html->Popup($title_notification, $text_notification, 'bg-info text-white');
    }

    public static function showPageFooter() {
        $html=self::getInstance();
        $html->Footer();
    }

    protected function showMetaKeywords() {
        if (sizeof($this->keywords)==0) {
            return;
        }
        echo '<meta name="keywords" content="'.join(',', $this->keywords).'">'.PHP_EOL;
    }

    public static function setLatModified($timestamp) {
        self::$timestamp=strtotime($timestamp);
    }

    public static function addHeader($data) {
        $html=self::getInstance();
        $html->header.=$data.PHP_EOL;
    }

    public static function addDescription($description) {
        self::addHeader("<meta name=\"description\" content=\"$description\">");
    }

    public static function addKeyword($keyword) {
        self::$keywords[]=$keyword;
    }

    public static function showException($message) {
        $title='Внимание!';
        $message="<p>$message</p>".PHP_EOL;
        if (self::$header_shown) {
            self::showPopup($title, $message);
            self::showPageFooter();
            return;
        }
        $html=new \Templates\Message();
        $html->style='bg-danger';
        $html->title=$title;
        $html->site_info=Settings::get('site');
        $html->message=$message;
        $html->show();
    }

    public static function showPopup($title, $message) {
        $html=self::getInstance();
        $html->Popup($title, $message);
    }

    public static function showNotification($title, $message, $url=null) {
        $html=new \Templates\Message();
        $message="<p>$message</p>".PHP_EOL;
        if (!is_null($url)) {
            $html->header='<meta http-equiv="Refresh" content="5;URL='.$url.'">';
            $message.="<p><a href=\"$url\">Продолжить</a></p>";
        }
        Header("Content-Type: text/html; charset=utf-8");
        self::disableBrowserCache();
        $html->title=$title;
        $html->message=$message;
        $html->site_info=Settings::get('site');
        $html->show();
        exit;
    }
    
    public static function storeNotification($title, $message) {
        setcookie('message_title',$title,0,'/');
        setcookie('message_text',$message,0,'/');
    }

    public static function disableBrowserCache() {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Expires: ".date("r"));
        header("Pragma: no-cache"); // HTTP/1.0
    }

    public static function isSecure() {
        return getenv('HTTPS')!==false;
    }

    public static function showLoginForm($url=false) {
        $html=new \Templates\Login();
        $html->title='Вход в систему';
        if ($url) {
            $html->redirect_uri=$url;
            $html->url='/login/';
        }
        Header("Content-Type: text/html; charset=utf-8");
        self::disableBrowserCache();
        $html->show();
    }

    public static function __callStatic($name, $args) {
        if (substr($name, 0, 4)!='show') {
            throw new Exception('Вызван несуществующий метод '.$name);
        }
        $method_name=substr($name, 4);
        $callback=array(self::getInstance(), $method_name);
        return call_user_func_array($callback, $args);
    }

    public static function Exception($ex) {
        switch (get_class($ex)) {
            case 'AppException':
                $message=$ex->getMessage();
                break;
            default:
                $message=$ex;
                if (Settings::get('debug')!=true) {
                    error_log($message, 0);
                    $message='Извините. Произошла программная ошибка. Информация об ошибке сохранена в журнале. Если вы часто видите это сообщение, сообщите о нём администратору сайта.';
                }
        }
        self::showException($message);
    }

}
