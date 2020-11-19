<?php

namespace App\Classes;

use App\Classes\Parameters;

class Templates
{
    private $app;

    public function __construct(General $site)
    {
        $this->app = $site;
        $this->params = new Parameters;
    }

    public function get($param)
    {
        $content = '';
        $method = "get_{$param}_params";
        $params = [];
        if (method_exists($this->params, $method)) {
            $params = $this->params->$method();
        }
        $template = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Templates', "{$param}.php"]);
        if (file_exists($template)) {
           ob_start();
           extract($params);
           require($template);
           $content = ob_get_contents();
           ob_end_clean();
        }
        return $this->ready($content);
    }

    private function ready($content)
    {
        $replace = ['SERVER_HOST' => $_SERVER['HTTP_HOST']];
        foreach ($replace as $key => $value) {
            $content = str_replace("{{{$key}}}", "{$value}", $content);
        }
        return $content;
    }
}