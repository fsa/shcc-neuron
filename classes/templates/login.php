<?php

namespace Templates;

class Login {

    public $title='Вход на сайт';
    public $url='./';
    public $redirect_uri='';
    public $header;

    public function show() {
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=$this->title?></title>
<link rel="stylesheet" type="text/css" href="/bootstrap.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?=$this->header?>
</head>
<body>
<div class="container">
<div class="card">
<div class="card-header"><?=$this->title?></div>
<div class="card-body">
<form method="POST" action="<?=$this->url?>">
<input type="hidden" name="redirect_uri" value="<?=$this->redirect_uri?>">
<div class="form-group">
<label for="inputLogin">Логин</label>
<input type="login" name="login" class="form-control" id="inputLogin" aria-describedby="loginHelp" placeholder="Введите имя пользователя">
<!--small id="loginHelp" class="form-text text-muted">We'll never share your email with anyone else.</small-->
</div>
<div class="form-group">
<label for="inputPassword">Пароль</label>
<input type="password" name="password" class="form-control" id="inputPassword" placeholder="Введите пароль">
</div>
<button type="submit" class="btn btn-primary">Войти</button>
</form>
</div>
</div>
</div>
</body>
</html>
<?php
    }

}
