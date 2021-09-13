<?php

class DBRedis {

    private static $redis=null;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    public static function getInstance(): Redis {
        if (self::$redis) {
            return self::$redis;
        }
        $host=getenv('REDIS_HOST')?getenv('REDIS_HOST'):'127.0.0.1';
        $port=getenv('REDIS_PORT')?getenv('REDIS_PORT'):6379;
        self::$redis=new Redis();
        self::$redis->connect($host, $port);
        if(getenv('REDIS_AUTH')) {
            if(!self::$redis->auth(getenv('REDIS_AUTH'))) {
                throw new AppException('Redis Auth Failed');
            }
        }
        return self::$redis;
    }

    public static function isConnected(): bool {
        return !is_null(self::$redis);
    }

    public static function disconnect(): void {
        self::$redis=null;
    }

    public static function __callStatic($name, $args) {
        $callback=array(self::getInstance(), $name);
        return call_user_func_array($callback, $args);
    }

}
