<?php
namespace Templates;
class Message {
  public $title;
  public $site_title;
  public $style='info';
  public $header;
  public $message;

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
<h1><?=\Settings::get('site_title')?></h1>
<div class="card">
<div class="card-header bg-<?=$this->style?>"><?=$this->title?></div>
<div class="card-body"><?=$this->message?></div>
</div>
</div>
</body>
</html>
<?php
  }
}
