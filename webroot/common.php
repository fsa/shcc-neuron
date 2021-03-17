<?php

set_include_path(get_include_path().':'.__DIR__.'/../classes/');
spl_autoload_extensions('.php');
spl_autoload_register();
date_default_timezone_set(Settings::get('timezone'));
httpResponse::setExceptionHandler();
httpResponse::setContext(Settings::get('site'));
