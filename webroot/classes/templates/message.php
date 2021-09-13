<?php

namespace Templates;

class Message {

    public $title;
    public $context;
    public $style;
    public $header;
    public $message;

    public function show() {
        switch ($this->style) {
            case 'primary':
            case 'secondary':
            case 'success':
            case 'danger':
            case 'dark':
            case 'warning':
                $style_class='bg-'.$this->style.' text-white';
                break;
            case 'info':
            case 'light':
            case 'white':
            case 'transparent':
                $style_class='bg-'.$this->style.' text-dark';
                break;
            default:
                $style_class='bg-info text-white';
        }
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
<body bgcolor="white">
<div class="container">
<h1><?=$this->context['title']?></h1>
<div class="card">
<div class="card-header <?=$style_class?>"><?=$this->title?></div>
<div class="card-body"><?=$this->message?></div>
</div>
</div>
</body>
</html>
<?php
    }
}
