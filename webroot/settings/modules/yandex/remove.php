<?php
if(!isset($action)) {die;}

SmartHome\Module\Tts\Settings::dropProvider();
httpResponse::storeNotification('Конфигурация синтезатора голоса удалена. Синтез речи отключен.');
httpResponse::redirection('../');
