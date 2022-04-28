<?php

if (sizeof($argv) != 2) {
    die('Usage: ' . $argv[0] . ' plugin_name' . PHP_EOL);
}
require '../vendor/autoload.php';
$plugin = $argv[1];
if (!$url = getenv('SERVER_URL')) {
    $url = 'http://127.0.0.1';
}
$response = file_get_contents($url . '/api/daemon/', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json; charset=utf-8",
        'content' => json_encode(['plugin' => $plugin]),
        'ignore_errors' => true
    ]
]));
$state = json_decode($response);
if (is_null($state)) {
    echo "$plugin: Error getting plugin info " . PHP_EOL;
    exit(1);
}
if (isset($state->error)) {
    echo "$plugin: " . $state->error . PHP_EOL;
    exit(2);
}
if (!(isset($state->daemon) and isset($state->settings))) {
    echo "$plugin: Plugin configuration error" . PHP_EOL;
    exit(3);
}
$daemon_class = $state->daemon;
if (!class_exists($daemon_class)) {
    echo "Daemon class \"$daemon_class\" not exists." . PHP_EOL;
    exit(4);
}
$daemon = new $daemon_class(function ($hwid, $events) use ($url, $plugin) {
    file_get_contents($url . '/api/events/', false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json; charset=utf-8",
            'content' => json_encode(['plugin' => $plugin, 'hwid' => $hwid, 'events' => $events]),
            'ignore_errors' => true
        ]
    ]));
}, (array)$state->settings);
$daemon->prepare();
while (1) {
    try {
        $daemon->iteration();
    } catch (Exception $ex) {
        print_r($ex);
        echo PHP_EOL;
        break;
    }
}
$daemon->finish();
exit(0);
