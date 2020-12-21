<?php

namespace App\Classes;

use GeoIp2\Database\Reader;

class GetLocation
{
    public function __construct()
    {
        // потом поменять местами
        $data = $this->get_by_data();
        return $data;

        $api = $this->get_by_api();
        if ($api) {
            return $api;
        }
    }

    public function get_by_data()
    {
        $datFile = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'GeoLite2-City.mmdb']);
        $reader = new Reader($datFile);

        $userIp = $this->get_user_ip();
        if ($userIp == '127.0.0.1') {
            return false;
        }
        $reader = $reader->city($userIp);

        $array = [
            'ip' => $userIp,
            'city' => $reader->city->name,
            'region' => $reader->mostSpecificSubdivision->name,
            'country' => $reader->country->isoCode,
            'postal' => $reader->postal->code,
            'location' => $reader->location->latitude.','.$reader->location->longitude,
            'timezone' => $reader->location->timeZone
        ];
        
        return $array;
    }

    public function get_by_api()
    {
        $userIp = $this->get_user_ip();
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
}