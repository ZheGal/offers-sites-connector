<?php

namespace App\Classes;

class GlobalMaxis
{
    public function __construct()
    {
        $empty = $this->check_empty();
        if ($empty) {
            header("Location:{$_POST['_ref']}");
        }

        $this->check_requests();
    }

    public function check_empty()
    {
        $error = 0;

        if (empty($_POST['firstname'])) {
            $error = 1;
        }
        if (empty($_POST['lastname'])) {
            $error = 1;
        }
        if (empty($_POST['email'])) {
            $_SESSION['error'][] = "Email is empty";
            $error = 1;
        }
        if (empty($_POST['phone_number'])) {
            $_SESSION['error'][] = "Phone number is empty";
            $error = 1;
        }

        return $error;
    }

    public function check_requests()
    {
        print_r($_REQUEST);
    }
}