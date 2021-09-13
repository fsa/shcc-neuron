<?php

class Logger {

    public static function log(string $name, $value, int $timestamp=null) {
        self::store('log', $name, $value, $timestamp);
    }

    # public static function emergency
    # public static function alert
    # public static function critical
    # public static function error
    # public static function warning
    # public static function notice
    # public static function info
    
    public static function debug(string $name, $value, int $timestamp=null) {
        if (Settings::get('debug', false)) {
            self::store('debug', $name, $value, $timestamp);
        }
    }

    private static function store(string $level, string $name, $value, int $timestamp=null) {
        if (!$timestamp) {
            $timestamp=time();
        }
        $data=($value instanceof string)?$value:print_r($value, true);
        error_log(date('H:i:s', $timestamp).'=>'.$data.PHP_EOL, 3, __DIR__.'/../logs/'.$name.'/'.$level.'_'.date('Y_m_d', $timestamp).'.log');
    }

}
