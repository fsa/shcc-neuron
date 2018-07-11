<?php

require_once '../../common.php';
HTML::addHeader('<script src="/libs/highcharts/highstock.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
HTML::showPageHeader('Относительная влажность');
?>
<script>
var date=new Date();
date.setDate(date.getDate()-2);
var series = [{
        name: 'Комната',
        params: {place: 2, meter: 2}
        },{
        name: 'Кухня',
        params: {place: 3, meter: 2}
        }];
var title = 'Относительная влажность';
var units = '%';
</script>
<script src="../highstock.js"></script>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php

HTML::showPageFooter();
