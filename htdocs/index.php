<?php

require_once 'common.php';
Auth\Internal::grantAccess();
HTML::showPageHeader('Панель управления');
if(file_exists('../custom/dashboard.php')) {
    include_once '../custom/dashboard.php';
} else {
?>
<p><strong>Умный дом</strong></p>
<?php
    if(SmartHome\Vars::get('System.NightMode')) {
?>
<p>Включен ночной режим.</p>
<?php
    }
    if(SmartHome\Vars::get('System.SecurityMode')) {
?>
<p>Включен режим охраны.</p>
<?php
    }
?>
<p>Для настройки внешнего вида этой страницы создайте файл custom/dashboard.php.</p>
<?php
}
HTML::showPageFooter();