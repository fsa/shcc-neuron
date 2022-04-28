<?php

namespace FSA\SmartHome\TTS;

interface ProviderInterface {

    function getVoiceFile($text);
}
