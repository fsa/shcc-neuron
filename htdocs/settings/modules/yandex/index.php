<?php

require_once '../../../common.php';
Auth\Session::grantAccess([]);
$tts_file='../../../../config/tts.conf';
$action=filter_input(INPUT_POST,'action');
switch ($action) {
    case null:
    case false:
        break;
    case 'setup':
        require 'save.php';
        break;
    case 'remove':
        require 'remove.php';
        break;
    default:
        httpResponse::showError('Неверное действие');
}

if(file_exists($tts_file)) {
    $tts=unserialize(file_get_contents($tts_file));
    if($tts instanceof Yandex\TtsApi) {
        $params=$tts->getParams();
    }
}
if(!isset($params)) {
    $params=[
        'key'=>SmartHome\Vars::get('yandex.tts_key')??'',
        'speaker'=>SmartHome\Vars::get('yandex.tts_voice')??'oksana',
        'emotion'=>SmartHome\Vars::get('yandex.tts_emotion')??'neutral'
    ];
}
use Templates\Forms;
httpResponse::showHtmlHeader('Яндекс');
httpResponse::showNavPills('../../%s/', require '../../sections.php', 'modules');
?>
<p><a href="../">Вернуться к списку модулей</a></p>
<hr>
<form method="POST" action="./">
<?php
Forms::inputString('api_key', $params['key'], 'Ключ API:');
Forms::inputSelectArray('voice', $params['speaker'], 'Голос', ['jane'=>'Джейн (жен.)', 'oksana'=>'Оксана (жен.)', 'alyss'=>'Элис (жен.)', 'omazh'=>'Омаж (жен.)', 'zahar'=>'Захар (муж.)', 'ermil'=>'Ермил (муж.)']);
Forms::inputSelectArray('emotion', $params['emotion'], 'Эмоциональная окраска голоса', ['good'=>'радостный, доброжелательный', 'evil'=>'раздраженный', 'neutral'=>'нейтральный']);
Forms::submitButton('Установить синтезатор TTS', 'setup');
Forms::submitButton('Отключить синтез речи', 'remove', 'btn-danger');
?>
</form>
<?php
httpResponse::showHtmlFooter();
