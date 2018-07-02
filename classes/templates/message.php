<?php
namespace Templates;
class Message {
  public $title;
  public $site_title;
  public $style='default';
  public $header;
  public $message;

  public function show() {
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=$this->title?></title>
<link rel="stylesheet" type="text/css" href="/message.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?=$this->header?>
</head>
<body bgcolor="white">
<div class="container">
<div class="page-header">
  <h1><?=$this->site_title?></h1>
</div>
<div class="panel panel-<?=$this->style?>">
  <div class="panel-heading"><?=$this->title?></div>
  <div class="panel-body">
<?=$this->message?>
  </div>
  </div>
</div>
</body>
</html>
<?php
  }
}
