<?php
require_once('autoload.php');

use App\Classes\General;
use App\Classes\Additional;
use App\Classes\Parameters;

$site = new General();
$additional = new Additional();
$parameters = new Parameters();

// Назначаем переменные, которые выводятся на страницах сайта
$site->set('a', 'shit');

$site->run();