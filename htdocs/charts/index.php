<?php
require_once '../common.php';
Auth\Internal::grantAccess();
$unit=filter_input(INPUT_GET,'unit',FILTER_VALIDATE_INT);
if (!$unit) {
    $units=SmartHome\MeterUnits::getUnitsList();
    $unit=key($units);
    $name=$units[$unit];
}
$meter=SmartHome\MeterUnits::getUnitById($unit);
if (!$meter) {
    throw new AppException('Не найдена измеряемая величина для отображения');
} else {
    $name=$meter->name;
}
HTML::addHeader('<script src="/libs/highcharts/highstock.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
HTML::showPageHeader($name);
?>
<ul class="nav nav-pills justify-content-center">
<?php
    foreach (SmartHome\MeterUnits::getUnitsList() as $id=> $name) {
        if($id==$unit) {
?>
<li class="nav-item">
    <a class="nav-link active" href="./?unit=<?=$id?>" id="unit_id" unit_id="<?=$id?>" role="button"><?=$name?></a>
</li>
<?php            
        } else {
?>
<li class="nav-item">
    <a class="nav-link" href="./?unit=<?=$id?>" role="button"><?=$name?></a>
</li>
<?php
        }
    }
    ?>
</ul>
<script src="highstock.js"></script>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php
HTML::showPageFooter();
