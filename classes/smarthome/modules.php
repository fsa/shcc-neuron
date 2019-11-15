<?php

namespace SmartHome;

use DB, PDO;

class Modules {

    public static function getActiveDaemons() {
        $stmt=DB::query('SELECT name, namespace FROM modules WHERE daemon=true AND disabled=false');
        $daemons=$stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $daemons;
    }
    
    public static function getModuleList() {
        $stmt=DB::query('SELECT id, name FROM modules WHERE disabled=false');
        return $stmt;       
    }
    
    public static function getModuleIdByName($name) {
        $stmt=DB::prepare('SELECT id FROM modules WHERE name=?');
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public static function getModules() {
        $stmt=DB::query("SELECT id, name, namespace, description, daemon, settings, disabled FROM modules ORDER BY name");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        return $stmt;
    }

    public static function disableModule(int $id, bool $disable) {
        $stmt=DB::prepare('UPDATE modules SET disabled=? WHERE id=? RETURNING namespace');
        $stmt->execute([$disable?'true':'false', $id]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}
