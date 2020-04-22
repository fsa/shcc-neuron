<?php

namespace SmartHome;

use AppException;

class Daemons {

    public static function getActive() {
        return self::getVar();
    }

    public static function enable($name) {
        $daemons=self::getVar();
        $daemon_class=$name.'\\Daemon';
        if(!class_exists($daemon_class)) {
            throw new AppException('Ошибка при активации демона. Класс демона не найден!');
        }
        $daemons[$name]=$daemon_class;
        self::setVar($daemons);
    }

    public static function disable($name) {
        $daemons=self::getVar();
        if(array_search($name, $daemons)!==false) {
            throw new AppException('Демон не требует отключения, т.к. уже отключен.');
        }
        unset($daemons[$name]);
        self::setVar($daemons);
    }

    private static function getVar() {
        $daemons=Vars::getJson('System.Daemons', true);
        if(is_array($daemons)) {
            return $daemons;
        }
        return [];
    }

    private static function setVar(array $value) {
        Vars::setJson('System.Daemons', $value);
    }

}