<?php

function get_user_ip()
{
    return '185.41.250.246';
    $c = false;
    
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $c = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } else {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
             $c = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
             $c = $_SERVER['REMOTE_ADDR'];
        } else {
             $c = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }

    return $c;
}

function dd($d)
{
    echo "<pre style=\"color: #000;background-color: #e6e6e6;padding: 1rem;\">";
    var_dump($d);
    echo "</pre>";
    die;
}