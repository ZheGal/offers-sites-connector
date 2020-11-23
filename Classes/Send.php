<?php

namespace App\Classes;

use App\Classes\Neogara;

class Send
{
    public function neogara()
    {
        $self = new Neogara();
        print_r($_SESSION);
    }

    public function global()
    {

    }

    public function send_mail()
    {
        
    }
}