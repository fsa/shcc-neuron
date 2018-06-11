<?php

require_once 'common.php';
try {
    throw new Exception('Bla');
} catch (Exception $ex) {
    echo $ex;
}