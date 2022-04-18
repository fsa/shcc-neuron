<?php

if (!isset($action)) {
    die;
}

SmartHome\Module\Tts\Settings::dropProvider();
App::response()->storeNotification('Конфигурация синтезатора голоса удалена. Синтез речи отключен.');
App::response()->redirection('../');
