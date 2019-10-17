<?php

class WTOTEMSEC_LIBRARY_Render
{

    public static function getTable($theads,$tbodies){
        $table = '<table class="wp-list-table widefat fixed striped users">';
        $_theads = '<thead><tr>';
        if(!empty($theads)){
            foreach ($theads as $th){
                $_theads .= '<th scope="col" class="manage-column">'.$th.'</th>';
            }
        }

        $_theads .= '</tr></thead>';
        $_tbody = '<tbody>';
        if(!empty($tbodies)){
            foreach ($tbodies as $column => $tbody){
                $_tbody .= '<tr>';
                foreach ($tbody as $td){
                    $_tbody .= '<td scope="col" class="manage-column">'.$td.'</td>';
                }
                $_tbody .= '</tr>';
            }
        }

        $_tbody .= '</tbody>';
        $table .= $_theads;
        $table .= $_tbody;
        $table .= '</table>';
        return $table;
    }


    public static function description($text){
        return '<div class="wtotem-notification notice" style="padding:5px 20px 20px 20px;border-left-color: skyblue"><h3 style="margin: .4em 0">'.WTOTEMSEC_LIBRARY_Localization::lmsg('description').'</h3>'.htmlspecialchars($text).'</div>';
    }

    public static function getMainButton($service,$uid,$cmd,$cmd_text){
        $url = esc_url(admin_url('admin-post.php'));
        $form = '<form action="'.$url.'" method="post" style="display:inline-block">
        <input type="hidden" name="service" value="'.$service.'">
        <input type="hidden" name="action" value="ajax_cmd">
        <input type="hidden" name="uid" value="'.$uid.'">
        <input type="hidden" name="cmd" value="'.$cmd.'">'.$cmd_text.'</form>';
        return $form;
    }

    public static function generateMainButton($uid){
        $generated_buttons = [];
        $services = ["WAF","VC"];
        foreach ($services as $service){
            $_service = strtolower($service);
            $url = esc_url(admin_url('admin-post.php'));
            $buttons = [];
            $form = '<form action="'.$url.'" method="post" style="display:inline-block">
        <input type="hidden" name="service" value="'.$_service.'">
        <input type="hidden" name="action" value="ajax_cmd">
        <input type="hidden" name="uid" value="'.$uid.'">
        <input type="hidden" name="cmd" value="{{{cmd}}}">{{{button}}}</form>';
            $result = '';
            $installedFile = wtotemsec_checkInstalledFile($service);
            $status = wtotemsec_checkStatus($uid, $service);
            $runned = $status['active'];
            if (!$installedFile['status']) {
                $cmd = $_service . '_install';
                $buttons[] = ['button' => wtotemsec_main_button(WTOTEMSEC_LIBRARY_Localization::lmsg('install')),'cmd' => $cmd];
            } else {
                    if ($runned) {
                        wtotemsec_app()->set($_service.'_status',"start");
                        $cmd = $_service . '_stop';
                        $buttons[] = ['button' => wtotemsec_main_button(WTOTEMSEC_LIBRARY_Localization::lmsg('stop')),'cmd' => $cmd];
                    } else {
                        wtotemsec_app()->set($_service.'_status',"stop");
                        $cmd = $_service . '_start';
                        $buttons[] = ['button' => wtotemsec_main_button(WTOTEMSEC_LIBRARY_Localization::lmsg('run')),'cmd' => $cmd];
                    }
            }
            foreach ($buttons as $btn){
                $newform = $form;
                $newform = str_replace("{{{cmd}}}",$btn['cmd'],$newform);
                $newform = str_replace("{{{button}}}",$btn['button'],$newform);
                $result .= $newform;
            }
            $generated_buttons[$service] = $result;
        }
        return $generated_buttons;
    }
}