<?php
require_once('autoload.php');
require_once('./vendor/autoload.php');

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
$site->set('pixel', $templates->get('pixel'));
$site->set('pixel_img', $templates->get('pixel_img'));
$site->set('pixel_img_pageview', $templates->get('pixel_img_pageview'));
$site->set('phone_code', $templates->get('phone_code'));
$site->set('partner_name', $site->get_partner());
$site->set('prokl_link', $site->get_relink());
$site->set('metrika_code', $site->get_metrika_code());
$site->set('metrika_preland', $templates->get('metrika_from_preland'));
$site->set('utm_form', $site->get_utm_form_link());

// Если пользователь заполнял форму и его редиректнуло на главную, заполнить поля
if (isset($_SESSION['form_fields'])) {
    $site->inputs_fill();
}

$site->run();

// helpers

function dd($d)
{
    echo "<pre style=\"color: #000;background-color: #e6e6e6;padding: 1rem;\">";
    var_dump($d);
    echo "</pre>";
    die;
}