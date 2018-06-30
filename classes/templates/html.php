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
<?=$this->header?>
</head>
<body>
    <p>
        <a href="/">Главная страница</a> |
        <a href="/devices/">Обнаруженные устройства</a> |
        <a href="/charts/">Графики</a>
    </p>
<hr>
<?php
    }

    public function footer() {
?>
<hr>
</body>
</html>
<?php        
    }

}
