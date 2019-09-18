<?php

require_once '../common.php';
Auth\Internal::grantAccess(['admin']);
HTML::showPageHeader('Настройки');
?>
<p><a href="places/">Объекты</a></p>
<p><a href="devices/">Устройства</a></p>
            
<?php
HTML::showPageFooter();