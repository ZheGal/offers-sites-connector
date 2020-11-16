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
        $settingsPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        if (!file_exists($settingsPath)) {
            $this->new_settings();
            return $this->get_settings();
        }

        $settingsRaw = file_get_contents($settingsPath);

        if (empty($settingsRaw)) {
            $this->new_settings();
            return $this->get_settings();
        }

        $settingsJson = json_decode($settingsRaw, 1);

        if (!is_array($settingsJson)) {
            $this->new_settings();
            return $this->get_settings();
        }

        $this->settings = $settingsJson;
        return true;
    }

    private function new_settings()
    {
        $settingsPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        $settingsExample = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'settings_example.php']);
        $settingsArray = require($settingsExample);
        $settingsJson = json_encode($settingsArray, JSON_PRETTY_PRINT);
        file_put_contents($settingsPath, $settingsJson);
    }
}