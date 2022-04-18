<?php
if (!isset($action)) {
    die;
}
$key = filter_input(INPUT_POST, 'key');
$speaker = filter_input(INPUT_POST, 'speaker');
$emotion = filter_input(INPUT_POST, 'emotion');
SmartHome\Vars::set('Yandex.TTS.Key', $key);
# Проверяем корректность настроек и сохраняем корректные
$yandex = new Yandex\TtsApi(['key' => $key]);
$yandex->setSpeaker($speaker);
SmartHome\Vars::set('Yandex.TTS.Speaker', $speaker);
$yandex->setEmotion($emotion);
SmartHome\Vars::set('Yandex.TTS.Emotion', $emotion);
SmartHome\Module\Tts\Settings::setProvider(Yandex\TtsApi::class, ['key' => $key, 'speaker' => $speaker, 'emotion' => $emotion]);
App::response()->storeNotification('В качестве синтезатора речи установлен Yandex SpeechKit. Голос ' . $speaker . ', эмоциональная окраска голоса ' . $emotion . '. Для вступления изменений в силу необходимо перезапустить демона TTS.');
App::response()->redirection('../');
