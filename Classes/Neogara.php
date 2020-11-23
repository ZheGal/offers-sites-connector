<?php

namespace App\Classes;

class Neogara
{
    private $settings;
    private $location;

    public function __construct($params = [])
    {
        $settings_path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        if (file_exists($settings_path)) {
            $this->settings = json_decode(file_get_contents($settings_path), 1);
        }
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function click_reg()
    {
        $array = json_encode([
            'pid' => $this->get_pid(),
            'pipeline' => $this->get_pipeline(),
            'ref' => $this->get_ref(),
            'ip' => $this->get_user_ip(),
            'city' => $this->get_user_city(),
            'country' => $this->get_user_country()
        ]);
        $url = 'https://admin.neogara.com/clicks';
        
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
        
        if (isset($request['id'])) {
            $_SESSION['click_id'] = $request['id'];
        }
    }

    public function lead_reg()
    {
        $url = 'https://admin.neogara.com/register/lid';
    }

    public function get_pid()
    {
        return $this->settings['pid'];
    }

    public function get_pipeline()
    {
        $group = $this->settings['group'];
        $offer = $this->settings['offer'];
        if (isset($group) && !empty($group)) {
            return $group;
        }
        return ($offer) ? $offer : $group;
    }

    public function get_ref()
    {
        $scheme = ($_SERVER['REQUEST_SCHEME'] == 'http') ? 'http' : 'https';
        return "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
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