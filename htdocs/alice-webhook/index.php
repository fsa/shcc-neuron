<?php

require_once '../common.php';
$alice=new Yandex\Alice(file_get_contents('php://input'));
$result_tts=false;
try {
    #$alice->checkSkillId('skill_id');
    $request=$alice->getRequest();
    $command=mb_strtolower($request['command']);
    if ($alice->isNewDialog()) {
        $result="Умный дом приветствует вас";
        #$result_tts="Умный дом приветствует вас";
    } else {
        $result='Я пока ничего не умею.';
        $result_tts="Я пока ничего не умею.";
    }
    $alice->setText($result,$result_tts);
} catch (AppException $ex) {
    $alice->setText($ex->getMessage());
} catch (Exception $ex) {
    $alice->setText('Сервер сообщает мне, что произошла какая-то ошибка.');
}
$alice->getResponse();
