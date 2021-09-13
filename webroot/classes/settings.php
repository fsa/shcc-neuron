<?php

class Settings {

    private static $_instance=null;
    private $settings;

    private function __clone() {
        
    }

    private function __construct() {
        $this->settings=require __DIR__.'/../../settings.php';
    }

    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance=new self;
        }
        return self::$_instance;
    }

    public static function get(string $name,$default_value=null) {
        $s=self::getInstance();
        if (isset($s->settings[$name])) {
            return $s->settings[$name];
        }
        return $default_value;
    }

}
