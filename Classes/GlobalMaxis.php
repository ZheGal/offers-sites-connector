<?php

namespace App\Classes;

class GlobalMaxis
{
    public $params;
    public $settings;

    public function __construct()
    {
        $empty = $this->check_empty();
        if ($empty) {
            header("Location:{$_POST['_ref']}");
        }

        $this->check_requests();
        $this->get_settings();
        $this->get_params();
        $crm = $this->send_to_crm();
        
        if ($crm) {
            $this->send_to_thanks();
        } else {
            $this->back_to_main();
        }
    }

    public function send_to_thanks()
    {
        $redirect = trim($this->settings['return'],'\/ ');
        unset($_SESSION);
        header("Location:/{$redirect}");
    }

    public function back_to_main()
    {
        $back = $_REQUEST['_ref'];
        header("Location:/{$back}");
    }

    public function get_settings()
    {
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        $raw = file_get_contents($path);
        $array = json_decode($raw, 1);
        $this->settings = $array;
    }

    public function check_empty()
    {
        $error = 0;

        if (empty($_POST['firstname'])) {
            $error = 1;
        }
        if (empty($_POST['lastname'])) {
            $error = 1;
        }
        if (empty($_POST['email'])) {
            $_SESSION['error'][] = "Email is empty";
            $error = 1;
        }
        if (empty($_POST['phone_number'])) {
            $_SESSION['error'][] = "Phone number is empty";
            $error = 1;
        }

        return $error;
    }

    public function check_requests()
    {
        $req = $_REQUEST;
        unset($_REQUEST);
        foreach ($req as $key => $value) {
            if ($key[0] != '_') {
                $_REQUEST[$key] = $value;
            }
        }
    }

    public function data_global()
    {
        return [
            'url' => 'https://my.globalmaxis.com/',
            'api' => 'fs3TXHRU6j6QyE2'
        ];
    }

    public function get_params()
    {
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'params.php']);
        if (isset($path)) {
            $this->params = require($path);
        }
    }

    public function get_desk_id()
    {
        $language = $this->settings['language'];
        $param = ($this->params['global']['desk'][$language]) ? $this->params['global']['desk'][$language] : false; 
        return $param;
    }

    public function get_additional()
    {
        $sitename = $this->settings['sitename'];
        $param = ($this->params['global']['campaign'][$sitename]) ? $this->params['global']['campaign'][$sitename] : false; 
        return $param;
    }

    public function get_responsible()
    {
        $language = $this->settings['language'];
        $param = ($this->params['global']['responsible'][$language]) ? $this->params['global']['responsible'][$language] : false; 
        return $param;
    }

    public function get_country()
    {
        $location = $_SESSION['location'];
        if (!empty($location['country'])) {
            return $location['country'];
        } else {
            $_SESSION['error'][] = 'Cannot detect your country!';
            $back = ($_REQUEST['_ref']) ? $_REQUEST['_ref'] : '/';
            header("Location:/");
            die;
        }
    }

    public function send_to_crm()
    {
        $api = $this->data_global();

        $params = [
            'key' => $api['api'],
            'rand_param' => rand(1000000, 99999999),
            'first_name' => htmlentities($_REQUEST["firstname"],ENT_COMPAT,'UTF-8'),
            'second_name' => htmlentities($_REQUEST["lastname"],ENT_COMPAT,'UTF-8'),
            'phone' => htmlentities($_REQUEST["phone_number"],ENT_COMPAT,'UTF-8'),
            'email' => htmlentities($_REQUEST["email"],ENT_COMPAT,'UTF-8'),
            'description' => 'description',
            'country' => htmlentities($this->get_country(),ENT_COMPAT,'UTF-8'),
            'desk_id' => $this->get_desk_id(),
            'responsible' => $this->get_responsible(),
            'date_of_birth' => '',
            'additionalField20' => htmlentities($_REQUEST["pid"],ENT_COMPAT,'UTF-8'),
            'additionalField21' => $this->get_additional(),
            'additionalField22' => '',
            'additionalField23' => '',
            'additionalField24' => '',
            'additionalField25' => '',
            'additionalField26' => htmlentities($_REQUEST["apsubid1"],ENT_COMPAT,'UTF-8'),
            'additionalField27' => htmlentities($_REQUEST["apsubid2"],ENT_COMPAT,'UTF-8'),
            'additionalField28' => htmlentities($_REQUEST["apsubid3"],ENT_COMPAT,'UTF-8'),
            'additionalField29' => htmlentities($_REQUEST["apsubid4"],ENT_COMPAT,'UTF-8'),
            'additionalField30' => htmlentities($_REQUEST["utm_source"],ENT_COMPAT,'UTF-8'),
            'additionalField31' => htmlentities($_REQUEST["utm_medium"],ENT_COMPAT,'UTF-8'),
            'additionalField32' => htmlentities($_REQUEST["utm_campaign"],ENT_COMPAT,'UTF-8'),
            'additionalField33' => htmlentities($_REQUEST["utm_content"],ENT_COMPAT,'UTF-8'),
            'additionalField34' => htmlentities($_REQUEST["sub1"],ENT_COMPAT,'UTF-8'),
            'additionalField35' => htmlentities($_REQUEST["sub2"],ENT_COMPAT,'UTF-8'),
            'additionalField36' => htmlentities($_REQUEST["sub3"],ENT_COMPAT,'UTF-8'),
            'additionalField37' => htmlentities($_REQUEST["sub4"],ENT_COMPAT,'UTF-8'),
            'additionalField38' => htmlentities($_REQUEST["txt"],ENT_COMPAT,'UTF-8'),
        ];

        $params = array_diff($params, array(''));
        $params['key'] = md5($params['key'] . $params['rand_param']);
        $params = array_diff($params,array(''));
        $url = $api['url'].'api/v_2/crm/CreateLead?'.http_build_query($params);
        $result = json_decode(file_get_contents($url),1);

        if ($result['result'] == 'success') {
            return true;
        }
        return false;
    }
}