<?php

class HTML {

    private static $_instance=null;
    private static $keywords=[];
    private static $timestamp=null;
    private static $title;
    private static $header_shown=false;
    private static $root_dir='./';

    private function __clone() {
        
    }

    private function __construct() {
        
    }

    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance=new Templates\HTML();
            #TODO: вычислить каталог
        }
        return self::$_instance;
    }

    public static function setTemplate($name) {
        $name='Templates\\'.$name;
        self::$_instance=new $name();
    }

    public static function setVar($var, $value) {
        $html=self::getInstance();
        $html->$var=$value;
    }

    public static function getRootDir() {
        return self::$root_dir;
    }

    public static function setRootDir($dir) {
        self::$root_dir=$dir;
    }

    public static function getTitle() {
        return self::$title;
    }

    public static function showPageHeader($title=null) {
        $html=self::getInstance();
        self::$header_shown=true;
        #$html->username=\User\Auth::getName();
        if (!self::$timestamp) {
            self::$timestamp=filemtime(getenv('SCRIPT_FILENAME'));
        }
        header("HTTP/1.0 200 OK");
        header("Content-Type: text/html; charset=utf-8");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s \G\M\T", self::$timestamp));
        self::disableBrowserCache();
        $html->title=($title?$title.' :: ':'').Settings::get('site')->title;
        self::$title=$title?$title:Settings::get('site')->title;
        $html->Header();
    }

    public static function showTitleH1() {
        $html=self::getInstance();
        $html->title=self::$title;
        $html->TitleH1();
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

    public static function addLeafletJs() {
        self::addHeader('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        self::addHeader('<link rel="stylesheet" href="/libs/leaflet/leaflet.css">');
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
        $html->style='danger';
        $html->title=$title;
        $html->site_title=Settings::get('site')->title;
        $html->message=$message;
        $html->show();
    }

    public static function showPopup($title, $message) {
        $html=new \Templates\Popup();
        $html->title=$title;
        $html->message="<h1>$title</h1>".PHP_EOL."$message";
        $html->show();
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
        $html->site_title=Settings::get('site')->title;
        $html->show();
        exit;
    }

    public static function disableBrowserCache() {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Expires: ".date("r"));
        header("Pragma: no-cache"); // HTTP/1.0
    }

    public static function isSecure() {
        return getenv('HTTPS')!==false;
    }

    public static function redirect($location) {
        header("Location: $location");
        echo "<a href=\"$location\">$location</a>";
        exit;
    }

    public static function redirect301($location) {
        header(getenv('SERVER_PROTOCOL')." 301 Moved Permanently", true);
        header("Location: $location");
        exit;
    }

    public static function error404() {
        header(getenv('SERVER_PROTOCOL')." 404 Not Found", true);
        echo 'File not found';
        exit;
    }

    public static function showLoginForm($url=false) {
        $html=new \Templates\Login();
        $html->title='Вход в систему';
        $html->root_dir=self::$root_dir;
        if ($url) {
            $html->redirect_uri=$url;
            $html->url='/login/';
        }
        Header("Content-Type: text/html; charset=utf-8");
        self::disableBrowserCache();
        $html->show();
        unset($html);
    }

    public static function Exception($ex) {
        DB::rollback();
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

    public static function __callStatic($name, $args) {
        if (substr($name, 0, 4)!='show') {
            throw new Exception('Вызван несуществующий метод '.$name);
        }
        $method_name=substr($name, 4);
        $callback=array(self::getInstance(), $method_name);
        return call_user_func_array($callback, $args);
    }

}
