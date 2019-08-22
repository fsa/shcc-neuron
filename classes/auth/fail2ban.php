<?php

namespace Auth;

use DB,
    PDO;

class Fail2Ban {

    public static function addFail($login) {
        $proxy=\Settings::get('trusted_proxy_x_real_ip');
        if (is_null($proxy)) {
            return;
        }
        $ip=self::getClientIp($proxy);
        $s=DB::prepare('INSERT INTO auth_fail2ban (login, ip, fail_time) VALUES (?,?,NOW())');
        $s->execute([$login, $ip]);
    }

    public static function ipIsBlocked(): bool {
        $proxy=\Settings::get('trusted_proxy_x_real_ip');
        if (is_null($proxy)) {
            return false;
        }
        $ip=self::getClientIp($proxy);
        $s=DB::prepare("SELECT count(*) FROM auth_fail2ban WHERE fail_time+INTERVAL '5 minutes'>NOW() AND ip=?");
        $s->execute([$ip]);
        $count=$s->fetch(PDO::FETCH_COLUMN);
        if ($count>50) {
            return true;
        }
        return false;
    }

    public static function loginIsBlocked($login): bool {
        $s=DB::prepare("SELECT count(*) FROM auth_fail2ban WHERE fail_time+INTERVAL '5 minutes'>NOW() AND login=?");
        $s->execute([$login]);
        $count=$s->fetch(PDO::FETCH_COLUMN);
        if ($count>50) {
            return true;
        }
        return false;
    }

    public static function getClientIp($proxy=[]) {
        $ip=getenv('REMOTE_ADDR');
        if (array_search($ip, $proxy)!==false) {
            $ip=getenv('HTTP_X_REAL_IP');
        }
        return $ip;
    }

}
