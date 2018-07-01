<?php

require_once '../../common.php';
HTML::addHeader('<script src="/libs/highcharts/highstock.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
HTML::showPageHeader('Атмосферное давление');
?>
<script>
var date=new Date();
date.setDate(date.getDate()-2);
var series = [{
        name: 'Комната',
        params: {place: 2, measure: 3}
        }];
var title = 'Атмосферное давление';
var units = 'мм.рт.ст.';

</script>
<script src="../highstock.js"></script>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php

HTML::showPageFooter();
