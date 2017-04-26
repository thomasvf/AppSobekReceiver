<?php

/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 26/01/2017
 * Time: 22:41
 */
class UrlSolver {
    private $url;

    public function __construct($url){
        $this->setUrl($url);
    }
    private function hasValidPath() {
        if( preg_match('|^(http(s)?://)?[a-z0-9-]+\.(.[a-z0-9-]+)+(:[0-9]+)?(/.*)?$|i', $this->getUrl()) == FALSE) return FALSE;
        return TRUE;
    }

    private function isValidUrlFilterVar(){
        return filter_var($this->getUrl(), FILTER_VALIDATE_URL);
    }

    public function isValidUrl(){
        return $this->hasValidPath() && $this->isValidUrlFilterVar();
    }

    public function getRepairedUrl(){
        $url_params = parse_url($this->getUrl());
        if($url_params === FALSE) return FALSE;

        // Repairs scheme
        if(!isset($url_params['scheme'])){
            $path = $url_params["path"];
            $pos = strpos($path, ":");
            if($pos !== FALSE){
                $url_params['scheme'] = substr($path, 0, $pos);
                $url_params['path'] = substr($path, $pos);
            }
        }
        if(!isset($url_params['scheme']) || !($url_params['scheme'] == "http" || $url_params['scheme'] == "https" || $url_params['scheme'] == "ftp")){
            $url_params['scheme'] = "http";
        }

        // Repairs path
        if(isset($url_params["path"])){
            $path = $url_params["path"];
            $pos = 0;
            while($path[$pos] == '/' || $path[$pos] == '.' || $path[$pos] == ':') $pos++;
            $url_params["path"] = substr($path, $pos);
        }

        $oldUrl = $this->getUrl();
        $newUrl = $this->unparseUrl($url_params);
        $this->setUrl($newUrl);
        return $this->getUrl();
    }

    private function unparseUrl($parsed_url) {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    public function getUrl(){
        return $this->url;
    }

    public function setUrl($url){
        $this->url = $url;
    }
}