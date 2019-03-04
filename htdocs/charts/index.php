<?php
require_once '../common.php';
$unit=filter_input(INPUT_GET,'unit',FILTER_VALIDATE_INT);
if ($unit) {
    $meter=SmartHome\MeterUnits::getUnitById($unit);
    if (!$meter) {
        throw new AppException('Не найдена измеряемая величина для отображения');
    }
    HTML::addHeader('<script src="/libs/highcharts/highstock.js"></script>');
    HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
    HTML::showPageHeader($meter->name);
} else {
    HTML::showPageHeader('Просмотр графиков');
}
?>
<div class="row">
    <?php
    foreach (SmartHome\MeterUnits::getUnitsList() as $id=> $name) {
        if($id==$unit) {
?>
        <a class="btn btn-primary col" href="./?unit=<?=$id?>" role="button"><?=$name?></a>
<?php            
        } else {
?>
        <a class="btn btn-secondary col" href="./?unit=<?=$id?>" role="button"><?=$name?></a>
<?php
        }
    }
    ?>
</div>
<?php
if ($unit) {
?>
<script src="highstock.js"></script>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php
}
HTML::showPageFooter();
