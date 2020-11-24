<?php

namespace App\Classes;

use App\Classes\Neogara;
use App\Classes\GlobalMaxis;
use App\Classes\Translate;

class Send
{
    public $translate;

    public function __construct()
    {
        $this->translate = new Translate();
        $this->check_empty_fields();
        $this->check_phone_code();
    }

    public function neogara()
    {
        $self = new Neogara();
        $send = $self->lead_reg();
    }

    public function global()
    {
        $self = new GlobalMaxis();
    }

    public function send_mail()
    {
        
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
        if (isset($_POST['phone_code'])) {
            $code = $_POST['phone_code'];
            $phone = $_POST['phone_number'];

            if ($code != $phone) {
                unset($_POST['phone_code']);
                unset($_REQUEST['phone_code']);

                $_POST['phone_number'] = $code.$phone;
                $_REQUEST['phone_number'] = $code.$phone;
            }
        }
    }
}