<?php

require_once '../common.php';
HTML::showPageHeader('Вход в систему');
?>
<p><a href="user.php">Пользователь</a></p>
<p><a href="admin.php">Администратор</a></p>
<hr>
<p><a href="/logout/">Выход</a></p>
<?php
HTML::showPageFooter();