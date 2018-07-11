<?php

require_once '../common.php';
$unit=filter_input(INPUT_GET,'unit',FILTER_VALIDATE_INT);
if($unit) {
    require 'show_unit.php';
    die;
}
HTML::showPageHeader('Просмотр графиков');
foreach (SmartHome\MeterUnits::getUnitsList() as $id=>$name) {
?>
<p><a href="./?unit=<?=$id?>"><?=$name?></a></p>
<?php
}
HTML::showPageFooter();