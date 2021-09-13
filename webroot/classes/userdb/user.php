<?php

namespace UserDB;

use DB;

class User {

    public $uuid;
    public $login;
    public $name;
    public $email;
    public $scope;

    public static function login($login, $password): ?self {
        $s=DB::prepare('SELECT uuid, password_hash FROM users WHERE (login=? OR (email IS NOT NULL AND email=?)) AND NOT COALESCE(disabled, false)');
        $s->execute([$login, $login]);
        $user=$s->fetchObject();
        if (!($user and password_verify($password, $user->password_hash))) {
            return null;
        }
        return new self($user->uuid);
    }

    public static function stmtGetAll(): \PDOStatement {
        $s=DB::query('SELECT * FROM users ORDER BY login');
        return $s;
    }

    public function __construct(string $uuid=null) {
        if (is_null($uuid)) {
            return;
        }
        $s=DB::prepare("WITH usr AS (SELECT * FROM users u WHERE uuid=? AND NOT COALESCE(disabled, false)), groups_set AS (SELECT uuid, unnest(groups) AS gid FROM usr), gscope_set AS (SELECT uuid, unnest(ug.scope) AS gscope FROM groups_set g LEFT JOIN user_groups ug ON g.gid=ug.name GROUP BY uuid, gscope), gscope AS (SELECT uuid, array_agg(gscope) AS group_scope FROM gscope_set GROUP BY uuid) SELECT json_build_object('uuid',uuid, 'login',login, 'name', name, 'email', email, 'scope', to_json(group_scope||scope)) FROM usr LEFT JOIN gscope USING (uuid)");
        $s->execute([$uuid]);
        $entity=$s->fetchColumn();
        if (!$entity) {
            return;
        }
        foreach (json_decode($entity) as $key=> $value) {
            $this->$key=$value;
        }
    }

    public function getId() {
        return $this->uuid;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getScope() {
        return $this->scope;
    }

    public function validate() {
        return isset($this->uuid);
    }

    public function getConstructorArgs() {
        return [$this->uuid];
    }

    #Fail2ban
    public static function addFail($login) {
        $ip=getenv('REMOTE_ADDR');
        $s=DB::prepare('INSERT INTO user_fail2ban (login, ip, fail_time) VALUES (?,?,NOW())');
        $s->execute([$login, $ip]);
    }

    public static function ipIsBlocked(): bool {
        $ip=getenv('REMOTE_ADDR');
        $s=DB::prepare("SELECT count(*) FROM user_fail2ban WHERE fail_time+INTERVAL '5 minutes'>NOW() AND ip=?");
        $s->execute([$ip]);
        $count=$s->fetch(PDO::FETCH_COLUMN);
        if ($count>50) {
            return true;
        }
        return false;
    }

    public static function loginIsBlocked($login): bool {
        $s=DB::prepare("SELECT count(*) FROM user_fail2ban WHERE fail_time+INTERVAL '5 minutes'>NOW() AND login=?");
        $s->execute([$login]);
        $count=$s->fetch(PDO::FETCH_COLUMN);
        if ($count>500) {
            return true;
        }
        return false;
    }

}
