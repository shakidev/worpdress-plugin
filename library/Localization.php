<?php

class WTOTEMSEC_LIBRARY_Localization
{

    public static function lmsg($lmsg,$values = []){
        $default = 'en_US';
        $lang = get_locale();
        $path = self::getPath($lang);
        $messages = [];
        if(is_file($path)){
            include $path;
        }else{
            include self::getPath($default);
        }
        $keys = explode(".",$lmsg);
        foreach ($keys as $key) {
            if(!isset($messages[$key])){
                break;
            }
            $messages = &$messages[$key];
        }
        if(is_string($messages)){
            foreach ($values as $_key => $value){
                $messages = str_replace("%%".$_key."%%",$value,$messages);
            }
            return $messages;
        }
        return "[".$lmsg."]";
    }

    public static function getPath($lang){
        return WTOTEMSEC_PLUGIN_PATH.'resources/locales/'.$lang.'.php';
    }
}