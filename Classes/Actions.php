<?php

namespace App\Classes;

use App\Classes\Send;

class Actions
{
    public static function connectorUpdate()
    {
        return new \App\Classes\SelfUpdate();
    }

    public static function backupRemoteDelete()
    {
        return new \App\Classes\BackupRemove();
    }

    public static function makePublicCopy()
    {
        return new \App\Classes\SiteCopy();
    }

    public static function backupSite()
    {
        return new \App\Classes\BackupSite();
    }

    public static function updateSettings()
    {
        echo 'none';
    }

    public static function linkToMetrikaStats()
    {
        $settings = self::get_settings();
        if (isset($settings['yandex']) && $settings['yandex'] != '') {
            $url = 'https://metrika.yandex.ru/dashboard?group=dekaminute&period=today&id=' . $settings['yandex'];
            header("Location:{$url}");
        } else {
            header("Content-type:text/plain");
            echo 'Metrika parameter is empty';
        }
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