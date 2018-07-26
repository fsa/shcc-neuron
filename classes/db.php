<?php

class DB {

    private static $_instance=null;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    public static function getInstance(): PDO {
        if (self::$_instance) {
            return self::$_instance;
        }
        $config=\Settings::get('pdo');
        self::$_instance=new PDO($config->dsn,$config->username,$config->password);
        self::$_instance->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        foreach ($config->init AS $query) {
            self::$_instance->query($query);
        }
        return self::$_instance;
    }

    public static function isConnected(): bool {
        return !is_null(self::$_instance);
    }
    
    public static function insert($table,$values) {
        $keys=array_keys($values);
        $stmt=self::prepare('INSERT INTO '.$table.' ('.join(',',$keys).') VALUES (:'.join(',:',$keys).')');
        $stmt->execute($values);
        return self::lastInsertId();
    }

    public static function update($table,$values,$index='id') {
        $keys=array_keys($values);
        $i=array_search($index,$keys);
        if ($i!==false) {
            unset($keys[$i]);
        }
        foreach ($keys as &$key) {
            $key=$key.'=:'.$key;
        }
        $stmt=self::prepare('UPDATE '.$table.' SET '.join(',',$keys).' WHERE '.$index.'=:'.$index);
        return $stmt->execute($values);
    }

    public static function __callStatic($name,$args) {
        $callback=array(self::getInstance(),$name);
        return call_user_func_array($callback,$args);
    }

}
