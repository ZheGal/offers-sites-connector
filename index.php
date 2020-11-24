<?php
require_once('autoload.php');

session_start();

use App\Classes\General;
use App\Classes\Router;
use App\Classes\Templates;

$site = new General();
$templates = new Templates($site);

$router = new Router();

// если путь совпадает с тем, который есть в роутере, подключаем его
// иначе ищем файл в папке public
if ($router->covergence()) {
    $router->connect();
}

// Назначаем переменные, которые выводятся на страницах сайта
$site->set('metrika', $templates->get('metrika'));
$site->set('metrika_thanks', $templates->get('metrika_thanks'));
$site->set('metrika_targetclick', $templates->get('metrika_targetclick'));
$site->set('pixel', 'Code Pixel');
$site->set('pixel_img', 'Code Pixel Img');
$site->set('pixel_img_pageview', 'Code Pixel Img PageView');
$site->set('phone_code', 'Phone Code');
$site->set('partner_name', $site->get_partner());

// Если пользователь заполнял форму и его редиректнуло на главную, заполнить поля
if (isset($_SESSION['form_fields'])) {
    $site->inputs_fill();
}

$site->run();