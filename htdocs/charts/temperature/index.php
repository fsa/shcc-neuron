<?php

require_once '../../common.php';
HTML::addHeader('<script src="/libs/highcharts/highstock.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
HTML::showPageHeader('Температура');
?>
<script>
var series = [{
        name: 'Комната',
        params: {place: 2, unit: 1}
        },{
        name: 'Кухня',
        params: {place: 3, unit: 1}
        }];
var title = 'Температура';
var units = '\u00B0C';
</script>
<script src="../highstock.js"></script>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php

HTML::showPageFooter();
