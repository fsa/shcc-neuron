<?php
if(!isset($action)) {die;}
$api_key=filter_input(INPUT_POST,'api_key');
$voice=filter_input(INPUT_POST,'voice');
$emotion=filter_input(INPUT_POST,'emotion');
SmartHome\Vars::set('Yandex.TTS.Key', $api_key);
SmartHome\Vars::set('Yandex.TTS.Voice', $voice);
SmartHome\Vars::set('Yandex.TTS.Emotion', $emotion);
$yandex=new Yandex\TtsApi($api_key);
$yandex->setSpeaker($voice);
$yandex->setEmotion($emotion);
file_put_contents($tts_file,serialize($yandex));
httpResponse::storeNotification('В качестве синтезатора речи установлен Yandex SpeechKit. Голос '.$voice.', эмоциональная окраска голоса '.$emotion.'. Для вступления изменений в силу необходимо перезапустить демона TTS.');
httpResponse::redirection('../');
