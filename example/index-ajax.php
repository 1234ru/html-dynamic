<?php

error_reporting(E_ALL); # включаем ошибки
ini_set('display_errors', 1); 

// require_once '../HTMLdynamic.php'; # подключаем инструмент

require_once '../vendor/autoload.php';
require_once '../legacy/_load.php';

$config = require 'config.php'; # подключаем файл с конфигурацией 

$HTML = (new \One234ru\HTMLdynamic($config))->generate($_GET); # генерируем HTML

echo $HTML; # выводим
