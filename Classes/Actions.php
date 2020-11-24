<?php

namespace App\Classes;

use App\Classes\Send;

class Actions
{
    public static function connectorUpdate()
    {
        echo __DIR__;
        // здесь мы должны перейти в папку app и сделать git pull через exec
        die;
    }

    public static function sendForm()
    {
        $send = new Send();
        $settings = self::get_settings();

        if (isset($_GET['partner'])) {
            $gets = $_GET['partner'];
            if ($get == 'global' || $get == 'neogara' || $get == 'neogara_js') {
                $action = $get;
            }
        } else {
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
        
        $send->$action();
    }

    public static function get_settings()
    {
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        $raw = file_get_contents($path);
        $json = json_decode($raw, 1);
        return $json;
    }
}