<?php

require_once '../../../common.php';
Auth\Internal::grantAccess(['admin']);
$action=filter_input(INPUT_POST,'action');
if($action) {
    require_once 'edit.php';
    exit;
}
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
$pid=filter_input(INPUT_GET,'pid',FILTER_VALIDATE_INT);
if($id) {
    $places=new \SmartHome\Places;
    $places->fetch($id);
    $place=$places->getPlace();
} else {
    $place=new SmartHome\Entity\Place;
    $place->pid=$pid;
}
use Templates\Forms;
HTML::showPageHeader();
?>
<form method="POST" action="./">
<?php
Forms::inputHidden('id',$place->id);
Forms::inputSelect('pid',$place->pid,'Родительский элемент',\SmartHome\Places::getPlaceListStmt());
Forms::inputString('name',$place->name,'Наименование');
Forms::submitButton($id?'Редактировать':'Добавить',$id?'edit':'add');
?>
</form>
<?php
HTML::showPageFooter();