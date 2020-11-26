<?php

namespace App\Classes;

use App\Classes\Neogara;
use App\Classes\GlobalMaxis;
use App\Classes\Translate;

class Send
{
    public $translate;
    public $settings;

    public function __construct()
    {
        $this->translate = new Translate();
        $this->get_settings();
        $this->check_empty_fields();
        $this->check_phone_code();
    }

    public function neogara()
    {
        $self = new Neogara();
        $send = $self->lead_reg();
    }

    public function get_settings()
    {
        $settingsPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        $settingsRaw = file_get_contents($settingsPath);
        $settingsJson = json_decode($settingsRaw, 1);
        $this->settings = $settingsJson;
        return true;
    }

    public function global()
    {
        $self = new GlobalMaxis();
    }

    public function check_empty_fields()
    {
        $translate = $this->translate;
        $result = false;
        if (empty($_POST['firstname'])) {
            $_SESSION['error'][] = $translate->t("First name is empty");
            $result = 1;
        }
        
        if (empty($_POST['lastname'])) {
            $_SESSION['error'][] = $translate->t("Last name is empty");
            $result = 1;
        }
        
        if (empty($_POST['email'])) {
            $_SESSION['error'][] = $translate->t("Email is empty");
            $result = 1;
        }
        
        if (empty($_POST['phone_number'])) {
            $_SESSION['error'][] = $translate->t("Phone number is empty");
            $result = 1;
        }

        $back = $_REQUEST['_ref'];
        if ($result) {
            $_SESSION['form_fields'] = $_POST;
            header("Location:{$back}");
        }
    }

    public function check_phone_code()
    {
        $loc = ($_SESSION['location']['country']) ? $_SESSION['location']['country'] : false;
        if (isset($_POST['phone_code'])) {
            $code = $_POST['phone_code'];
        } else {
            $code = $this->get_code_by_country($loc);
        }

        if (empty($code)) {
            return false;
        }

        $phone = $_POST['phone_number'];
        if ($code != $phone) {
            unset($_POST['phone_code']);
            unset($_REQUEST['phone_code']);

            $_POST['phone_number'] = $code.$phone;
            $_REQUEST['phone_number'] = $code.$phone;
        }
    }

    public function get_code_by_country($code = '')
    {
        if (empty($code)) {
            return false;
        }

        $array = [
            'PL' => '+48',
            'RU' => '+7',
            'UA' => '+380',
        ];
        return $array[$code];
    }
}