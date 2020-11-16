<?php

namespace App\Classes;

class General
{
    private $settings;

    public function __construct()
    {
        $this->get_settings();
    }

    private function get_settings()
    {
        $settingsPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'settings.json']);
        echo $settingsPath;
    }
}