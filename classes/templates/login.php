<?php

namespace Templates;

class Login {

    public $header;

    public function show() {
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?=$this->title?></title>
        <link rel="stylesheet" type="text/css" href="/styles.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
<?=$this->header?>
    </head>
    <body bgcolor="white">
        <div class="container">
            <div class="card">
            <div class="card-header bg-info">Вход</div>
            <div class="card-body">
            <form method="POST" action="./">
                <div class="form-group">
                    <label for="logn">Имя пользователя</label>
                    <input type="text" class="form-control" name="login" placeholder="Введите имя пользователя">
                    <small id="loginHelp" class="form-text text-muted">Укажите имя пользователья для входа в систему.</small>
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" name="password" placeholder="Введите пароль">
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
