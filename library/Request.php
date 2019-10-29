<?php defined('ABSPATH') or die("Protected By WT!");


class WTSEC_LIBRARY_Request
{

    public $request;

    public $method;

    public function __construct()
    {
        $this->request = [];
        $this->request += $this->clean($_GET);
        $this->request += $this->clean($_POST);
        $this->request = (object)$this->request;
        $this->method = $this->clean($_SERVER['REQUEST_METHOD']);
    }

    public function clean($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);
                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars(strip_tags($data), ENT_COMPAT, 'UTF-8');
        }
        return $data;
    }

    public function __get($name)
    {
        if (!is_null($this->request) && array_key_exists($name, $this->request)) {
            return $this->request->$name;
        }
        if (isset($this->$name)) {
            return $this->$name;
        }
        return "";
    }
}