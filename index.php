<?php
require_once('autoload.php');

use App\Classes\General;
use App\Classes\Templates;

$site = new General();
$templates = new Templates($site);

// Назначаем переменные, которые выводятся на страницах сайта
$site->set('metrika', $templates->get('metrika'));
$site->set('metrika_thanks', $templates->get('metrika_thanks'));
$site->set('metrika_targetclick', $templates->get('metrika_targetclick'));
$site->set('pixel', 'Code Pixel');
$site->set('pixel_img', 'Code Pixel Img');
$site->set('pixel_img_pageview', 'Code Pixel Img PageView');
$site->set('phone_code', 'Phone Code');

$site->run();