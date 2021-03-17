<?php

namespace Auth;

use DB,
    PDO,
    PDOStatement;

class Admin {

    public static function getUsersList(): PDOStatement {
        $s=DB::query('SELECT * FROM auth_users ORDER BY login');
        $s->setFetchMode(PDO::FETCH_OBJ);
        return $s;
    }

}
