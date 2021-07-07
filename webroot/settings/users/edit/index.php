<?php

require_once '../../../common.php';
Auth\Session::grantAccess([]);
$action=filter_input(INPUT_POST, 'action');
if ($action) {
    switch ($action) {
        case 'save':
            require 'save.php';
            break;
        case 'create':
            require 'create.php';
            break;
    }
    exit;
}
use Templates\Forms;
$id=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if($id) {
    $user=Auth\UserEntity::fetch($id);
} else {
    $user=new Auth\UserEntity;
}
httpResponse::setTemplate(new Templates\PageSettings);
httpResponse::showHtmlHeader('Редактировать пользователя '.$user->login);
Forms::formHeader('POST', './');
Forms::inputHidden('id', $user->id);
Forms::inputString('login', $user->login, 'Логин');
Forms::inputPassword('password', '', 'Пароль (оставьте поле пустым, если его не нужно менять)');
Forms::inputString('name', $user->name, 'Имя пользователя');
Forms::inputString('email', $user->email, 'Электронная почта');
foreach (Settings::get('user_groups', []) as $group=>$title) {
    Forms::inputCheckbox('scope['.$group.']', $user->memberOf($group), $title);
}
Forms::inputCheckbox('disabled', $user->disabled, 'Пользователь заблокирован');
Forms::submitButton($user->id?'Сохранить изменения':'Создать', 'save');
Forms::formFooter();
httpResponse::showHtmlFooter();