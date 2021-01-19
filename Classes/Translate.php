<?php

namespace App\Classes;

class Translate
{
    public $base;
    public $lang;

    public function __construct()
    {
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'translates.php']);
        $this->base = require($path);

        $spath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        $settings = json_decode(file_get_contents($spath), 1);

        if (isset($settings['language'])) {
            $this->lang = $settings['language'];
        }
    }

    public function t($str = '')
    {
        $base = $this->base;
        $lang = $this->lang;
        
        if (!isset($base[$str])) {
            return strval($str);
        }
        if (!isset($base[$str][$lang])) {
            return strval($str);
        }
        return strval($base[$str][$lang]);
    }
}