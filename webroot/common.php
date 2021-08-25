<?php

set_include_path(get_include_path().':'.__DIR__.'/../classes/');
spl_autoload_extensions('.php');
spl_autoload_register();
openlog("shcc", LOG_PID | LOG_ODELAY, LOG_USER);
if($tz=getenv('TZ')) {
    date_default_timezone_set($tz);
}
httpResponse::setExceptionHandler();
httpResponse::setContext(Settings::get('site', ['title'=>'SHCC']));
