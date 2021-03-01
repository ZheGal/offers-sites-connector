<?php

namespace App\Classes;

class SelfUpdate
{
    public $repository = 'https://github.com/ZheGal/offers-sites-connector/archive/main.zip';
    public $folder = 'offers-sites-connector-main';

    public function __construct()
    {
        header("Content-type:text/plain;charset=utf-8");
        $filename = $this->downloadFunctions(); // скачать функции
        if (file_exists($filename['path'])) {
            $unpack = $this->unpackArchive($filename); // распаковать архив
            $delete = $this->deleteIsset(); // удалить содержимое папки app
            $move = $this->moveFromIsset();
            $this->update_htaccess();
        }
    }
    
    public function moveFromIsset()
    {
        $app = '../app';
        $new = "../{$this->folder}";
        if (rename($new, $app)) {
            echo 'Success';
        } else {
            echo exec("mv {$new} {$app}");
            echo 'Trying to rename new folder. Update page to check';
        }
    }

    
    public function downloadFunctions()
    {
        $rep = $this->repository;
        $filename = 'app_' . substr(md5(rand(0,999999999)), 0, 10) . '_file.zip';
        $load = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', $filename]);
        
        $command = "wget -O {$load} {$rep}";
        $done = exec($command);

        if (!file_exists($load)) {
            echo "Cannot download file\n";
            return false; 
        }
        echo 'Archive downloaded'."\n";
        return [
            'path' => $load,
            'filename' => $filename
        ];
    }

    public function deleteIsset()
    {
        echo 'Deleting app folder';
        echo exec('rm -rf ../app')."\n";
    }

    public function unpackArchive($filename)
    {
        if (file_exists($filename['path'])) {
            $root = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']);

            $folder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', $this->folder]);
            if (file_exists($folder) && is_dir($folder)) {
                echo 'Deleting exists folder from archive:';
                echo exec("rm -rf {$folder}")."\n";
            }
            
            echo "Trying to unzip archive: ";
            echo exec("cd {$root} && unzip -o {$filename['filename']}") . "\n";
            
            echo "Deleting archive: ";
            echo unlink($filename['path']) . "\n";
        }
    }

    public function update_htaccess()
    {
        $path = '../.htaccess';
        
        if (file_exists('htaccess.example')) {
            file_put_contents($path, file_get_contents('htaccess.example'));
        }
    }
}