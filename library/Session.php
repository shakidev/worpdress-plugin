<?php

class WTOTEMSEC_LIBRARY_Session
{
    public static function notifications()
    {
        $notification_types = [
            "error","info","warning","success"
        ];
        foreach ($notification_types as $type) {
            if ($notifications = get_option(WTOTEMSEC_PLUGIN_PREFIX.$type)) {
                if(is_array($notifications)) {
                    foreach ($notifications as $notification) {
                        echo '<div class="ww-main ww-main--grid" id="ww-alert" title="" data-tlite="">
          <div class="ww-alert ww-alert--'.$type.'" title="" data-tlite="">
            <div class="ww-alert__info">'.strtoupper($type).':</div>
            <div class="ww-alert__text" title="" data-tlite="">'.$notification.'</div><button class="ww-alert__close" type="button" onclick="removeAlert()">
              <div class="ww-icon ww-icon--close"></div>
            </button>
          </div>
        </div>';
                    }
                }
                self::deleteNotification($type);
            }
        }
    }

    protected static function deleteNotification($name){
        return delete_option(WTOTEMSEC_PLUGIN_PREFIX.$name);
    }

    public static function setNotification($type,$value){
        if($notifications = get_option(WTOTEMSEC_PLUGIN_PREFIX.$type)){
            $notifications[] = $value;
            update_option(WTOTEMSEC_PLUGIN_PREFIX.$type,$notifications);
        }else{
            update_option(WTOTEMSEC_PLUGIN_PREFIX.$type,[$value]);
        }
    }
}