<?php

require_once 'autoloader.php';
$q=new Tts\Queue;
$q->addMessage('Новосибирское время 23 часа');
$q->addMessage('Температура воздуха +16 градусов цельсия');