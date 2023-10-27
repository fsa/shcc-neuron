<?php

if (getenv('REMOTE_ADDR')!='127.0.0.1') {
    die('Wrong host');
}
#fastcgi_finish_request();
require_once '../../../custom/lib.php';
$id=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) {
    die('ID!');
}
$s=App::sql()->prepare('SELECT "SenderNumber" as num, "TextDecoded" as text FROM inbox WHERE "ID"=?');
$s->execute([$id]);
$msg=$s->fetchObject();
#var_dump($msg);
if(!$msg or !($msg->text)) {
    die('No Message!');
}
telegram("Пришло SMS от ".$msg->num.":\n".$msg->text);
