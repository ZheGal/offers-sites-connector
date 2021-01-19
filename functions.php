<?php

function get_user_ip()
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

function get_settings()
{
    $settingsPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'settings.json']);
    if (!file_exists($settingsPath)) {
        new_settings();
        return get_settings();
    }

    $settingsRaw = file_get_contents($settingsPath);

    if (empty($settingsRaw)) {
        new_settings();
        return get_settings();
    }

    $settingsJson = json_decode($settingsRaw, 1);

    if (!is_array($settingsJson)) {
        new_settings();
        return get_settings();
    }
    $settingsJson = utm_settings($settingsJson);
    $settingsJson = array_diff($settingsJson, ['']);

    return $settingsJson;
}

function new_settings()
{
    $settingsPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'settings.json']);
    $settingsExample = implode(DIRECTORY_SEPARATOR, [__DIR__, 'settings_example.php']);
    $settingsArray = require($settingsExample);
    $settingsJson = json_encode($settingsArray, JSON_PRETTY_PRINT);
    file_put_contents($settingsPath, $settingsJson);
}

function utm_settings($array)
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
        unset($get['ynd']);
    }

    foreach ($get as $key => $value) {
        $array[$key] = $value;
    }
    return $array;
}

function dd($d)
{
    echo "<pre style=\"color: #000;background-color: #e6e6e6;padding: 1rem;\">";
    var_dump($d);
    echo "</pre>";
    die;
}

function get_root_folder()
{
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $delPath = explode("/app/index.php", $scriptName);
    return trim($delPath[0],'/');
}

function removeFolder($filename, $folder)
{
    $rand = md5(rand());
    $filename = $rand.$filename;
    $folder = $rand.$folder;
    $result = str_replace($folder, '', $filename);
    return trim($result);
}

function requestUnderscoresDelete()
{
    $result = [];
    foreach ($_REQUEST as $key => $value) {
        if ($key[0] != '_') {
            $result[$key] = $value;
        }
    }
    if (isset ($result['PHPSESSID'])) {
        unset($result['PHPSESSID']);
    }
    return $result;
}

function getSubs($array)
{
    if (is_array($array) && !empty($array)) {
        $result = [];
        $array = array_filter($array, function($element) {
            return !empty($element);
        });
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                $check = explode('sub', $key);
                if (empty($check[0]) && isset($check[1])) {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }
}