<?php

require_once '../common.php';
$host=\Settings::get('daemon-ip');
if(!is_null($host)) {
    var_dump(getenv('REMOTE_ADDR'),$host);
    if(getenv('REMOTE_ADDR')!=$host) {
        die('Wrong host');
    }
}
$module=filter_input(INPUT_GET,'module');
$uid=filter_input(INPUT_GET,'uid');
$json=filter_input(INPUT_GET,'data');
if(!$json) {
    $json=filter_input(INPUT_POST,'data');
}
if(!$module or !$uid or !$json) {
    die('Wrong prarameters');
}
$data=json_decode($json);
if(is_null($data)){
    die('Wrong JSON data');
}
$stmt=DB::prepare('SELECT s.id, s.property, d.place_id, s.measure_id FROM device_sensors s LEFT JOIN devices d ON s.device_id=d.id WHERE d.module=? AND d.uid=?');
$stmt->execute([$module,$uid]);
$sensors=$stmt->fetchAll(PDO::FETCH_OBJ);
$stmt->closeCursor();
foreach ($sensors as $sensor) {
    if(isset($data->{$sensor->property})) {
        $stmt=DB::prepare('INSERT INTO device_sensor_history (device_sensor_id,place_id,measure_id,value) VALUES (?,?,?,?)');
        $stmt->execute([$sensor->id,$sensor->place_id,$sensor->measure_id,$data->{$sensor->property}]);
        $stmt->closeCursor();
    }
}
#TODO открывать пользовтельский файл или код для обработки событий от модуля
