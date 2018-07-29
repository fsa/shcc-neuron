<?php

require_once '../common.php';
Auth::grantAccess(['admin']);
HTML::showPageHeader('Настройки');
?>
<p><a href="places/">Места</a></p>
<p><a href="devices/">Устройства</a></p>
            
<?php
HTML::showPageFooter();