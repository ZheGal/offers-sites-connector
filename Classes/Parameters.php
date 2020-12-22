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
        $result['user_ip'] = get_user_ip();

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

    public function get_phone_code_params()
    {
        $result = [];
        return $result;
    }

    public function get_pixel_img_pageview_params()
    {
        $result = [];
        if (isset($_GET['pxl'])) {
            $result['code'] = $_GET['pxl'];
        } elseif (isset($_GET['facebook'])) {
            $result['code'] = $_GET['facebook'];
        } else {
            $result['code'] = ($this->settings->facebook) ? $this->settings->facebook : false;
        }
        return $result;
    }

    public function get_pixel_img_params()
    {
        $result = [];
        if (isset($_GET['pxl'])) {
            $result['code'] = $_GET['pxl'];
        } elseif (isset($_GET['facebook'])) {
            $result['code'] = $_GET['facebook'];
        } else {
            $result['code'] = ($this->settings->facebook) ? $this->settings->facebook : false;
        }
        return $result;
    }

    public function get_pixel_params()
    {
        $result = [];
        if (isset($_GET['pxl'])) {
            $result['code'] = $_GET['pxl'];
        } elseif (isset($_GET['facebook'])) {
            $result['code'] = $_GET['facebook'];
        } else {
            $result['code'] = ($this->settings->facebook) ? $this->settings->facebook : false;
        }
        return $result;
    }

    public function get_metrika_from_preland_params()
    {
        $result = [];
        if (isset($_GET['yand'])) {
            $result['preland_code'] = $_GET['yand'];
        }
        return $result;
    }

    public function get_intl_tel_input_params()
    {
        $result = [];
        $settings = $this->settings;
        $country = (isset($settings->country)) ? $settings->country : 'us';
        $preLocation = new GetLocation();
        $location = $preLocation->get_all();
        
        $result['country'] = (isset($location['country'])) ? $location['country'] : $country;
        
        return $result;
    }
}