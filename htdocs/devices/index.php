<?php

require_once '../common.php';
$mem=new Shm();
$yeelight=$mem->getVar(1);
$xiaomi=$mem->getVar(2);
?>
<h1>Xiaomi</h1>
<?php
foreach ($xiaomi as $dev) {
?>
<p><?=$dev->getDeviceName()?></p>
<p>sid: <?=$dev->getDeviceId()?>. Last Update: <?=$dev->getLastUpdate()?></p>
<hr>
<?php
}
?>
<h1>Yeelight</h1>
<?php
foreach ($yeelight as $dev) {
?>
<p><?=$dev->getDeviceName()?></p>
<p>sid: <?=$dev->getDeviceId()?>. Last Update: <?=$dev->getLastUpdate()?></p>
<hr>
<?php
}
