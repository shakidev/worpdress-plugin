<?php

function wtotemsec_cmd($wp_filesystem,$cmd, $service, $_service, $uid, $status,$domain = ''){

    switch ($cmd) {
        case $_service . "_install":
            try {
                $file = wtotemsec_generateFile($uid, $service);
                if($service === "WAF"){
                $plugin_path = str_replace(ABSPATH, $wp_filesystem->abspath(), WTOTEMSEC_INSTALLATION_DIR);
                $path = $plugin_path;
                $target_dir = $wp_filesystem->find_folder($path);
                if(!$wp_filesystem->is_dir($path)) {
                    $wp_filesystem->mkdir($target_dir);
                }
                $target_file = trailingslashit($target_dir).$file['name'];
                if (!$wp_filesystem->put_contents($target_file, $file['file'], FS_CHMOD_FILE)){
                        WTOTEMSEC_LIBRARY_Session::setNotification("warning",WTOTEMSEC_LIBRARY_Localization::lmsg('could_not_install_file', ['service' => $service,'directory' => $target_dir]));
                 }
                }else{
                    $target_dir = $wp_filesystem->find_folder($wp_filesystem->abspath());
                    $target_file = trailingslashit($target_dir).$file['name'];
                    if(!$wp_filesystem->put_contents($target_file, $file['file'])) {
                        WTOTEMSEC_LIBRARY_Session::setNotification("warning",WTOTEMSEC_LIBRARY_Localization::lmsg('could_not_install_file', ['service' => $service,'directory' => $target_dir]));
                    }
                }
                wtotemsec_serviceConnect($uid, $service,$domain.'/'.$file['name']);
            } catch (Exception $e) {
                print_r($e->getMessage());
                die();
            }
            break;
        case $_service . "_start":
            WTOTEMSEC_LIBRARY_Webtotem::changeStatus($status['config_id'],$uid);
            wtotemsec_app()->set($_service.'_status',"start");
            break;
        case $_service . "_stop":
            WTOTEMSEC_LIBRARY_Webtotem::changeStatus($status['config_id'],$uid);
            wtotemsec_app()->set($_service.'_status',"stop");
            break;
        case $_service . "_connect":
            $file = wtotemsec_getInstalledFile($service);
            if (!is_null($file)) {
                wtotemsec_serviceConnect($uid, $service, $domain . '/' . $file);
            }else{
                WTOTEMSEC_LIBRARY_Session::setNotification("warning",WTOTEMSEC_LIBRARY_Localization::lmsg('file_not_found', ['service' => $service]));
            }
            break;
        case $_service . "_uninstall":
            try {
                $file = wtotemsec_getInstalledFile($service);
                if($service === "WAF"){
                    $plugin_path = str_replace(ABSPATH, $wp_filesystem->abspath(), WTOTEMSEC_INSTALLATION_DIR);
                    $mainDir = $plugin_path;
                }else{
                    $mainDir = $wp_filesystem->abspath();
                }
                $target_dir = $wp_filesystem->find_folder($mainDir);
                $target_file = trailingslashit($mainDir).$file;
                if (!$wp_filesystem->delete($target_file)) {
                    WTOTEMSEC_LIBRARY_Session::setNotification("warning",WTOTEMSEC_LIBRARY_Localization::lmsg('could_not_uninstall_file', ['service' => $service,'directory' => $target_dir]));
                }
            } catch (Exception $e) {
                print_r($e->getMessage());
                die();
            }
            break;
    }

//    return "success";
}

function wtotemsec_checkStatus($id, $service)
{
    $result = WTOTEMSEC_LIBRARY_Webtotem::checkStatus($id,$service);
    $data = $result['data'][strtolower($service) . 'ServiceChecks'][0];
    $is_active = (boolean)$data['config']['isActive'];
    $config_id = $data['config']['id'];
    $status = $data['status'];
    if ($status == -300 && $is_active) {
        WTOTEMSEC_LIBRARY_App::set($service . '_connected_' . $id, false);
    }
    return ['active' => $is_active, 'status' => $status, 'config_id' => $config_id];
}



function wtotemsec_generateFile($id, $service)
{
    $result = WTOTEMSEC_LIBRARY_Webtotem::generateFile($id,$service);
    $name = $result['data']['generateAgent']['agentName'] . '.' . strtolower($service) . '.php';
    $file = WTOTEMSEC_LIBRARY_WebTotem::requestURL(WTOTEMSEC_FILE_URL . '/' . $name);
    if(empty($file)){
        $file = WTOTEMSEC_LIBRARY_WebTotem::requestURL(WTOTEMSEC_FILE_URL . '/' . $name);
    }
    return ['file' => $file, 'name' => $name];
}

