<?php

set_include_path(__DIR__.'/../classes/');
spl_autoload_extensions('.php');
spl_autoload_register();

#date_default_timezone_set(Settings::get('timezone'));
#set_exception_handler('HTML::Exception');
