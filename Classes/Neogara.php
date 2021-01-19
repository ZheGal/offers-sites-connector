<?php

namespace App\Classes;

use App\Classes\Translate;

class Neogara
{
    private $settings;
    private $location;
    public $translate;

    public function __construct($params = [])
    {
        $this->translate = new Translate();
        $this->get_location();
        $settings_path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        if (file_exists($settings_path)) {
            $this->settings = json_decode(file_get_contents($settings_path), 1);
        }
        $this->utm_settings();
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function click_reg($view = '')
    {
        $settings = $this->settings;
        $subs = getSubs($settings);
        $find = preg_match_all("(<form[^<>]+>)", $view, $out);
        if (isset($out[0]) && empty($out[0])) {
            return $view;
        }
        
        $array = json_encode([
            'pid' => $this->get_pid(),
            'pipeline' => $this->get_pipeline(),
            'ref' => $this->get_ref(),
            'ip' => get_user_ip(),
            'city' => $this->get_user_city(),
            'country' => $this->get_user_country()
        ]);

        $domain = $this->get_neogara_server_domain();
        $url = "https://{$domain}/clicks";
        
        $request = $this->send_request([
            'url' => $url,
            'content' => $array
        ]);

        if (isset($request['error'])) {
            if (is_array($request['message'])) {
                foreach ($request['message'] as $mes) {
                    $_SESSION['error'][] = "{$request['statusCode']} {$request['error']}: {$mes}";
                }
            } else {
                $_SESSION['error'][] = "{$request['statusCode']} {$request['error']}: {$request['message']}";
            }
        }
        
        $inputs = array_diff([
            '_ref' => $this->get_ref(),
            '_click' => (isset($request['id'])) ? $request['id'] : false
        ], ['']);
        $inputs = array_merge($inputs, $subs);

        $input_ar = [];
        $input_str = '';
        foreach ($inputs as $key => $value) {
            $input_ar[] = "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\">";
        }
        $input_str = implode("\n", $input_ar);

        $find = preg_match_all("(<form[^<>]+>)", $view, $out);
        
        if (isset($out[0])) {
            $out[0] = array_unique($out[0]);
            foreach ($out[0] as $form) {
                $view = str_replace($form, "{$form}\n{$input_str}", $view);
            }
        }
        return $view;
    }

    public function lead_reg()
    {
        $comes = requestUnderscoresDelete();
        $prepare = [
            'pid' => $this->get_pid(),
            'pipeline' => $this->get_pipeline(),
            'firstname' => str_replace(' ', '', ucfirst(trim($_REQUEST['firstname']))),
            'lastname' => str_replace(' ', '', ucfirst(trim($_REQUEST['lastname']))),
            'phone' => $this->get_phone(),
            'email' => $this->get_email(),
            'ref' => $this->get_ref_lead(),
            'ip' => get_user_ip(),
            'city' => $this->get_user_city(),
            'country' => $this->get_user_country(),
            'click' => $this->get_click_id(),
        ];

        $array = json_encode(
            array_merge($comes, $prepare)
        );

        $domain = $this->get_neogara_server_domain();
        $url = "https://{$domain}/register/lid";
        
        $request = $this->send_request([
            'url' => $url,
            'content' => $array
        ]);
        
        if (isset($request['error'])) {
            if (is_array($request['message'])) {
                foreach ($request['message'] as $mes) {
                    $_SESSION['error'][$request['statusCode']] = "{$request['statusCode']} {$request['error']}: {$mes}";
                }
            } else {
                $_SESSION['error'][$request['statusCode']] = "{$request['statusCode']} {$request['error']}: {$request['message']}";
            }
            $back = ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
            header("Location:{$back}");
        }

        if ($request['result'] == 'ok') {
            $back = "/".$this->settings['return'];
            $request = explode("?", $_SERVER['REQUEST_URI']);
            if (isset($request[1])) {
                $back = "{$back}?{$request[1]}";
            }
            unset($_SESSION);
            header("Location:{$back}");
        }
    }

    public function get_location()
    {
        if (empty($this->location)) {
            $loc = new GetLocation();
            $this->location = $loc->get_all();
        }
    }

    public function get_click_id()
    {
        return (isset($_POST['_click'])) ? $_POST['_click'] : false;
    }

    public function get_phone()
    {
        if (!isset($_REQUEST['phone'])) {
            return $_REQUEST['phone_number'];
        }
        return trim($_REQUEST['phone']);
    }

    public function get_email()
    {
        $email = str_replace(' ', '', $_REQUEST['email']);
        $email = trim($email);
        return $email;
    }

    public function get_pid()
    {
        return (isset($_GET['pid'])) ? $_GET['pid'] : $this->settings['pid'];
    }

    public function get_pipeline()
    {
        if (!isset($_GET['group'])) {
            $group = $this->settings['group'];
            if (isset($group) && !empty($group)) {
                return $group;
            }
            return ($offer) ? $offer : $group;
        } else {
            return $_GET['group'];
        }
    }

    public function get_ref()
    {
        $schema = ($_SERVER['REQUEST_SCHEME'] == 'http') ? 'http' : 'https';
        return "{$schema}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }

    public function get_ref_lead()
    {
        return $_REQUEST['_ref'];
    }

    public function get_user_ip()
    {
        return $this->location['ip'];
    }

    public function get_user_city()
    {
        return $this->location['city'];
    }

    public function get_user_country()
    {
        return $this->location['country'];
    }

    public function send_request($data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $data['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data['content']);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        
        $array = json_decode($result, 1);
        
        if (is_array($array)) {
            return $array;
        }
        return $result;
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

    private function get_neogara_server_domain()
    {
        $domain = 'admin.neogara.com';
        if (isset($_GET['action']) && $_GET['action'] == 'test') {
            $domain = "dev.{$domain}";
        }
        return $domain;
    }
}