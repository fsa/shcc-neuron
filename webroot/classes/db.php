<?php

class DB {

    private static $pdo=null;
    private static $origExceptionHandler;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    public static function getInstance(): PDO {
        if (self::$pdo) {
            return self::$pdo;
        }
        $url=getenv('DATABASE_URL');
        if (!$url) {
            throw new Exception('Database is not configured.');
        }
        $db=parse_url($url);
        self::$pdo=new PDO("pgsql:".sprintf(
                "host=%s;port=%s;user=%s;password=%s;dbname=%s",
                $db["host"], $db["port"]??5432, $db["user"], $db["pass"], ltrim($db["path"], "/")));
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $tz=getenv('TZ');
        if ($tz) {
            self::$pdo->query("SET TIMEZONE=\"$tz\"");
        }
        return self::$pdo;
    }

    public static function isConnected(): bool {
        return !is_null(self::$pdo);
    }

    public static function insert($table, $values, $index='id') {
        $keys=array_keys($values);
        $stmt=self::prepare('INSERT INTO '.$table.' ('.join(',', $keys).') VALUES (:'.join(',:', $keys).') RETURNING '.$index);
        $stmt->execute($values);
        return $stmt->fetchColumn();
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

    public static function beginTransaction(): bool {
        $result=self::getInstance()->beginTransaction();
        self::$origExceptionHandler=set_exception_handler([self::class, 'Exception']);
        return $result;
    }

    public static function commit(): bool {
        $result=self::getInstance()->commit();
        set_exception_handler(self::$origExceptionHandler);
        self::$origExceptionHandler=null;
        return $result;
    }

    public static function rollback(): bool {
        $result=self::getInstance()->rollBack();
        set_exception_handler(self::$origExceptionHandler);
        self::$origExceptionHandler=null;
        return $result;
    }

    public static function prepare(string $statement, array $driver_options=[]): PDOStatement {
        return self::getInstance()->prepare($statement, $driver_options);
    }

    public static function disconnect(): void {
        self::$pdo=null;
    }

    public static function __callStatic($name, $args) {
        $callback=array(self::getInstance(), $name);
        return call_user_func_array($callback, $args);
    }

    public static function Exception($ex): void {
        $origExceptionHandler=self::$origExceptionHandler;
        self::rollback();
        call_user_func($origExceptionHandler, $ex);
    }

}