function wtotemsec_serviceConnect($id, $service,$domain = '')
{
    $result = WTOTEMSEC_LIBRARY_Webtotem::serviceConnect($id,$service);
    $status = (isset($result['errors']) || isset($result['data']['checkAgent']['lockFor']) || (isset($result['data']['checkAgent']['installed']) && $result['data']['checkAgent']['installed'] === false)) ? false : true;
    if (!$status) {
        WTOTEMSEC_LIBRARY_Session::setNotification("warning",WTOTEMSEC_LIBRARY_Localization::lmsg('unable_to_connect_to_service', ['service' => $service,'site' => $domain]));
    }
    return $status;
}


function wtotemsec_servicePing($service,$domain = '')
{
    if($domain == NULL) return false;
    if(mb_stripos($domain,"http") === false){
        $domain = "http://".$domain;
    }
    $args = [
        'timeout' => '10',
        'sslverify' => false,
        'redirection' => 3,
    ];
    $response = wp_remote_get($domain,$args);
    $httpcode = wp_remote_retrieve_response_code($response);
    if($httpcode>=200 && $httpcode<400){
        return true;
    } else {
        WTOTEMSEC_LIBRARY_Session::setNotification("warning",WTOTEMSEC_LIBRARY_Localization::lmsg('unable_to_connect_to_service', ['service' => $service,'site' => $domain]));
        return false;
    }
}

function wtotemsec_getInstalledFile($service)
{
    if($service === "VC"){
        $files = scandir(ABSPATH);
        foreach ($files as $file) {
            if ($file[0] !== "." && stripos($file, "." . strtolower($service) . ".php") !== false) {
                return $file;
            }
        }
    }else{
        $files = scandir(WTOTEMSEC_INSTALLATION_DIR);
        foreach ($files as $file) {
            if (stripos($file, "." . strtolower($service) . ".php") !== false) {
                return $file;
            }
        }
    }

    return null;
}


function wtotemsec_button($label = '',$class = ''){
    return '<button class="ww-button '.$class.'">'.$label.'</button>';
}

function wtotemsec_main_button($label = '',$class = ''){
    return '<button class="ww-button '.$class.'">'.$label.'</button>';
}

function wtotemsec_generateButtons($uid,$_service, $service, $status,$domain = '')
{
    $url = esc_url(admin_url('admin-post.php'));
    $buttons = [];

    $form = '<form action="'.$url.'" method="post" style="display:inline-block;">
        <input type="hidden" name="service" value="'.$_service.'">
        <input type="hidden" name="action" value="ajax_cmd">
        <input type="hidden" name="uid" value="'.$uid.'">
        <input type="hidden" name="cmd" value="{{{cmd}}}">{{{button}}}</form>';
    $result = [];
    $installedFile = wtotemsec_checkInstalledFile($service);
    $runned = $status['active'];

    if($service === "WAF"){
        $first_class = "";
    }else{
        $first_class = "ww-button--block ";
    }
    $disable_uninstall = false;
    if (!$installedFile['status']) {
        $cmd = $_service . '_install';
        $buttons[] = ['place' => 'first','button' => wtotemsec_button(WTOTEMSEC_LIBRARY_Localization::lmsg('install'),$first_class."ww-button--success"),'cmd' => $cmd];
    } else {
        if($service === "WAF"){
            $url = $domain.'/?ping='.$installedFile['file'];
        }else{
            $url = $domain.'/'.$installedFile['file'];
        }
        $connected = wtotemsec_servicePing($service,$url);
        if (!$connected) {
            $disable_uninstall = true;
            $cmd = $_service . '_connect';
            $buttons[] = ['place' => 'first','button' => wtotemsec_button(WTOTEMSEC_LIBRARY_Localization::lmsg('connect'),$first_class."ww-button--success"),'cmd' => $cmd];
        } else {
            if ($runned) {
                $cmd = $_service . '_stop';
                $buttons[] = ['place' => 'second','button' => wtotemsec_button(WTOTEMSEC_LIBRARY_Localization::lmsg('stop'),"ww-button--primary"),'cmd' => $cmd];
            } else {
                $cmd = $_service . '_start';
                $buttons[] = ['place' => 'second','button' => wtotemsec_button(WTOTEMSEC_LIBRARY_Localization::lmsg('run'),"ww-button--primary"),'cmd' => $cmd];
            }
        }
        if(!$disable_uninstall && !$runned){
            $cmd = $_service . '_uninstall';
            $buttons[] = ['place' => 'first','button' => wtotemsec_button(WTOTEMSEC_LIBRARY_Localization::lmsg('uninstall'),$first_class."ww-button--attention"),'cmd' => $cmd];
        }
    }
    foreach ($buttons as $btn){
        $newform = $form;
        $newform = str_replace("{{{cmd}}}",$btn['cmd'],$newform);
        $newform = str_replace("{{{button}}}",$btn['button'],$newform);
        $result[$btn['place']] = $newform;
    }
    return $result;
}

function wtotemsec_checkInstalledFile($service)
{
    $file = wtotemsec_getInstalledFile($service);
    return ['status' => is_null($file) ? false : true,'file' => $file];
}