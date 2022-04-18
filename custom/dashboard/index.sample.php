<?php
/**
 * Не изменяйте этот файл. Данный файл может быть изменён при обновлении системы.
 * Вы можете создать файл * index.php в этом же каталоге и он будет подключен
 * вместо данного файла. 
 */

App::response()->addHeader('<script src="/js/dashboard.js"></script>');
App::response()->showHeader('Панель управления');
App::response()->showCardsHeader();
App::response()->showCardHeader('Настройте внешний вид');
?>
<p>Для настройки внешнего вида этой страницы создайте файл custom/dashboard/index.php.</p>
<?php
App::response()->showCardFooter();
Widgets\SystemState::show();
Widgets\MessageLog::show();
App::response()->showCardsFooter();
App::response()->showFooter();