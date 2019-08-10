<?php

require_once '../../custom/lib.php';
DB::query("set @@session.time_zone = '+00:00'");

$config=\Settings::get('pdo');
$pgsql=new PDO('pgsql:host=localhost;dbname=phpmd',$config->username,$config->password);
$pgsql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pgsql->query('SET TIMEZONE="+00:00"');

$tables=[
    'places',
    'modules',
    'devices',
    'meter_units',
    'meters',
    'meter_history',
    'indicators',
    'indicator_history',
    'variables'
];

foreach($tables as $table) {
    $m=DB::query('SELECT * FROM '.$table);
    $row=$m->fetch(PDO::FETCH_ASSOC);
    $keys=array_keys($row);
    $stmt=$pgsql->prepare('INSERT INTO '.$table.' ('.join(',',$keys).') VALUES (:'.join(',:',$keys).')');
    do {
        printf('%s %s', $table, print_r($row, true));
        $stmt->execute($row);
    } while($row=$m->fetch(PDO::FETCH_ASSOC));
    $m->closeCursor();
}

function insert($s, $table,$values) {
    $keys=array_keys($values);
    ;
}