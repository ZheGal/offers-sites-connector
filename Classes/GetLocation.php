<?php

namespace App\Classes;

use GeoIp2\Database\Reader;

class GetLocation
{
    public $api;

    public function __construct()
    {
        $api = $this->get_by_api();
        if ($api) {
            $this->api = $api;
        }
        // $data = $this->get_by_data();
        // $this->api = $data;
        // return;
    }

    public function get_by_data()
    {
        $datFile = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'GeoLite2-Country.mmdb']);
        $reader = new Reader($datFile);

        $userIp = get_user_ip();
        if ($userIp == '127.0.0.1') {
            return false;
        }
        $reader = $reader->country($userIp);

        $array = [
            'ip' => $userIp,
            'country' => $reader->country->isoCode
        ];
        
        return $array;
    }

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