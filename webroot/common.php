<?php

set_include_path(get_include_path().':'.__DIR__.'/classes/');
spl_autoload_extensions('.php');
spl_autoload_register();
ini_set('syslog.filter', 'raw');
openlog("shcc", LOG_PID | LOG_ODELAY, LOG_USER);
if(getenv('TZ')) {
    date_default_timezone_set(getenv('TZ'));
}
httpResponse::setHtmlExceptionHandler();
httpResponse::setContext(Settings::get('site', ['title'=>'SHCC']));
