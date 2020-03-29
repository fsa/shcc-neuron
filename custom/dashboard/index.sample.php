<?php
/**
 * Не изменяйте этот файл. Данный файл может быть изменён при обновлении системы.
 * Вы можете создать файл * index.php в этом же каталоге и он будет подключен
 * вместо данного файла. 
 */
httpResponse::showCardsHeader();
httpResponse::showCard('Настройте внешний вид','Для настройки внешнего вида этой страницы создайте файл custom/dashboard/index.php.');
Widgets\SystemState::show();
Widgets\MessageLog::show();
httpResponse::showCardsFooter();