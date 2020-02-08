<?php

namespace Widgets;

use HTML,
    SmartHome\Vars;

class SystemState {

    public static function show() {
        $state=[];
        if (Vars::get('System.NightMode')) {
            $state[]='Включен ночной режим.';
        }
        if (Vars::get('System.SecurityMode')) {
            $state[]='Включен режим охраны.';
        }
        if (sizeof($state)==0) {
            $state[]='Система работает в обычном режиме.';
        }
        HTML::showCard('Состояние системы', join('<br>', $state));
    }

}
