<?php

namespace App\Classes;

use App\Classes\Send;

class Actions
{
    public static function connectorUpdate()
    {
        header("Content-type:text/plain");
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..']) . DIRECTORY_SEPARATOR;
        $command = "bash /var/www/www-root/data/www/cryptobaafank.info/app/update.sh";
        echo json_encode([
            'command' => 'git pull',
            'message' => exec($command)
        ]);
        // здесь мы должны перейти в папку app и сделать git pull через exec
        die;
    }

    public static function sendForm()
    {
        $send = new Send();
        $settings = self::get_settings();
        
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

        if (isset($_GET['partner'])) {
            $gets = $_GET['partner'];
            if ($gets == 'global' or $gets == 'neogara' or $gets == 'neogara_js') {
                $action = $gets;
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