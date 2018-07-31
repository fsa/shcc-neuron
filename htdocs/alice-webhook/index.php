<?php

require_once '../common.php';
$tts=false;
try {
    $alice=new Yandex\Alice(file_get_contents('php://input'));
    //$alice->checkSkillId(Settings::get('yandex')->alice_skill_id);
    $request=$alice->getRequest();
    $session=$alice->getSession();
    $command=mb_strtolower($request['command']);
    if (strripos($command,'помощь')!==false) { #Test: 
        $text="Это приватный навык для управления умным домом. Для использования навыка необходимо зарегистрироваться. Ваш ID: ".$session['user_id'];
    } else if ($alice->isNewDialog()) {
        $text="Я умный дом phpmd. Я вас не знаю. Вам необходимо зарегистрироваться.";
        $tts="Я умный дом. Я вас не знаю. Вам необходимо зарегистрироваться.";
    } else {
        $text='Мне не разрешают общаться с незнакомцами. Вам нужно зарегистрироваться у автора проекта.';
        #$tts="Я вас не знаю.";
    }
    $alice->setText($text,$tts);
} catch (AppException $ex) {
    $alice->setText($ex->getMessage());
} catch (Exception $ex) {
    $alice->setText('Сервер сообщает мне, что произошла какая-то ошибка.');
}
$alice->getResponse();
