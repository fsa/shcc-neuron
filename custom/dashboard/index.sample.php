<?php
/**
 * Не изменяйте этот файл. Данный файл может быть изменён при обновлении системы.
 * Вы можете создать файл * index.php в этом же каталоге и он будет подключен
 * вместо данного файла. 
 */
HTML::showCardsHeader();
HTML::showCard('Настройте внешний вид','Для настройки внешнего вида этой страницы создайте файл custom/dashboard/index.php.');
$state=[];
if(SmartHome\Vars::get('System.NightMode')) {
    $state[]='Включен ночной режим.';
}
if(SmartHome\Vars::get('System.SecurityMode')) {
    $state[]='Включен режим охраны.';
}
if(sizeof($state)==0) {
    $state[]='Система работает в обычном режиме.';
}
HTML::showCard('Состояние системы',join('<br>', $state));
$log=Tts\Log::getLastMessages();
$log_message=[];
foreach ($log AS $row) {
    $log_message[]=sprintf('%s %s', date('H:i:s',strtotime($row->timestamp)), $row->text);
}
HTML::showCard('Последние голосовые сообщения',join('<br>', $log_message));
HTML::showCardsFooter();