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
HTML::showNavPills('./?unit=%s', SmartHome\MeterUnits::getUnitsList(), $unit);
?>
<script src="highstock.js"></script>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php
HTML::showPageFooter();
