<?php
require_once('autoload.php');

use App\Classes\General;
use App\Classes\Additional;
use App\Classes\Templates;
use App\Classes\Send;

$site = new General();
$additional = new Additional($site);
$templates = new Templates($site);

// если отправляем форму на send.php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $site->get_file() == 'send.php') {
    $send = new Send();
    die;
}

// Назначаем переменные, которые выводятся на страницах сайта
$site->set('metrika', $templates->get('metrika'));
$site->set('metrika_thanks', $templates->get('metrika_thanks'));
$site->set('metrika_targetclick', $templates->get('metrika_targetclick'));
$site->set('pixel', 'Code Pixel');
$site->set('pixel_img', 'Code Pixel Img');
$site->set('pixel_img_pageview', 'Code Pixel Img PageView');
$site->set('phone_code', 'Phone Code');

$site->run();