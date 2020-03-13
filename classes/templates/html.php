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
<meta name="theme-color" content="#17a2b8">
<link rel="stylesheet" href="/bootstrap.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
<script src="/libs/jquery/jquery.min.js"></script>
<script src="/libs/bootstrap/bootstrap.min.js"></script>
<?=$this->header?>
</head>
<body>
<header class="container-fluid p-0">
<nav class="navbar navbar-expand-md navbar-dark bg-info" role="navigation">
<a class="navbar-brand" href="/" role="banner">SHCC</a>
 
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsDefault" aria-controls="navbarsDefault" aria-expanded="false" aria-label="Переключить навигацию">
<span class="navbar-toggler-icon"></span>
</button>
 
<div class="collapse navbar-collapse" id="navbarsDefault">
<ul class="navbar-nav mr-auto">
    <li class="nav-item">
        <a class="nav-link" href="/charts/">Графики</a>
    </li>
<?php
if(Auth::memberOf(['admin'])) {
?>
    <li class="nav-item">
        <a class="nav-link" href="/settings/">Настройки</a>
    </li>
<?php
}
if(Auth::memberOf()) {
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
</header>
<main role="main" class="container-fluid p-3">
<?php
    }

    public function NavPills(string $url, array $buttons, $current=null) {
?>
<ul class="nav nav-pills justify-content-center" id="navpills_item_id" navpills_item_id="<?=$current?>">
<?php
    foreach ($buttons as $id=> $name) {
?>
<li class="nav-item">
    <a class="nav-link<?=($id==$current)?' active':''?>" href="<?=sprintf($url, $id)?>"><?=$name?></a>
</li>
<?php
    }
    ?>
</ul>
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
<div class="card border-info">
<div class="card-header bg-info text-white"><?=$title?></div>
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
<footer class="footer container-fluid bg-info p-3 text-white"><?=date('d.m.Y H:i:s')?></footer>
</body>
</html>
<?php        
    }

}
