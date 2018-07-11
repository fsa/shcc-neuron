<?php

if (!isset($unit)) {
    die;
}
$meter=SmartHome\MeterUnits::getUnitById($unit);
if(!$meter) {
    throw new AppException('Не найдена измеряемая величина для отображения');
}
$series=[];
foreach (SmartHome\Meters::getMetersByUnitId($unit) as $serie) {
    $series[]="{name: '".html_entity_decode($serie->name)."', params: {place: $serie->place_id, unit: $unit}}";
}
if(sizeof($series)==0) {
    throw new AppException('Не найдены активные датчики для измерения: '.$meter->name);
} 
HTML::addHeader('<script src="/libs/highcharts/highstock.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
HTML::showPageHeader($meter->name);
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
HTML::showPageFooter();
