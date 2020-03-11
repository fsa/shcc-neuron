<?php

require_once '../common.php';
Auth\Internal::grantAccess(['admin']);
HTML::showPageHeader('Настройки');
$menu=require './sections.php';
HTML::showNavPills('%s/', $menu);
foreach ($menu as $path=>$name) {
?>
<p><a href="<?=$path?>/"><?=$name?></a></p>
<?php
}
HTML::showPageFooter();