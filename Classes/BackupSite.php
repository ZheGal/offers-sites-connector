<?php

namespace App\Classes;

class BackupSite
{
    public $from;
    public $fromLink;

    public function __construct()
    {
        header("Content-type:text/plain");
        $this->check_get();
        $this->fromLink = $this->from_link();
        $this->download_unpack();
    }

    public function check_get()
    {
        $get = $_GET;
        $get = array_diff($get, ['']);
        if (!isset($get['from']) or empty($get['from'])) {
            die('Backup FROM what?');
        } else {
            $this->from = $get['from'];
        }
    }

    public function from_link()
    {
        $check = file_get_contents("http://{$this->from}/api/copySite.me");
        if (empty($check)) {
            die('It\'s impossible');
        }
        return $check;
    }

    public function download_unpack()
    {
        
    }
}