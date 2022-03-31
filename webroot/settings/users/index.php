<?php
use FSA\Neuron\HttpResponse,
    FSA\Neuron\Session,
    FSA\Neuron\UserDB\User,
    FSA\Neuron\HTML\DBQuery;
require_once '../../common.php';
Session::grantAccess([]);
HttpResponse::setTemplate(new Templates\PageSettings);
HttpResponse::showHtmlHeader('Пользователи');
?>
<p><a href="edit/" class="btn btn-secondary">Создать нового пользователя</a></p>
<?php
$users=new DBQuery(new Templates\Settings\Users);
$users->setCaption('Список пользователей');
$users->show(User::stmtGetAll());
HttpResponse::showHtmlFooter();
