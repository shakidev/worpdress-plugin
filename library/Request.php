<?php

class WTOTEMSEC_LIBRARY_Request
{

    public $request;

    public $method;

    public function __construct()
    {
        $this->request = [];
        $this->request += $_GET;
        $this->request += $_POST;
        $this->request = (object) $this->request;
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function __get($name)
    {
        if (!is_null($this->request) && array_key_exists($name, $this->request)) {
            return $this->request->$name;
        }
        if(isset($this->$name)){
            return $this->$name;
        }else{
            return "";
        }
    }

    public function url(){
        return $_SERVER['REQUEST_URI'];
    }

}