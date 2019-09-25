<?php
if(!isset($action)) {die;}

if(file_exists($tts_file)) {
    if(unlink($tts_file)) {
        HTML::storeNotification('Отключение TTS', 'Конфигурация синтезатора голоса удалена. Синтез речи отключен.');
    } else {
        throw new AppException('Не удалось удалить файл конфигурации.');
    }
} else {
    HTML::storeNotification('Отключение TTS', 'Файл конфигурации не найден. Синтез речи не был включен.');
}
httpResponse::redirect('../');