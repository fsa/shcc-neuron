<?php

namespace Auth;

use DB,
    PDO;

class Session {
    public static function start(UserInterface $user, int $timeout=2592000) {
        # Установить сессионную cookie
        return;
    }

    public static function refresh() {
        return null;
    }
    
    public static function destroy() {
        # Уничтожить запись о сессии и сессионную cookie
        return;
    }
}
