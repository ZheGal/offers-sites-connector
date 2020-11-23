<?php

namespace App\Classes;

use App\Classes\Send;

class Actions
{
    public static function connectorUpdate()
    {
        echo __DIR__;
        // здесь мы должны перейти в папку app и сделать git pull через exec
        die;
    }

    public static function sendForm()
    {
        $send = new Send();
        $send->neogara();
    }
}