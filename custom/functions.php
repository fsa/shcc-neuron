<?php

/**
 * Не редактируйте данный файл. Он может быть изменён при обновлении системы.
 */
$night = boolval(getVar('System:NightMode'));
$security = boolval(getVar('System:SecurityMode'));
$mute = boolval($night or $security or getVar('System:SoundMute'));
$minute = date('i');
$hour = date('H');
$time = date('H:i');

function say($text, $priority = 0)
{
    global $mute;
    if ($mute) {
        SmartHome::tts()->addLogMessage($text);
        return;
    }
    SmartHome::tts()->addMessage($text);
}

function telegram($text, $priority = 0)
{
    $telegram = App::getSettings('telegram');
    if (!$telegram or !isset($telegram['log_channel'])) {
        return;
    }
    FSA\Telegram\Query::init($telegram);
    $api = new FSA\Telegram\SendMessage($telegram['log_channel'], $text);
    $api->disable_notification = ($priority == 0);
    $api->httpPostJson();
}

function getDevice($name)
{
    return SmartHome::getDevice($name);
}

function setDevice($name, $device)
{
    SmartHome::setDevice($name, $device);
}

function getVar($name)
{
    return App::getVar($name);
}

function setVar($name, $value)
{
    App::setVar($name, $value);
}

function getJson($name)
{
    return App::getVarJson($name);
}

function setJson($name, $object)
{
    App::setVarJson($name, $object);
}

function log_info($message)
{
    syslog(LOG_INFO, $message);
}

function log_debug($message)
{
    syslog(LOG_DEBUG, $message);
}

function log_error($message)
{
    syslog(LOG_ERR, $message);
}
