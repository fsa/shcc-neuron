<?php

namespace Auth;

use DB,
    PDO,
    Settings;

class Fail2Ban {

    public static function addFail($login) {
        if (Settings::get('fail2ban', false)) {
            return;
        }
        $ip=getenv('REMOTE_ADDR');
        $s=DB::prepare('INSERT INTO auth_fail2ban (login, ip, fail_time) VALUES (?,?,NOW())');
        $s->execute([$login, $ip]);
    }

    public static function ipIsBlocked(): bool {
        if (Settings::get('fail2ban', false)) {
            return false;
        }
        $ip=getenv('REMOTE_ADDR');
        $s=DB::prepare("SELECT count(*) FROM auth_fail2ban WHERE fail_time+INTERVAL '5 minutes'>NOW() AND ip=?");
        $s->execute([$ip]);
        $count=$s->fetch(PDO::FETCH_COLUMN);
        if ($count>50) {
            return true;
        }
        return false;
    }

    public static function loginIsBlocked($login): bool {
        if (Settings::get('fail2ban', false)) {
            return false;
        }
        $s=DB::prepare("SELECT count(*) FROM auth_fail2ban WHERE fail_time+INTERVAL '5 minutes'>NOW() AND login=?");
        $s->execute([$login]);
        $count=$s->fetch(PDO::FETCH_COLUMN);
        if ($count>500) {
            return true;
        }
        return false;
    }

}
