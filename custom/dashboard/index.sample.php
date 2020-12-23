<?php
/**
 * Не изменяйте этот файл. Данный файл может быть изменён при обновлении системы.
 * Вы можете создать файл * index.php в этом же каталоге и он будет подключен
 * вместо данного файла. 
 */
httpResponse::addHeader('<script src="/js/dashboard.js"></script>');
httpResponse::showHtmlHeader('Панель управления');
httpResponse::showCardsHeader();
httpResponse::showCardHeader('Настройте внешний вид');
?>
<p>Для настройки внешнего вида этой страницы создайте файл custom/dashboard/index.php.</p>
<?php
httpResponse::showCardFooter();
Widgets\SystemState::show();
Widgets\MessageLog::show();
httpResponse::showCardsFooter();
httpResponse::showHtmlFooter();