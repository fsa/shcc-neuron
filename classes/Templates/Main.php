<?php

namespace Templates;

class Main
{

    public $title;
    public $context;
    public $header;
    public $notify;

    public function showHeader()
    {
        $title=is_null($this->title)?$this->context['title']:$this->title.' :: '.$this->context['title'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=$title?></title>
<meta name="viewport" content="width=device-width">
<meta name="theme-color" content="#527779">
<link rel="manifest" href="/manifest.json">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">
<link rel="stylesheet" href="/bootstrap.css">
<script src="/libs/bootstrap/bootstrap.min.js"></script>
<script src="/js/main.js"></script>
<?=$this->header?>
</head>
<body>
<?php
        if($this->notify) {
            $this->showNotify('Информация', $this->notify);
        }
?>
<header class="container-fluid p-0">
<nav class="navbar navbar-expand-md navbar-dark bg-primary px-3" role="navigation">
<a class="navbar-brand" href="/" role="banner">SHCC</a>

<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsDefault" aria-controls="navbarsDefault" aria-expanded="false" aria-label="Переключить навигацию">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarsDefault">
<ul class="navbar-nav mr-auto">
<?php
foreach($this->context['dashboard'] as $url=>$name) {
?>
    <li class="nav-item">
        <a class="nav-link" href="/?page=<?=$url?>"><?=$name?></a>
    </li>
<?php
}
if($this->context['session']->memberOf([])) {
?>
    <li class="nav-item">
        <a class="nav-link" href="/settings/">Настройки</a>
    </li>
<?php
}
if($this->context['session']->memberOf()) {
?>
    <li class="nav-item">
        <a class="nav-link" href="/login/?action=logout">Выход</a>
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
<main role="main" class="container-fluid py-3">
<?php
    }
    
    public function showNavPills(string $url, array $buttons, $current=null)
    {
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

    public function showNavTabs(string $url, array $buttons, $current=null)
    {
?>
<ul class="nav nav-tabs justify-content-center" id="navpills_item_id" navpills_item_id="<?=$current?>">
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

    public function showCardsHeader()
    {
?>
<div class="row row-cols-1 row-cols-md-3 g-4">
<?php
    }

    public function showCardsFooter()
    {
?>
</div>
<?php
    }
    
    public function showCard($title, $text, $state=null)
    {
        $this->showCardHeader($title);
?>
<p class="card-text"><?=$text?></p>
<?php
        if(!is_null($state)) {
            $this->showCardState($state);
        }
        $this->showCardFooter();
    }

    public function showCardHeader($title)
    {
?>
<div class="col">
<div class="card h-100">
<div class="card-header bg-primary text-white"><?=$title?></div>
<div class="card-body">
<?php
    }

    public function showCardState($state)
    {
?>
<p class="card-text"><small class="text-muted"><?=$state?></small></p>
<?php
        }

    public function showCardFooter()
    {
?>
</div>
</div>
</div>
<?php
    }
    
    public function showPopup($message, $title, $style=null) {
        switch ($style) {
            case 'primary':
            case 'secondary':
            case 'success':
            case 'danger':
            case 'info':
            case 'dark':
                $style_class='bg-'.$style.' text-white';
                break;
            case 'warning':
            case 'light':
            case 'white':
            case 'transparent':
                $style_class='bg-'.$style.' text-dark';
                break;
            default:
                $style_class='bg-primary text-white';
        }
?>
<div class="modal" tabindex="-1" autoopen="on">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header <?=$style_class?>">
        <h5 class="modal-title"><?=$title?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body"><?=$message?></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<?php
    }

    private function showNotify(string $title, string $text)
    {
?>
<div aria-live="polite" aria-atomic="true" class="position-relative" style="z-index: 1050">
  <div class="toast-container position-absolute top-0 end-0 p-3">
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-secondary text-white">
      <strong class="me-auto"><?=$title?></strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      <?=$text?>
    </div>
    </div>
  </div>
</div>
<?php
    }

    public function showFooter()
    {
?>
</main>
    <footer class="footer container-fluid bg-primary p-3 text-white"><span id="page_last_update"><?=date('d.m.Y H:i:s')?></span></footer>
</body>
</html>
<?php        
    }

}
