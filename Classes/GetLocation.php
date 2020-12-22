<?php

namespace App\Classes;

// use GeoIp2\Database\Reader;

class GetLocation
{
    public $api;

    public function __construct()
    {
        if( !$this->check_session() ) {
            $this->import_to_session();
        }
    }

    public function get_all()
    {
        $result = $this->print_session_location();
        return $result;
    }

    public function import_to_session()
    {
        $userIp = get_user_ip();
        $api = $this->get_by_api();
        if ($api) {
            $json = json_encode($api);
            $_SESSION['locations'][$userIp] = base64_encode($json);
        }
    }

    public function check_session()
    {
        $userIp = get_user_ip();
        return isset($_SESSION['locations'][$userIp]);
    }

    public function print_session_location()
    {
        $userIp = get_user_ip();
        $base = $_SESSION['locations'][$userIp];
        $json = base64_decode($base);
        return json_decode($json, 1);
    }

    // public function get_by_data()
    // {
    //     $datFile = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'GeoLite2-Country.mmdb']);
    //     $reader = new Reader($datFile);

    //     $userIp = get_user_ip();
    //     if ($userIp == '127.0.0.1') {
    //         return false;
    //     }
    //     $reader = $reader->country($userIp);

    //     $array = [
    //         'ip' => $userIp,
    //         'country' => $reader->country->isoCode
    //     ];
        
    //     return $array;
    // }

    public function get_by_api()
    {
        $userIp = get_user_ip();
        $apiUrl = "http://ipinfo.io/{$userIp}?token=8b50524357b6bc";
        $raw = @file_get_contents($apiUrl);
        if (!empty($raw)) {
            $json = json_decode($raw, 1);
            if (is_array($json) && isset($json['country'])) {
                return $json;
            }
        }
        return false;
    }
}