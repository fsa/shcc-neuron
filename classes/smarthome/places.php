<?php

namespace SmartHome;

use DB,
    PDO;

class Places {

    public static function getPlaceList() {
        $s=DB::query('SELECT NULL,"Не установлено" UNION SELECT id, name FROM places');
        return $s;
    }

}
