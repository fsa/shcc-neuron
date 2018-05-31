<?php

class Settings {

    private static $_instance=null;
    private $settings;

    private function __clone() {
        
    }

    private function __construct() {
        $settings=file_get_contents(__DIR__.'/../settings.json');
        if ($settings===false) {
            throw new Exception('Не удалось открыть файл конфигурации.');
        }
        $this->settings=json_decode($settings);
        if (is_null($this->settings)) {
            throw new Exception('Ошибка в файле конфигурации.');
        }
    }

    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance=new self;
        }
        return self::$_instance;
    }

    public static function get($name) {
        $s=self::getInstance();
        if (isset($s->settings->$name)) {
            return $s->settings->$name;
        }
        return null;
    }

}
