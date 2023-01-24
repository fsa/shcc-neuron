<?php

require_once '../../../vendor/autoload.php';
$response = App::initJson();
$daemon_host = App::getSettings('daemon-ip', '127.0.0.1');
if ($daemon_host != getenv('REMOTE_ADDR')) {
    $response->returnError(403);
}
$request = file_get_contents('php://input');
$json = json_decode($request);
if (!$json) {
    $response->returnError(400);
}
if (!isset($json->plugin)) {
    $response->returnError(400, 'Plugin name required');
}
$plugin_info = App::plugins()->getPlugin($json->plugin);
if (!$plugin_info) {
    $response->returnError(400, 'Plugin not found');
}
if (!isset($plugin_info->daemon)) {
    $response->returnError(400, 'Plugin Daemon not found');
}
$db = App::deviceDatabase();
$factory = App::deviceFactory();
$storage = App::deviceStorage();
$devices = $db->getAll($json->plugin)->fetchAll();
foreach ($devices as $item) {
    $device = $factory->create($json->plugin, $item->hwid, $item->class, $item->properties);
    if ($device) {
        $storage->setNx($json->plugin . ':' . $item->hwid, $device);
    }
}
$response->json(['daemon' => $plugin_info->daemon, 'settings' => array_merge((array)$plugin_info->settings, App::getSettings($json->plugin, []))]);
