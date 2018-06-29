<?php

namespace SmartHome;

interface DeviceInterface {

    function init($device_id,$init_data);

    function getDeviceId();

    function getModuleName();

    function getDeviceStatus();

    function getLastUpdate();
}
