<?php
use FSA\Neuron\HttpResponse,
    FSA\Neuron\Settings;
require_once __DIR__.'/../vendor/autoload.php';
ini_set('syslog.filter', 'raw');
openlog("shcc", LOG_PID | LOG_ODELAY, LOG_USER);
if(getenv('TZ')) {
    date_default_timezone_set(getenv('TZ'));
}
HttpResponse::setHtmlMode(Settings::get('site', ['title'=>'SHCC']));
