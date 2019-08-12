<?php

if(!isset($request_id)) {die;}
header('Content-Type: application/json');
?>
{
    "request_id": "<?=$request_id?>"
}