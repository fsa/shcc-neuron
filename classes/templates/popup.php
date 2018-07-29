<?php
namespace Templates;
class Popup {
  public $message;
  
  public function show() {
?>
<div id="parent_popup">
<div id="popup">
<?=$this->message?>
<p style="cursor: pointer;" onclick="document.getElementById('parent_popup').style.display='none';">Закрыть</p>
</div>
</div>
<?php
  }
}
