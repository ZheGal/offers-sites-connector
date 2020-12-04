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
        $settings_path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        if (file_exists($settings_path)) {
            $this->settings = json_decode(file_get_contents($settings_path), 1);
        }
        if (isset($_SESSION['location'])) {
            $this->location = $_SESSION['location'];
        }
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }
        }
        unset($_SESSION['location']);
    }

    public function click_reg($view = '')
    {
        $array = json_encode([
            'pid' => $this->get_pid(),
            'pipeline' => $this->get_pipeline(),
            'ref' => $this->get_ref(),
            'ip' => $this->get_user_ip(),
            'city' => $this->get_user_city(),
            'country' => $this->get_user_country()
        ]);
        $url = 'https://admin.neogara.com/clicks'; // prod
        
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
            'ref' => $this->get_ref(),
            'click' => (isset($request['id'])) ? $request['id'] : false
        ], ['']);

        $input_str = '';
        foreach ($inputs as $key => $value) {
            $input_str .= "\n\t<input type=\"hidden\" name=\"_{$key}\" value=\"{$value}\">";
        }

        $find = preg_match_all("(<form[^<>]+>)", $view, $out);
        
        if (isset($out[0])) {
            foreach ($out[0] as $form) {
                $view = str_replace($form, "{$form}\n{$input_str}", $view);
            }
        }
        return $view;
    }

    public function lead_reg()
    {
        $array = json_encode([
            'pid' => $this->get_pid(),
            'pipeline' => $this->get_pipeline(),
            'firstname' => $_REQUEST['firstname'],
            'lastname' => $_REQUEST['lastname'],
            'phone' => $this->get_phone(),
            'email' => $this->get_email(),
            'ref' => $this->get_ref_lead(),
            'ip' => $this->get_user_ip(),
            'city' => $this->get_user_city(),
            'country' => $this->get_user_country(),
            'click' => $this->get_click_id(),
        ]);

        $url = 'https://admin.neogara.com/register/lid'; // prod
        
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

    public function get_click_id()
    {
        return (isset($_POST['_click'])) ? $_POST['_click'] : false;
    }

    public function get_phone()
    {
        return $_REQUEST['phone_number'];
    }

    public function get_email()
    {
        return $_REQUEST['email'];
    }

    public function get_pid()
    {
        return ($_GET['pid']) ? $_GET['pid'] : $this->settings['pid'];
    }

    public function get_pipeline()
    {
        if (!isset($_GET['group'])) {
            $group = $this->settings['group'];
            $offer = $this->settings['offer'];
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
}