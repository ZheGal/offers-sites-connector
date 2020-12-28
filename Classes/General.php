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
        $this->check_last_symb();
        $this->get_settings();
        $this->utm_settings();
        $this->get_location();
    }

    public function check_last_symb()
    {
        $ref = $this->get_ref();
        $symb = str_split($ref);
        $last = end($symb);
        if ($last == '?') {
            $ref = trim($ref, '?\/');
            $ref = trim($ref, '?\/');
            header("Location:{$ref}");
        }
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
        $view = $this->add_after_submit_script($view);
        $view = $this->change_url_get_ipinfo($view);
        echo $view;
    }

    public function change_url_get_ipinfo($view)
    {
        $url = '//' . $_SERVER['HTTP_HOST'] . '/api/getLocation.me';
        $view = str_replace('$.get("https://ipinfo.io", function() {}, "jsonp").', '$.get("'.$url.'", function() {}, "json").', $view);
        $view = str_replace('$.get(\'https://ipinfo.io?\', function() {}, "jsonp")', '$.get(\''.$url.'\', function() {}, "json")', $view);
        return $view;
    }

    public function check_form($view)
    {
        $form = explode("<form", $view);
        return isset($form[1]);
    }

    public function add_after_submit_script($view)
    {
        $check = explode("<form", $view);
        if (!isset($check[1])) {
            return $view;
        }

        $file = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Templates', 'after_submit.php']);
        if (file_exists($file)) {
            ob_start();
            require_once($file);
            $content = ob_get_contents();
            ob_end_clean();
            $view = str_replace("</body", $content."\n"."</body", $view);
        }
        return $view;
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
            $out[0] = array_unique($out[0]);
            foreach ($out[0] as $form) {
                $view = str_replace($form, "{$form}\n{$input_str}", $view);
            }
        }
        return $view;
    }

    public function check_utm($view)
    {
        $a = (!empty($_GET)) ? '?'.http_build_query($_GET) : false;
        
        if (empty($a)) {
            return $view;
        }
        
        $find = preg_match_all('/action=["|\']([\s\S]+?)["|\']/', $view, $forms);
        
        if ($find) {
            foreach ($forms[0] as $id => $form) {
                $to = $forms[1][$id].$a;
                $from = $forms[1][$id];
                $check = explode($a, $from);
                if (!isset($check[1])) {
                    $rep = str_replace($from, $to, $form);
                    $view = str_replace($form, $rep, $view);
                }
            }
        }
        return $view;
    }

    public function check_errors($view)
    {
        if (!$this->have_forms($view)) {
            return $view;
        }

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

    public function have_forms($view)
    {
        $form = explode("<form", $view);
        if (count($form) > 1) {
            return true;
        }
        return false;
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
        $rootFolder = get_root_folder();
        
        $fileName = trim($requestAr[0], '\/ ');

        if (!empty($rootFolder)) {
            $fileName = removeFolder($fileName, $rootFolder);
        }
        
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

    public function get_ref()
    {
        $schema = ($_SERVER['REQUEST_SCHEME'] == 'http') ? 'http' : 'https';
        return "{$schema}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }

    public function get_relink()
    {
        $settings = $this->settings;
        $utm = $_GET;
        $utm = $this->last_yandex($utm);
        $str = '';
        if (!empty($utm)) {
            $str = '?'.http_build_query($utm);
        }
        if (isset($settings['relink'])) {
            return $settings['relink'].$str;
        }
        return '#';
    }

    public function last_yandex($utm)
    {
        $settings = $this->settings;
        if (isset($utm['ynd'])) {
            $ynd = $utm['ynd'];
            unset($utm['ynd']);
        }
        if (isset($utm['yandex'])) {
            $ynd = ($ynd) ? $ynd : $utm['yandex'];
            unset($utm['yandex']);
        }
        $utm['yand'] = ($ynd) ? $ynd : $settings['yandex'];
        return $utm;
    }

    public function get_metrika_code()
    {
        $settings = $this->settings;
        return $settings['yandex'];
    }

    public function get_utm_form_link()
    {
        $utm = explode("?", $_SERVER["REQUEST_URI"]);
        if (!empty($utm[1])) {
            return "?{$utm[1]}";
        }
        return false;
    }

    public function get_location()
    {
        $app = new GetLocation();
        return $app->get_all();
    }
}