<?php

require_once 'autoloader.php';

$yandex=new Yandex\TtsApi(\Settings::get('yandex')->tts_api_key);
#jane|oksana|alyss|omazh|zahar|ermil
$yandex->setSpeaker('jane');
#good|neutral|evil
$yandex->setEmotion('neutral');
file_put_contents(__DIR__.'/../config/tts.conf',serialize($yandex));
