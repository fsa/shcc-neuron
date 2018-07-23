<?php

require_once '../common.php';
$tts=false;
try {
    $alice=new Yandex\Alice(file_get_contents('php://input'));
    //$alice->checkSkillId(Settings::get('yandex')->alice_skill_id);
    $request=$alice->getRequest();
    $command=mb_strtolower($request['command']);
    if ($alice->isNewDialog()) {
        $text="Я умный дом phpmd. А кто вы? Я вас не знаю.";
        #$text="Умный дом phpmd приветствует вас!";
        #$tts="Умный дом приветствует вас";
    } else {
        $text='Я вас не знаю.';
        $tts="Я вас не знаю.";
    }
    $alice->setText($text,$tts);
} catch (AppException $ex) {
    $alice->setText($ex->getMessage());
} catch (Exception $ex) {
    $alice->setText('Сервер сообщает мне, что произошла какая-то ошибка.');
}
$alice->getResponse();
