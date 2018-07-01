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
    <nav class="navbar navbar-expand-md navbar-light bg-gray fixed-top">
      <a class="navbar-brand" href="/">Главная страница</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="/devices/">Обнаруженные устройства</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/charts/">Графики</a>
          </li>
        </ul>
      </div>
    </nav>

    <main role="main" class="container-fluid p-5">
<?php
    }

    public function footer() {
?>
    </main>
    <footer class="container bg-gray">&copy; Tavda.net 2018</footer>
</body>
</html>
<?php        
    }

}
