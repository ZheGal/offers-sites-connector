<?php

namespace App\Classes;

class Cloakit
{
    public $settings;
    public $id;

    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->id = $settings['cloakit'];
    }

    public function connect()
    {
        $mainserver = 'https://panel.cloakit.space/';
        
        if (isset($_SERVER['HTTP_REFERER'])) {if (stristr($_SERVER['HTTP_REFERER'], 'yabs.yandex')) {
            $_SERVER['HTTP_REFERER'] = 'yabs.yandex';
        }}
        
        $data = array(
            '_server' => json_encode($_SERVER),
            'user' => 'dbb8f9158c69d5301355f9689dbae151',
            'company' => $this->id
        );
        $ch = curl_init();
        $optArray = array(
            CURLOPT_URL => $mainserver.'api_v2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data
        );
        
        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        curl_close($ch);
        $responses = json_decode($result, true);

        if ($_SERVER['QUERY_STRING']!='') {
            $realpage = explode('?',$responses['page']);
            $realpage = $realpage[0];
            $responses['page'] = $realpage;
          
            $querys = explode('&',$_SERVER['QUERY_STRING']);
          
            foreach ($querys as $query) {
              $query = explode('=',$query);
              $_GET[$query[0]]=$query[1];
            }
          }
          
          if ($responses['mode']=='load') {
            return $responses['page'];
          }
          else if ($responses['mode']=='redirect') {
            if ($responses['type']=='blackpage') {
                return $responses['page'];
            }
            else {
                return $responses['page'];
            }
          }
        return false;
    }
}