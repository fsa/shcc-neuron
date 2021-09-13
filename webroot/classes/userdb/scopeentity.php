<?php

namespace UserDB;

use DB, PDO;

class ScopeEntity extends \Entity {

    public $name;
    public $description;

    public static function getScopes() {
        $s=DB::query('SELECT name, description FROM user_scopes ORDER BY name');
        return $s->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}

