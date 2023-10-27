<?php

if (getenv('REMOTE_ADDR')!='127.0.0.1') {
    die('Wrong host');
}
#fastcgi_finish_request();
require_once '../../common.php';
$id=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) {
    die('ID!');
}
$telegram=Settings::get('telegram');
if(!$telegram) {
    die('TG config!');
}
Telegram\Query::init($telegram);
$s=DB::prepare('SELECT "TextDecoded" FROM inbox WHERE "ID"=?');
$s->execute([$id]);
$text=$s->fetchColumn();
if(!$text) {
    die('Text');
}
$api=new Telegram\SendMessage($telegram['log_channel'], "Вам пришла SMS:\n".$text);
#$api->setParseModeMarkdown();
$api->setDisableWebPagePreview();
$api->httpPostJson();
