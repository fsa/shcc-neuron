<?php
if(!is_array(spl_autoload_functions())) {
    set_include_path(__DIR__.'/../classes/');
    spl_autoload_extensions('.php');
    spl_autoload_register();
    date_default_timezone_set(\Settings::get('timezone'));
}

$night=boolval(getVar('System.NightMode'));
$security=boolval(getVar('System.SecurityMode'));
$mute=boolval($night or $security);
$minute=date('i');
$hour=date('H');
$time=date('H:i');

function say($text) {
    global $mute;
    if ($mute) {
        return;
    }
    $tts=new Tts\Queue();
    $tts->addMessage($text);
}

function getDevivce($name) {
    return SmartHome\Devices::get($name);
}

function getVar($name) {
    return SmartHome\Vars::get($name);
}

function setVar($name,$value) {
    SmartHome\Vars::set($name,$value);
}

function getOject($name) {
    return SmartHome\Vars::getObject($name);
}

function setObject($name,$object) {
    SmartHome\Vars::setObject($name,$object);
}

function getJson($name) {
    return SmartHome\Vars::getJson($name);
}

function setJson($name,$object) {
    SmartHome\Vars::setJson($name,$object);
}
