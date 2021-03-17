<?php

require_once '../common.php';
Auth\Session::grantAccess([]);
httpResponse::setTemplate(new Templates\PageSettings);
httpResponse::showHtmlHeader('Настройки');
?>
<p>Настройки.</p>
<?php
httpResponse::showHtmlFooter();