<?php

if (!isset($action)) {
    die;
}
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
$pid=filter_input(INPUT_POST,'pid',FILTER_VALIDATE_INT);
$name=filter_input(INPUT_POST,'name');
$places=new \SmartHome\Places;
$places->createPlace($id,$pid,$name);
$places->upsert();
httpResponse::redirect('../');