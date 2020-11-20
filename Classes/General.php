<?php

namespace App\Classes;

class General
{
    public $settings;
    public $variables;

    public function __construct()
    {
        $this->check_htaccess();
        $this->get_settings();
        $this->utm_settings();
    }

    public function get_file()
    {
        $reqAr = explode("?", $_SERVER['REQUEST_URI']);
        $req = $reqAr[0];
        $file = explode("/", $req);
        return end($file);
    }

    public function set($key, $value = null)
    {
        if (!empty($key) || !empty($value)) {
            $this->variables[$key] = $value;
        }
    }

    public function run()
    {
        $view = $this->render();
        if ($this->get_partner() == 'neogara_js') {
            $view = $this->add_neo_js($view);
        }
        echo $view;
    }

    private function render()
    {
        $vars = $this->variables;
        $requestAr = explode("?", $_SERVER['REQUEST_URI']);
        $fileName = trim($requestAr[0], '\/ ');
        $fileName = (empty($fileName)) ? 'index.php' : $fileName;
        
        $fileNamePath = $this->get_file_path($fileName);

        if ($fileNamePath) {
            extract($vars);
            ob_start();
            require_once($fileNamePath);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        return $this->error(404);
    }

    public function get_settings()
    {
        $settingsPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        if (!file_exists($settingsPath)) {
            $this->new_settings();
            return $this->get_settings();
        }

        $settingsRaw = file_get_contents($settingsPath);

        if (empty($settingsRaw)) {
            $this->new_settings();
            return $this->get_settings();
        }

        $settingsJson = json_decode($settingsRaw, 1);

        if (!is_array($settingsJson)) {
            $this->new_settings();
            return $this->get_settings();
        }

        $this->settings = $settingsJson;
        return true;
    }

    public function new_settings()
    {
        $settingsPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        $settingsExample = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'settings_example.php']);
        $settingsArray = require($settingsExample);
        $settingsJson = json_encode($settingsArray, JSON_PRETTY_PRINT);
        file_put_contents($settingsPath, $settingsJson);
    }

    private function error($code)
    {
        header("Location:/");
        // return http_response_code($code);
        // die;
    }

    private function get_file_path($path)
    {
        $dir = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'public', $path]);
        if (file_exists($dir)) {
            return $dir;
        }
        return false;
    }

    public function utm_settings()
    {
        $get = $_GET;
        if (empty($get)) {
            return false;
        }

        if (isset($get['pxl'])) {
            $get['facebook'] = $get['pxl'];
        }

        if (isset($get['ynd'])) {
            $get['yandex'] = $get['ynd'];
        }

        foreach ($get as $key => $value) {
            $this->settings[$key] = $value;
        }
    }

    public function get_partner()
    {
        $partners = $this->settings['partner'];
        foreach ($partners as $partner => $value) {
            $all[] = $partner;
            if ($value == 1) {
                return $partner;
            }
        }
        return $all[0];
    }

    public function add_neo_js($view)
    {
        $needed = ['group', 'offer', 'pid', 'return'];
        foreach ($this->settings as $key => $value) {
            if (in_array($key, $needed)) {
                $array[$key] = $value;
            }
        }
        
        $query = array_diff($array, ['']);
        $http_query = (!empty($query)) ? '?' . http_build_query($query) : null;
        $link = "https://admin.neogara.com/script/neo_form.js{$http_query}";
        $script = "<script src=\"{$link}\"></script>";

        return str_replace('</head>', "{$script}\n</head>", $view);
    }

    public function check_htaccess()
    {
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '.htaccess']);
        $pathExm = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'htaccess.example']);
        if (!file_exists($path)) {
            $raw = file_get_contents($pathExm);
            file_put_contents($path, $raw);
        }
    }
}