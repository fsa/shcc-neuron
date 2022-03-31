<?php
use FSA\Neuron\HttpResponse,
    FSA\Neuron\Session;
require_once '../common.php';
Session::grantAccess([]);
HttpResponse::setTemplate(new Templates\PageSettings);
HttpResponse::showHtmlHeader('Настройки');
?>
<p>Настройки.</p>
<?php
HttpResponse::showHtmlFooter();