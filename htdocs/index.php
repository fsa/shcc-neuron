<?php

require_once 'common.php';
HTML::showPageHeader('Умный дом');
?>
<h1>Умный дом</h1>
<?php
if(SmartHome\Vars::get('SyStem.NightMode')) {
?>
<p>Включен ночной режим.</p>
<?php
}
if(SmartHome\Vars::get('SyStem.SecurityMode')) {
?>
<p>Включен режим охраны.</p>
<?php
}
#var_dump(Auth::getUser());
?>
<p>Это тестовая версия системы &laquo;Умный дом&raquo;. Используйте её на свой страх и риск.</p>
<?php
HTML::showPageFooter();