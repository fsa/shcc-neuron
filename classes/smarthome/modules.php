<?php

namespace SmartHome;

use DB;

class Modules {

    public static function getActiveDaemons() {
        $stmt=DB::query('SELECT name, namespace FROM modules WHERE daemon_disabled=0 AND disabled=0');
        $daemons=$stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $daemons;
    }

}
