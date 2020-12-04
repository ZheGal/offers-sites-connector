<?php

namespace App\Classes;

class BackupRemove
{
    public function __construct()
    {
        $file = $this->get_filename();
        if ($file) {
            $path = $this->backup_path();
            if ($path) {
                $this->remove_path();
            }
        }
    }

    public function remove_path()
    {
        echo 'Deleting exists folder from archive:';
        echo exec("rm -rf {$this->path}")."\n";
    }

    public function get_filename()
    {
        if (isset($_GET['file'])) {
            $this->filename = $_GET['file'];
            return true;
        }
        return false;
    }

    public function backup_path()
    {
        $dir = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'backup', $this->filename]);
        if (file_exists($dir)) {
            $this->path = $dir;
            return true;
        }
    }
}