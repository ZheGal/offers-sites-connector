<?php

namespace App\Classes;

class Neogara
{
    public function click_reg()
    {
        $method = 'POST';
        $url = 'https://admin.neogara.com/clicks';
    }

    public function lead_reg()
    {
        $method = 'POST';
        $url = 'https://admin.neogara.com/register/lid';
    }
}