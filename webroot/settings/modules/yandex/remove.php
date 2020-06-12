<?php
if(!isset($action)) {die;}

if(file_exists($tts_file)) {
    if(unlink($tts_file)) {
        httpResponse::storeNotification('Конфигурация синтезатора голоса удалена. Синтез речи отключен.');
    } else {
        httpResponse::showError('Не удалось удалить файл конфигурации.');
    }
} else {
    httpResponse::storeNotification('Файл конфигурации не найден. Синтез голоса не был включен.');
}
httpResponse::redirection('../');
