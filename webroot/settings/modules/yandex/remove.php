<?php
use FSA\Neuron\HttpResponse;
if(!isset($action)) {die;}

SmartHome\Module\Tts\Settings::dropProvider();
HttpResponse::storeNotification('Конфигурация синтезатора голоса удалена. Синтез речи отключен.');
HttpResponse::redirection('../');
