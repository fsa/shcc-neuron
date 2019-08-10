<?php

class DB {

    private static $pdo=null;
    private static $inTransaction=false;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    public static function getInstance(): PDO {
        if (self::$pdo) {
            return self::$pdo;
        }
        $config=\Settings::get('pdo');
        self::$pdo=new PDO($config->dsn, $config->username, $config->password);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        foreach ($config->init AS $query) {
            self::$pdo->query($query);
        }
        return self::$pdo;
    }

    public static function isConnected(): bool {
        return !is_null(self::$pdo);
    }

    public static function insert($table, $values) {
        $keys=array_keys($values);
        $stmt=self::prepare('INSERT INTO '.$table.' ('.join(',', $keys).') VALUES (:'.join(',:', $keys).')');
        $stmt->execute($values);
        return self::lastInsertId();
    }

    public static function update($table, $values, $index='id') {
        $keys=array_keys($values);
        $i=array_search($index, $keys);
        if ($i!==false) {
            unset($keys[$i]);
        }
        foreach ($keys as &$key) {
            $key=$key.'=:'.$key;
        }
        $stmt=self::prepare('UPDATE '.$table.' SET '.join(',', $keys).' WHERE '.$index.'=:'.$index);
        return $stmt->execute($values);
    }
    
    public static function beginTransaction() {
        self::getInstance()->beginTransaction();
        self::$inTransaction=true;
    }

    public static function rollback(): bool {
        if (is_null(self::$pdo)) {
            return true;
        }
        if (!self::$inTransaction) {
            return true;
        }
        $result=self::$pdo->rollback();
        self::$inTransaction=false;
        return $result;
    }
    
    public static function prepare(string $statement, array $driver_options=[]): PDOStatement {
        return self::getInstance()->prepare($statement,$driver_options);
    }

    public static function __callStatic($name, $args) {
        $callback=array(self::getInstance(), $name);
        return call_user_func_array($callback, $args);
    }

}
