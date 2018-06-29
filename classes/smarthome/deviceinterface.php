<?php

namespace SmartHome;

interface DeviceInterface {

    function init($device_id,$init_data);

    function getModuleName();

    function getDeviceId();

    function getLastUpdate();

    function __toString();
}
