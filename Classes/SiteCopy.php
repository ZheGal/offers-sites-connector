<?php

namespace App\Classes;

class SiteCopy
{
    public function __construct()
    {
        header("Content-type:text/plain");

        $this->publicDir = $this->dirs(__DIR__, '..', '..', 'public');
        $this->backupDir = $this->dirs(__DIR__, '..', '..', 'backup');

        $this->backup_dir();
        $this->create_archive();

        $result = "https://{$_SERVER['HTTP_HOST']}/backup/public.zip";
        echo $result;
    }

    public function create_archive()
    {
        $commands = implode(" && ", [
            "cd {$this->publicDir}",
            "zip -r ../backup/public.zip ."
        ]);
        return exec($commands);
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