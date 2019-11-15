<?php

require_once '../common.php';
Auth\Internal::logout();
httpResponse::redirect('../');