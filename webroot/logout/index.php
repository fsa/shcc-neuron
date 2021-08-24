<?php

require_once '../common.php';
Session::drop();
httpResponse::redirection('../');
