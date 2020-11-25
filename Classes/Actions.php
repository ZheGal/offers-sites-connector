<?php

namespace App\Classes;

use App\Classes\Send;

class Actions
{
    public static function connectorUpdate()
    {
        header("Content-type:text/plain");
        $filename = 'app_' . substr(md5(rand(0,999999999)), 0, 10) . '_file.zip';
        $command_list = implode(" && ", [
            'cd ' . implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']),
            'rm -rf app',
            'mkdir app',
            'cd app',
            "wget -O {$filename} https://github.com/ZheGal/offers-sites-connector/archive/main.zip",
            "unzip -o {$filename}",
            "rm -rf {$filename}",
            "cd offers-sites-connector-main",
            "zip -r app.zip .",
            "mv app.zip ../",
            "cd ../",
            "rm -rf offers-sites-connector-main",
            "unzip -o app.zip",
            "rm -rf app.zip 2>&1"
        ]);
        $command = exec($command_list);
        echo 'done';
        die;
    }

    public static function sendForm()
    {
        $send = new Send();
        $settings = self::get_settings();
        
        if (isset ($settings['partner'])) {
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
        
        if (isset ($settings['partners'])) {
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
        return $json;
    }
}