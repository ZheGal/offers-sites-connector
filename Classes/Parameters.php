<?php

namespace App\Classes;

class Parameters extends General
{
    public function __construct()
    {
        $settings_path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        $settings = file_get_contents($settings_path);
        $this->settings = json_decode($settings);
    }

    public function get_metrika_params()
    {
        $result = [];
        if (isset($_GET['yandex'])) {
            $result['code'] = $_GET['yandex'];
        } else {
            $result['code'] = ($this->settings->yandex) ? $this->settings->yandex : false;
        }
        $result['user_ip'] = $this->get_user_ip();

        return $result;
    }

    public function get_metrika_thanks_params()
    {
        $result = [];
        if (isset($_GET['yandex'])) {
            $result['code'] = $_GET['yandex'];
        } else {
            $result['code'] = ($this->settings->yandex) ? $this->settings->yandex : false;
        }

        return $result;
    }

    public function get_metrika_targetclick_params()
    {
        $result = [];
        if (isset($_GET['yandex'])) {
            $result['code'] = $_GET['yandex'];
        } else {
            $result['code'] = ($this->settings->yandex) ? $this->settings->yandex : false;
        }

        return $result;
    }
}