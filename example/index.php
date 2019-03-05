<?php

error_reporting(E_ALL); # включаем ошибки
ini_set('display_errors', 1); 

require_once '../HTMLdynamic.php'; # подключаем инструмент

$config = require 'config.php'; # подключаем файл с конфигурацией 

$HTML = (new HTMLdynamic($config))->generate($_GET); # генерируем HTML

echo $HTML; # выводим
