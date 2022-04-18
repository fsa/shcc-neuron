<?php

require_once '../../vendor/autoload.php';
App::initHtml(Templates\PageSettings::class);
App::session()->grantAccess([]);
App::response()->showHeader('Настройки');
?>
<p>Настройки.</p>
<?php
App::response()->showFooter();