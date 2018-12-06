<?php
require_once '../common.php';
$unit=filter_input(INPUT_GET,'unit',FILTER_VALIDATE_INT);
if ($unit) {
    $meter=SmartHome\MeterUnits::getUnitById($unit);
    if (!$meter) {
        throw new AppException('Не найдена измеряемая величина для отображения');
    }
    $series=[];
    foreach (SmartHome\Meters::getMetersByUnitId($unit) as $serie) {
        $series[]="{name: '".html_entity_decode($serie->name)."', params: {place: $serie->place_id, unit: $unit}}";
    }
    if (sizeof($series)==0) {
        throw new AppException('Не найдены активные датчики для измерения: '.$meter->name);
    }
    HTML::addHeader('<script src="/libs/highcharts/highstock.js"></script>');
    HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
    HTML::showPageHeader($meter->name);
} else {
    HTML::showPageHeader('Просмотр графиков');
}
?>
<p>
    <?php
    foreach (SmartHome\MeterUnits::getUnitsList() as $id=> $name) {
        if($id==$unit) {
?>
        <a class="btn btn-primary" href="./?unit=<?=$id?>" role="button"><?=$name?></a>
<?php            
        } else {
?>
        <a class="btn btn-secondary" href="./?unit=<?=$id?>" role="button"><?=$name?></a>
<?php
        }
    }
    ?>
</p>
<?php
if ($unit) {
?>
<script>
    var date = new Date();
    date.setDate(date.getDate() - 2);
    var series = [<?=join(',',$series)?>];
    var title = '<?=html_entity_decode($meter->name)?>';
    var units = '<?=html_entity_decode($meter->unit)?>';
</script>
<script src="highstock.js"></script>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php
}
HTML::showPageFooter();
