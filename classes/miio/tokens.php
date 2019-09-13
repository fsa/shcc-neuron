<?php

namespace miIO;

use DB,
    PDO;

class Tokens {

    public static function updateToken(string $uid, string $token): string {
        DB::beginTransaction();
        $s=DB::prepare('SELECT id FROM miio_tokens WHERE uid=?');
        $s->execute([$uid]);
        $id=$s->fetch(PDO::FETCH_COLUMN);
        $s->closeCursor();
        if ($id) {
            $s=DB::prepare('UPDATE miio_tokens SET token=? WHERE uid=?');
            $s->execute([$token, $uid]);
            DB::commit();
            return 'Токен изменён';
        } else {
            $s=DB::prepare('INSERT INTO miio_tokens (uid, token) VALUES (?,?)');
            $s->execute([$uid, $token]);
            DB::commit();
            return 'Токен добавлен';
        }
    }

    public static function getTokens(): array {
        $s=DB::query('SELECT uid, token FROM miio_tokens');
        return $s->fetchAll(PDO::FETCH_KEY_PAIR);
    }

}
