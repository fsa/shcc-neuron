<?php
/**
 * Не изменяйте этот файл. Данный файл может быть изменён при обновлении системы.
 * Вы можете создать файл * index.php в этом же каталоге и он будет подключен
 * вместо данного файла. 
 */

use FSA\Neuron\HttpResponse;
HttpResponse::addHeader('<script src="/js/dashboard.js"></script>');
HttpResponse::showHtmlHeader('Панель управления');
HttpResponse::showCardsHeader();
HttpResponse::showCardHeader('Настройте внешний вид');
?>
<p>Для настройки внешнего вида этой страницы создайте файл custom/dashboard/index.php.</p>
<?php
HttpResponse::showCardFooter();
Widgets\SystemState::show();
Widgets\MessageLog::show();
HttpResponse::showCardsFooter();
HttpResponse::showHtmlFooter();