<?php

require_once '../common.php';
Auth\Session::grantAccess([]);
httpResponse::setTemplate(\Templates\PageSettings::class);
httpResponse::showHtmlHeader('Настройки');
?>
<p>Настройки.</p>
<?php
httpResponse::showHtmlFooter();