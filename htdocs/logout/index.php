<?php

require_once '../common.php';
Auth\Session::logout();
httpResponse::redirection('../');