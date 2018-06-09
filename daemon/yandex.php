<?php

require_once 'autoloader.php';

$yandex=new Yandex\TtsApi(\Settings::get('yandex')->tts_api_key);
#jane|oksana|alyss|omazh|zahar|ermil
$yandex->setSpeaker('omazh');
#good|neutral|evil
$yandex->setEmotion('good');
$yandex->say('Проверка работы API Яндекса.');
$yandex->setEmotion('neutral');
$yandex->say('Проверка работы API Яндекса.');
$yandex->setEmotion('evil');
$yandex->say('Проверка работы API Яндекса.');