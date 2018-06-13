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
<?php
    }

    public function footer() {
?>
</body>
</html>
<?php        
    }

}
