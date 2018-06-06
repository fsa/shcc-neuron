<?php

require_once 'autoloader.php';

#$obj='O:22:"Yeelight\GenericDevice":13:{s:32:"Yeelight\GenericDevicelocation";s:29:"yeelight://172.17.23.16:55443";s:26:"Yeelight\GenericDeviceid";s:18:"0x0000000005383a0a";s:29:"Yeelight\GenericDevicemodel";s:5:"color";s:30:"Yeelight\GenericDevicefw_ver";s:2:"27";s:31:"Yeelight\GenericDevicesupport";a:20:{i:0;s:8:"get_prop";i:1;s:11:"set_default";i:2;s:9:"set_power";i:3;s:6:"toggle";i:4;s:10:"set_bright";i:5;s:8:"start_cf";i:6;s:7:"stop_cf";i:7;s:9:"set_scene";i:8;s:8:"cron_add";i:9;s:8:"cron_get";i:10;s:8:"cron_del";i:11;s:10:"set_ct_abx";i:12;s:7:"set_rgb";i:13;s:7:"set_hsv";i:14;s:10:"set_adjust";i:15;s:13:"adjust_bright";i:16;s:9:"adjust_ct";i:17;s:12:"adjust_color";i:18;s:9:"set_music";i:19;s:3:"set";}s:29:"Yeelight\GenericDevicepower";s:3:"off";s:30:"Yeelight\GenericDevicebright";s:3:"100";s:34:"Yeelight\GenericDevicecolor_mode";s:1:"2";s:26:"Yeelight\GenericDevicect";s:4:"5070";s:27:"Yeelight\GenericDevicergb";s:6:"ff0000";s:27:"Yeelight\GenericDevicehue";s:3:"130";s:27:"Yeelight\GenericDevicesat";s:3:"100";s:28:"Yeelight\GenericDevicename";s:0:"";}';
#$obj='O:22:"Yeelight\GenericDevice":13:{s:32:"Yeelight\GenericDevicelocation";s:29:"yeelight://172.17.23.17:55443";s:26:"Yeelight\GenericDeviceid";s:18:"0x0000000005438b97";s:29:"Yeelight\GenericDevicemodel";s:7:"bslamp1";s:30:"Yeelight\GenericDevicefw_ver";s:3:"166";s:31:"Yeelight\GenericDevicesupport";a:20:{i:0;s:8:"get_prop";i:1;s:11:"set_default";i:2;s:9:"set_power";i:3;s:6:"toggle";i:4;s:10:"set_bright";i:5;s:8:"start_cf";i:6;s:7:"stop_cf";i:7;s:9:"set_scene";i:8;s:8:"cron_add";i:9;s:8:"cron_get";i:10;s:8:"cron_del";i:11;s:10:"set_ct_abx";i:12;s:7:"set_rgb";i:13;s:7:"set_hsv";i:14;s:10:"set_adjust";i:15;s:13:"adjust_bright";i:16;s:9:"adjust_ct";i:17;s:12:"adjust_color";i:18;s:9:"set_music";i:19;s:3:"set";}s:29:"Yeelight\GenericDevicepower";s:3:"off";s:30:"Yeelight\GenericDevicebright";s:3:"100";s:34:"Yeelight\GenericDevicecolor_mode";s:1:"2";s:26:"Yeelight\GenericDevicect";s:4:"4783";s:27:"Yeelight\GenericDevicergb";s:6:"ff9800";s:27:"Yeelight\GenericDevicehue";s:2:"36";s:27:"Yeelight\GenericDevicesat";s:3:"100";s:28:"Yeelight\GenericDevicename";s:0:"";}';
$yeelight=new \Yeelight\GenericDevice();
$yeelight=unserialize(file_get_contents('lamp2'));
#$yeelight=new Yeelight\GenericDevice();
#var_dump($yeelight);
$yeelight->setPersistent(true);
echo "1: ".$yeelight->actionSetPower(true,2000);
#echo "1: ".$yeelight->actionToggle();
echo $yeelight->actionStartCF(4,0,"1000, 2, 2700, 100, 500, 1,255, 10, 5000, 7, 0,0, 500, 2, 5000, 1");
sleep(3);
#echo "2: ".$yeelight->actionSetRGB("00FF00",2000);
sleep(3);
#echo "3: ".$yeelight->actionSetRGB("FFFFFF",2000);
sleep(3);
echo "4: ".$yeelight->actionSetPower(false,2000);
#echo "4: ".$yeelight->actionToggle();
$yeelight->closeSocket();
die;




$address="172.17.23.17";
$port="55443";

$socket=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
if ($socket<0) {
    throw new Exception('socket_create() failed: '.socket_strerror(socket_last_error())."\n");
}
$result=socket_connect($socket,$address,$port);
if ($result===false) {
    throw new Exception('socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
}
$msg='{"id":1,"method":"toggle","params":[]}'."\r\n";
socket_write($socket,$msg,strlen($msg));
$out=socket_read($socket,1024);
echo $out."\n";
if (isset($socket)) {
    socket_close($socket);
}