<?php

namespace App\Classes;

class Parameters
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
        $result['code'] = ($this->settings->yandex) ? $this->settings->yandex : false;
        $result['user_ip'] = $this->get_user_ip();

        return $result;
    }

    public function get_metrika_thanks_params()
    {
        $result = [];
        $result['code'] = ($this->settings->yandex) ? $this->settings->yandex : false;

        return $result;
    }

    public function get_metrika_targetclick_params()
    {
        $result = [];
        $result['code'] = ($this->settings->yandex) ? $this->settings->yandex : false;

        return $result;
    }

    public function get_user_ip()
    {
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
}