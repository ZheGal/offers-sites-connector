<?php

namespace App\Classes;

class Send extends General
{
    public function __construct()
    {
        $this->get_settings();
        $this->utm_settings();
        $partner = $this->get_partner();
        
    }
}