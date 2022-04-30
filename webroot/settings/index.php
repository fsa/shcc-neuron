<?php

require_once '../../vendor/autoload.php';
$response = App::initHtml(Templates\PageSettings::class);
App::session()->grantAccess([]);
$response->redirection('devices/');
