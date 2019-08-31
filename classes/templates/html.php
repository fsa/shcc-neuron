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
<meta name="theme-color" content="#343a40">
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
    
    public function CardsHeader(){
?>
<div class="card-columns">
<?php
    }

    public function CardsFooter(){
?>
</div>
<?php
    }
    
    public function Card($title, $text, $state=null) {
?>
<div class="card border-dark">
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
<?php
    }
    
    public static function Popup($title, $message, $style='bg-danger text-white') {
?>
<div class="modal" tabindex="-1" role="dialog" id="popupMessage">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header <?=$style?>">
        <h5 class="modal-title"><?=$title?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?=$message?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
    $('#popupMessage').modal('show');
});
</script>
<?php
    }

    public function Footer() {
?>
</main>
<footer class="footer container-fluid bg-dark">&copy; Tavda.net, 2018-2019.</footer>
</body>
</html>
<?php        
    }

}
