<?php

namespace Templates;

use Auth\Internal as Auth;

class HTML {
    
    public $header;

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
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
<script src="/libs/jquery/jquery.min.js"></script>
<script src="/libs/bootstrap/bootstrap.min.js"></script>
<?=$this->header?>
</head>
<body>
<header class="header">
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" role="navigation">
<a class="navbar-brand" href="/" role="banner">PHPMD</a>
 
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsDefault" aria-controls="navbarsDefault" aria-expanded="false" aria-label="Переключить навигацию">
<span class="navbar-toggler-icon"></span>
</button>
 
<div class="collapse navbar-collapse" id="navbarsDefault">
<ul class="navbar-nav mr-auto">
    <li class="nav-item">
        <a class="nav-link text-white" href="/charts/">Графики</a>
    </li>
<?php
if(Auth::memberOf(['admin'])) {
?>
    <li class="nav-item">
        <a class="nav-link text-white" href="/settings/">Настройки</a>
    </li>
<?php
}
if(Auth::memberOf()) {
?>
    <li class="nav-item">
        <a class="nav-link text-white" href="/logout/">Выход</a>
    </li>
<?php
} else {
?>
    <li class="nav-item text-white">
        <a class="nav-link" href="/login/">Вход</a>
    </li>
<?php
}
?>
</ul>
</div>
</nav>
</header>
<main role="main" class="container-fluid">
<?php
    }
    
    public function Card($title, $text, $state=null) {
?>
<div class="col-sm-3">
<div class="card border-dark mb-4">
<div class="card-header bg-dark text-white"><?=$title?></div>
<div class="card-body">
<p class="card-text"><?=$text?></p>
<?php
        if(!is_null($state)) {
?>
<p class="card-text"><small class="text-muted"><?=$state?></small></p>
<?php
        }
?>
</div>
</div>
</div>
<?php
    }

    public function footer() {
?>
</main>
<footer class="footer container-fluid bg-dark">&copy; Tavda.net, 2018-2019.</footer>
</body>
</html>
<?php        
    }

}
