<?php

namespace Auth;

use DB,
    PDO;

class Fail2Ban {

    public static function addFail($login) {
        $fail2ban=\Settings::get('fail2ban');
        if (is_null($fail2ban)) {
            return;
        }
        $ip=self::getClientIp(isset($fail2ban->trusted_proxy_x_real_ip)?$fail2ban->trusted_proxy_x_real_ip:[]);
        $s=DB::prepare('INSERT INTO auth_fail2ban (login, ip, fail_time) VALUES (?,?,NOW())');
        $s->execute([$login, $ip]);
    }

    public static function ipIsBlocked(): bool {
        $fail2ban=\Settings::get('fail2ban');
        if (is_null($fail2ban)) {
            return false;
        }
        $ip=self::getClientIp(isset($fail2ban->trusted_proxy_x_real_ip)?$fail2ban->trusted_proxy_x_real_ip:[]);
        $s=DB::prepare("SELECT count(*) FROM auth_fail2ban WHERE fail_time+INTERVAL '5 minutes'>NOW() AND ip=?");
        $s->execute([$ip]);
        $count=$s->fetch(PDO::FETCH_COLUMN);
        if ($count>50) {
            return true;
        }
        return false;
    }

    public static function loginIsBlocked($login): bool {
        $fail2ban=\Settings::get('fail2ban');
        if (is_null($fail2ban)) {
            return false;
        }
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
