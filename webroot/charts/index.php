<?php
require_once '../common.php';
Auth\Session::grantAccess();
$unit=filter_input(INPUT_GET,'unit',FILTER_VALIDATE_INT);
if (!$unit) {
    $units=SmartHome\MeterUnits::getUnitsList();
    $unit=key($units);
    $name=$units[$unit];
}
$meter=SmartHome\MeterUnits::getUnitById($unit);
if (!$meter) {
    httpResponse::showError('Не найдена измеряемая величина для отображения');
} else {
    $name=$meter->name;
}
httpResponse::addHeader('<script src="/libs/highcharts/highstock.js"></script>');
httpResponse::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
httpResponse::showHtmlHeader($name);
?>
<script src="/js/highstock.js"></script>
<ul class="nav nav-pills justify-content-center" id="charts_list"></ul>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php
httpResponse::showHtmlFooter();
