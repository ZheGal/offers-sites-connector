<?php

namespace App\Classes;

class SiteCopy
{
    public function __construct()
    {
        header("Content-type:text/plain");

        $this->publicDir = $this->dirs(__DIR__, '..', '..', 'public');
        $this->backupDir = $this->dirs(__DIR__, '..', '..', 'backup');
        $this->publicName = $this->get_public_rand_name();

        $this->backup_dir();
        $this->create_archive();

        $result = "https://{$_SERVER['HTTP_HOST']}/backup/{$this->publicName}.zip";
        echo $result;
    }

    public function create_archive()
    {
        $commands = implode(" && ", [
            "cd {$this->publicDir}",
            "zip -r ../backup/{$this->publicName}.zip ."
        ]);
        return exec($commands);
    }

    public function get_public_rand_name()
    {
        $filename = 'backup_' . substr(md5(rand(0,999999999)), 0, 10) . '_file';
        return $filename;
    }

    public function backup_dir()
    {
        $exists = file_exists($this->backupDir);
        if ($exists) {
            $command = "rm -rf {$this->backupDir} 2>&1";
            $delete = exec($command);
            mkdir($this->backupDir);
        }
        if (!$exists) {
            mkdir($this->backupDir);
        }
    }

    public function dirs(...$array)
    {
        return implode(DIRECTORY_SEPARATOR, $array);
    }
}