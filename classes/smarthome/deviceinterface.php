<?php

namespace SmartHome;

interface DeviceInterface {

    function getModuleName();

    function getDeviceId();

    function getLastUpdate();

    function __toString();
}
