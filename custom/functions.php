<?php
/**
 * Не редактируйте данный файл. Он может быть изменён при обновлении системы.
 */

$night=boolval(getVar('System.NightMode'));
$security=boolval(getVar('System.SecurityMode'));
$mute=boolval($night or $security or getVar('System.SoundMute'));
$minute=date('i');
$hour=date('H');
$time=date('H:i');

function say($text, $priority=0) {
    Tts\Queue::addLogMessage($text);
    global $mute;
    if ($mute) {
        return;
    }
    $tts=new Tts\Queue();
    $tts->addMessage($text);
}

function telegram($text, $priority=0) {
    $telegram=Settings::get('telegram');
    if(!$telegram or !isset($telegram['log_channel'])) {
        return;
    }
    Telegram\Query::init($telegram);
    $api=new Telegram\SendMessage($telegram['log_channel'], $text);
    $api->disable_notification=($priority==0);
    $api->httpPostJson();
}

function getDevivce($name) {
    return SmartHome\Devices::get($name);
}

function getVar($name) {
    return SmartHome\Vars::get($name);
}

function setVar($name, $value) {
    SmartHome\Vars::set($name, $value);
}

function getJson($name) {
    return SmartHome\Vars::getJson($name);
}

function setJson($name, $object) {
    SmartHome\Vars::setJson($name, $object);
}
