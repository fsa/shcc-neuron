<?php

namespace Auth;

use DB,
    PDO,
    Settings;

class Session {

    public static function start(UserEntity $user) {
        $uid=self::genRandomString(32);
        $token=self::genRandomString(32);
        self::setUidCookie($uid);
        self::setTokenCookie($token);
        self::dbInsertSession($uid, $token, $user);
        return;
    }

    public static function refresh(): ?UserEntity {
        $uid=self::getUidCookie();
        $token=self::getTokenCookie();
        if(!$uid) {
            return null;
        }
        $session=self::dbSelectSession($uid);
        if(!$session) {
            return null;
        }
        if($session->token!=$token) {
            self::dbDeleteSession($uid);
            self::dropCookie();
            return null;
        }
        $new_token=self::genRandomString(32);
        self::dbUpdateSession($uid, $new_token);
        self::setTokenCookie($new_token);
        return UserEntity::jsonUnserialize($session->user_entity);
    }

    public static function destroy(): bool {
        $uid=self::getUidCookie();
        $token=self::getTokenCookie();
        if(!$uid) {
            return false;
        }
        self::dbDeleteSession($uid);
        self::dropCookie();
        return true;
    }

    private static function genRandomString($length): string {
        $symbols='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
        $max_index=strlen($symbols)-1;
        $string='';
        for ($i=0; $i<$length; $i++) {
            $index=rand(0, $max_index);
            $string.=$symbols[$index];
        }
        return $string;
    }
    
    private static function dbInsertSession(string $uid, string $token, UserEntity $user) {
        $session=Settings::get('session');
        $s=DB::prepare('INSERT INTO auth_sessions (uid, token, user_entity, expires, ip, browser) VALUES (?,?,?,?,?,?)');
        $s->execute([$uid, $token, json_encode($user), date('c',time()+$session->time), getenv('REMOTE_ADDR'), getenv('HTTP_USER_AGENT')]);
    }
    
    private static function dbUpdateSession(string $uid, string $new_token) {
        $session=Settings::get('session');
        $s=DB::prepare('UPDATE auth_sessions SET token=?, expires=?, ip=?, browser=? WHERE uid=?');
        $s->execute([$new_token, date('c',time()+$session->time), getenv('REMOTE_ADDR'), getenv('HTTP_USER_AGENT'), $uid]);
    }

    private static function dbSelectSession($uid) {
        $s=DB::prepare('SELECT * FROM auth_sessions WHERE uid=? AND expires>NOW()');
        $s->execute([$uid]);
        return $s->fetch(PDO::FETCH_OBJ);
    }

    private static function dbDeleteSession($uid) {
        $s=DB::prepare('DELETE FROM auth_sessions WHERE uid=?');
        $s->execute([$uid]);
    }
    
    private static function dbDeleteExpiredSession() {
        DB::query('DELETE FROM auth_sessions WHERE expires<NOW()');
    }

    private static function setUidCookie(string $uid): void {
        $cookie=Settings::get('session');
        setcookie($cookie->uid, $uid, time()+$cookie->time, $cookie->path, getenv('HTTP_HOST'), false, true);
    }
    
    private static function setTokenCookie(string $token): void {
        $cookie=Settings::get('session');
        setcookie($cookie->token, $token, time()+$cookie->time, $cookie->path, getenv('HTTP_HOST'), false, true);
    }
    
    private static function getUidCookie(): ?string {
        $cookie=Settings::get('session');
        return filter_input(INPUT_COOKIE, $cookie->uid);
    }

    private static function getTokenCookie(): ?string {
        $cookie=Settings::get('session');
        return filter_input(INPUT_COOKIE, $cookie->token);
    }

    private static function dropCookie(): void {
        $cookie=Settings::get('session');
        setcookie($cookie->uid,'',time()-3600, $cookie->path, getenv('HTTP_HOST'), false, true);
        setcookie($cookie->token,'',time()-3600, $cookie->path, getenv('HTTP_HOST'), false, true);
    }

}
