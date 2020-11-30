<?php

namespace App\Classes;

class BackupSite
{
    public $from;
    public $fromLink;

    public function __construct()
    {
        header("Content-type: text/plain");
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
        $check = file_get_contents("https://{$this->from}/api/copySite.me");
        if (empty($check)) {
            die('It\'s impossible');
        }
        return str_replace('http://', 'https://', $check);
    }

    public function download_unpack()
    {
        $done = [];
        $this->fromLink = strval(trim($this->fromLink));
        $new_dir = $this->dirs(__DIR__, '..', '..', 'new');
        if (file_exists($new_dir) && is_dir($new_dir)) {
            $a = exec("rm -rf {$new_dir}/");
        }

        if (file_exists($new_dir) && is_file($new_dir)) {
            $a =  exec("rm -rf {$new_dir}");
        }
        mkdir($new_dir);

        $command = "cd {$new_dir} && wget -O new.zip {$this->fromLink} 2>&1";
        $done[] = exec($command);

        $command = "cd {$new_dir} && unzip -o new.zip && rm -rf new.zip && rm -rf ../public && mv ../new ../public 2>&1";
        $done[] = exec($command);
        $done[] = 'Done';
        echo implode("\n", $done);
    }

    public function dirs(...$array)
    {
        return implode(DIRECTORY_SEPARATOR, $array);
    }
}