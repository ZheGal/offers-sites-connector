<?php

namespace App\Classes;

use App\Classes\Send;

class Actions
{
    public static function connectorUpdate()
    {
        return new \App\Classes\SelfUpdate();
    }

    public static function getLocation()
    {
        header("Content-type:text/plain");
        $location = new \App\Classes\GetLocation();
        $print = $location->get_all();
        echo json_encode($print, JSON_PRETTY_PRINT);
    }

    public static function linkToMetrikaStats()
    {
        $settings = self::get_settings();
        if (isset($settings['yandex']) && $settings['yandex'] != '') {
            $url = 'https://metrika.yandex.ru/dashboard?group=dekaminute&period=today&id=' . $settings['yandex'];
            header("Location:{$url}");
        } else {
            header("Content-type:text/plain");
            echo json_encode([
                'message' => 'Metrika parameter is empty'
            ], JSON_PRETTY_PRINT);
        }
    }

    public static function linkToCloakIt()
    {
        $settings = self::get_settings();
        if (isset($settings['cloakit']) && $settings['cloakit'] != '') {
            $url = 'https://panel.cloakit.space/campaign/' . $settings['cloakit'];
            header("Location:{$url}");
        } else {
            header("Content-type:text/plain");
            echo json_encode([
                'message' => 'Cloakit parameter is empty'
            ], JSON_PRETTY_PRINT);
        }
    }

    public static function sendForm()
    {
        $send = new Send();
        $settings = self::get_settings();
        $action = 'neogara';

        if (isset ($settings['partners']) && !isset($settings['partner'])) {
            $settings['partner'] = $settings['partners'];
        }
        
        if (isset ($settings['partner']) && is_array($settings['partner'])) {
            foreach ($settings['partner'] as $partner => $value) {
                $all[] = $partner;
                if ($value == 1) {
                    $action = $partner;
                break;
                }
            }
            if (empty($action)) {
                $action = $all[0];
            }
        }
        
        if (isset ($settings['partners']) && is_array($settings['partners'])) {
            foreach ($settings['partners'] as $partner => $value) {
                $all[] = $partner;
                if ($value == 1) {
                    $action = $partner;
                break;
                }
            }
            if (empty($action)) {
                $action = $all[0];
            }
        }

        if (isset($_GET['partner'])) {
            $gets = $_GET['partner'];
            if ($gets == 'global' or $gets == 'neogara' or $gets == 'neogara_js') {
                $action = $gets;
            }
        }
        
        if (!empty($action)) {
            $send->$action();
        }
    }

    public static function get_settings()
    {
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        $raw = file_get_contents($path);
        $json = json_decode($raw, 1);
        $json = self::utm_settings($json);
        return $json;
    }
    

    public static function utm_settings($json)
    {
        $get = $_GET;
        if (empty($get)) {
            return $json;
        }

        if (isset($get['pxl'])) {
            $get['facebook'] = $get['pxl'];
        }

        if (isset($get['ynd'])) {
            $get['yandex'] = $get['ynd'];
        }

        foreach ($get as $key => $value) {
            $json[$key] = $value;
        }
        return $json;
    }
}