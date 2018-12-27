<?php

namespace Templates;

class HTML {

    public function header() {
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=$this->title?></title>
<meta name="viewport" content="width=device-width">
<meta name="theme-color" content="gray">
<link rel="stylesheet" href="/styles.css">
<script src="/libs/jquery/jquery.min.js"></script>
<?=$this->header?>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="/">Главная страница</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Свернуть меню"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/charts/">Графики</a>
            </li>
<?php
if(\Auth::memberOf(['admin'])) {
?>
            <li class="nav-item">
                <a class="nav-link" href="/settings/">Настройки</a>
            </li>
<?php
}
if(\Auth::memberOf()) {
?>
            <li class="nav-item">
                <a class="nav-link" href="/logout/">Выход</a>
            </li>
<?php
} else {
?>
            <li class="nav-item">
                <a class="nav-link" href="/login/">Вход</a>
            </li>
<?php
}
?>
        </ul>
    </div>
</nav>

<main role="main" class="container-fluid">
<?php
    }

    public function footer() {
?>
</main>
<footer class="container-fluid bg-dark">&copy; Tavda.net 2018</footer>
</body>
</html>
<?php        
    }

}
