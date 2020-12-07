<?php

namespace App\Classes;

use App\Classes\Neogara;

class General
{
    public $settings;
    public $variables;
    public $location;
    public $inputs_fill;

    public function __construct()
    {
        $this->check_htaccess();
        $this->check_folder();
        $this->get_settings();
        $this->utm_settings();
        $this->get_location();
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

    public function inputs_fill()
    {
        $this->inputs_fill = 1;
    }

    public function inputs_fill_action($view = '')
    {
        $inputs = [];
        if (!empty($_SESSION['form_fields'])) {
            foreach ($_SESSION['form_fields'] as $key => $value) {
                if ($key[0] != '_') {
                    $inputs[$key] = $value;
                }
            }
        }
        $inputs = array_diff($inputs, ['']);

        preg_match_all('/<input.*?name=[\'|"](.*?)[\'|"].*?>/', $view, $forms);
        if (!empty($forms)) {
            foreach($forms[0] as $k => $input) {
                $input_key = $forms[1][$k];
                if (isset($inputs[$input_key])) {
                    $add = "value=\"{$inputs[$input_key]}\"";
                    $input_b = str_replace('>', ' '.$add.'>', $input);
                    $view = str_replace($input, $input_b, $view);
                }
            }
        }
        unset($_SESSION['form_fields']);
        return $view;
    }

    public function run()
    {
        $view = $this->render();
        if (!$view) {
            return false;
        }

        if ($this->inputs_fill) {
            $view = $this->inputs_fill_action($view);
        }
        $current = $this->get_ref();
        if ($this->get_partner() == 'neogara') {
            $params = ['location' => $this->location];
            $neogara = new Neogara($params);
            $view = $neogara->click_reg($view);
        } else {
            $view = $this->get_ref_field($view);
        }

        if ($this->get_partner() == 'neogara_js') {
            $view = $this->add_neo_js($view);
        }

        $view = $this->check_utm($view);
        $view = $this->check_errors($view);

        unset($_SESSION);

        $view = $this->add_utm_to_links($view);
        echo $view;
    }

    public function add_utm_to_links($view)
    {
        $getstr = '';
        if (!empty($_GET)) {
            $getstr = http_build_query($_GET);
        }
        $getstr = (!empty($getstr)) ? "?{$getstr}" : false;
        $view = str_replace('<a href="/"', '<a href="/'.$getstr.'"', $view);
        $view = str_replace('<a href=\'/\'', '<a href=\'/'.$getstr.'\'', $view);

        return $view;
    }

    public function get_ref_field($view)
    {
        $ref = $this->get_ref();
        $input_str = "\n\t<input type=\"hidden\" name=\"_ref\" value=\"{$ref}\">";
        $find = preg_match_all("(<form[^<>]+>)", $view, $out);
        
        if (isset($out[0])) {
            foreach ($out[0] as $form) {
                $view = str_replace($form, "{$form}\n{$input_str}", $view);
            }
        }
        return $view;
    }

    public function check_utm($view)
    {
        $a = (!empty($_GET)) ? '?'.http_build_query($_GET) : false;
        
        $find = preg_match_all('/action=["|\']([\s\S]+?)["|\']/', $view, $forms);
        
        if ($find) {
            foreach ($forms[0] as $id => $form) {
                $rep = str_replace($forms[1][$id], $forms[1][$id].$a, $form);
                $view = str_replace($form, $rep, $view);
            }
        }
        return $view;
    }

    public function check_errors($view)
    {
        $q = '';
        if (isset($_SESSION['error'])) {
            $q = '<script>document.addEventListener("DOMContentLoaded", function(){';
            foreach ($_SESSION['error'] as $error) {
                $q .= "alert('{$error}');";
            }
            $q .= '});</script>';
        }
        unset($_SESSION['error']);
        $view = str_replace('</body', $q."\n</body", $view);
        return $view;
    }

    public function isCloakit()
    {
        $settings = $this->settings;
        return (isset($settings['cloakit']) && !empty($settings['cloakit']));
    }

    public function render()
    {
        $vars = $this->variables;
        $requestAr = explode("?", $_SERVER['REQUEST_URI']);
        $fileName = trim($requestAr[0], '\/ ');
        $fileName = (empty($fileName)) ? 'index.php' : $fileName;

        if ($this->isCloakit() && $fileName == 'index.php') {
            $cloak = new \App\Classes\Cloakit($this->settings);
            $fileName = $cloak->connect();
        }

        $fileNamePath = $this->get_file_path($fileName);

        // print_r($fileNamePath);
        if ($fileNamePath) {
            extract($vars);
            ob_start();
            require_once($fileNamePath);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        return false;
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

    public function error($code)
    {
        switch ($code) {
            case '404':
                header("HTTP/1.0 404 Not Found");
            break;
        }
    }

    public function get_file_path($path)
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
        if (isset($_GET['partner'])) {
            $gets = $_GET['partner'];
            if ($gets == 'global' or $gets == 'neogara' or $gets == 'neogara_js') {
                return $gets;
            }
        }
        if (isset($this->settings['partner'])) {
            $partners = $this->settings['partner'];
        } elseif (isset($this->settings['partners'])) {
            $partners = $this->settings['partners'];
        }
        if (!empty($partners)) {
            foreach ($partners as $partner => $value) {
                $all[] = $partner;
                if ($value == 1) {
                    return $partner;
                }
            }
            return $all[0];
        }
        else return false;
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
        $link = "https://admin.neogara.com/script/neo_form_js.js{$http_query}";
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
            header("Location:/");
        }
    }

    public function get_ip_info()
    {
        $ip = $this->get_user_ip();
        $url = "http://ipinfo.io/{$ip}?token=8b50524357b6bc";
        return $url;
    }

    public function get_user_ip()
    {
        $c = false;
        
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $c = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                 $c = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
                 $c = $_SERVER['REMOTE_ADDR'];
            } else {
                 $c = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }

        return $c;
    }

    public function check_folder()
    {
        $path =  implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'public']);
        if (!file_exists($path)) {
            mkdir($path);
        }

        if (file_exists($path) && !is_dir($path)) {
            unlink($path);
            mkdir($path);
        }
    }

    public function get_location()
    {
        if (isset($_SESSION['location']) && !empty($_SESSION['location'])) {
            $this->location = $_SESSION['location'];
            return true;
        }
        $url = $this->get_ip_info();
        $raw = file_get_contents($url);
        $json = json_decode($raw, 1);
        if (!empty($raw) && is_array($json)) {
            $_SESSION['location'] = $json;
            $this->location = $json;
            return true;
        }
        return false;
    }

    public function get_ref()
    {
        $schema = ($_SERVER['REQUEST_SCHEME'] == 'http') ? 'http' : 'https';
        return "{$schema}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }
}