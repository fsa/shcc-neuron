<?php

require_once 'autoloader.php';

$yandex=new Yandex\TtsApi(\Settings::get('yandex')->tts_api_key);
#jane|oksana|alyss|omazh|zahar|ermil
$yandex->setSpeaker('oksana');
#good|neutral|evil
$yandex->setEmotion('evil');
$yandex->say('Новосибирское время 23 часа');
$yandex->say('Температура воздуха +16 градусов цельсия');