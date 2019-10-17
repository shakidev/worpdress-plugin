<?php

class WTOTEMSEC_LIBRARY_App
{

    public function __construct()
    {
        $this->auth();
    }

    public function auth(){
        if(self::getOption('authorized') === false){
            wp_redirect(wtotemsec_getUrl('login'));
            exit;
        }
    }

    public static function authorized(){
        return (boolean) self::getOption('authorized');
    }

    public static function login($token){
        self::set('authorized', true);
        self::set('authToken', $token);
    }

    public static function logout(){
        self::set('authorized', false);
        self::set('authToken', "");
    }



    public function getLocalDomains(){
        return [];
    }

    public function set($name,$value){
        return update_option(WTOTEMSEC_PLUGIN_PREFIX.$name,$value);
    }

    public function get($name){
        return get_option(WTOTEMSEC_PLUGIN_PREFIX.$name);
    }

    public static function getOption($name){
        return get_option(WTOTEMSEC_PLUGIN_PREFIX.$name);
    }

    public static function getToken(){
        return self::getOption('authToken');
    }

    public static function getName(){
        return self::getOption('username');
    }
}