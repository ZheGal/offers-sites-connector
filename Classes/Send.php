<?php

namespace App\Classes;

use App\Classes\Neogara;

class Send
{
    public function __construct()
    {
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

    }

    public function send_mail()
    {
        
    }

    public function check_empty_fields()
    {
        $result = false;
        if (empty($_POST['firstname'])) {
            $_SESSION['error'][] = "First name is empty";
            $result = 1;
        }
        
        if (empty($_POST['lastname'])) {
            $_SESSION['error'][] = "Last name is empty";
            $result = 1;
        }
        
        if (empty($_POST['email'])) {
            $result = 1;
        }
        
        if (empty($_POST['phone_number'])) {
            $result = 1;
        }

        $back = $_SESSION['ref'];
        if ($result) {
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