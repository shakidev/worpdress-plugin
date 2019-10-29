<?php defined('ABSPATH') or die("Protected By WT!");

function wtsec_cmd($wp_filesystem,$cmd, $service, $_service, $uid, $status,$domain = ''){

    switch ($cmd) {
        case $_service . "_install":
            try {
                $file = wtsec_generateFile($uid, $service);
                if($service === "WAF"){
                $plugin_path = str_replace(ABSPATH, $wp_filesystem->abspath(), WTSEC_INSTALLATION_DIR);
                $path = $plugin_path;
                $target_dir = $wp_filesystem->find_folder($path);
                if(!$wp_filesystem->is_dir($path)) {
                    $wp_filesystem->mkdir($target_dir);
                }
                $target_file = trailingslashit($target_dir).$file['name'];
                if (!$wp_filesystem->put_contents($target_file, $file['file'], FS_CHMOD_FILE)){
                        WTSEC_LIBRARY_Session::setNotification("warning",WTSEC_LIBRARY_Localization::lmsg('could_not_install_file', ['service' => $service,'directory' => $target_dir]));
                 }else{
                    wtsec_app()->set($_service . '_installed_file',$file['name']);
                }
                }else{
                    $target_dir = $wp_filesystem->find_folder($wp_filesystem->abspath());
                    $target_file = trailingslashit($target_dir).$file['name'];
                    if(!$wp_filesystem->put_contents($target_file, $file['file'])) {
                        WTSEC_LIBRARY_Session::setNotification("warning",WTSEC_LIBRARY_Localization::lmsg('could_not_install_file', ['service' => $service,'directory' => $target_dir]));
                    }else{
                        wtsec_app()->set($_service . '_installed_file',$file['name']);
                    }
                }
                wtsec_serviceConnect($uid, $service,$domain.'/'.$file['name']);
            } catch (Exception $e) {
                print_r($e->getMessage());
                die();
            }
            break;
        case $_service . "_start":
            WTSEC_LIBRARY_WT::changeStatus($status['config_id'],$uid);
            wtsec_app()->set($_service.'_status',"start");
            break;
        case $_service . "_stop":
            WTSEC_LIBRARY_WT::changeStatus($status['config_id'],$uid);
            wtsec_app()->set($_service.'_status',"stop");
            break;
        case $_service . "_connect":
            $file = wtsec_getInstalledFile($_service);
            if ($file) {
                wtsec_serviceConnect($uid, $service, $domain . '/' . $file);
            }else{
                WTSEC_LIBRARY_Session::setNotification("warning",WTSEC_LIBRARY_Localization::lmsg('file_not_found', ['service' => $service]));
            }
            break;
        case $_service . "_uninstall":
            try {
                $file = wtsec_getInstalledFile($_service);
                if($service === "WAF"){
                    $plugin_path = str_replace(ABSPATH, $wp_filesystem->abspath(), WTSEC_INSTALLATION_DIR);
                    $mainDir = $plugin_path;
                }else{
                    $mainDir = $wp_filesystem->abspath();
                }
                $target_dir = $wp_filesystem->find_folder($mainDir);
                $target_file = trailingslashit($mainDir).$file;
                if (!$wp_filesystem->delete($target_file)) {
                    WTSEC_LIBRARY_Session::setNotification("warning",WTSEC_LIBRARY_Localization::lmsg('could_not_uninstall_file', ['service' => $service,'directory' => $target_dir]));
                }
                wtsec_app()->set($_service . '_status', "uninstalled");
                WTSEC_LIBRARY_App::deleteOption($_service . '_installed_file');
            } catch (Exception $e) {
                print_r($e->getMessage());
                die();
            }
            break;
    }
}

function wtsec_checkStatus($id, $service)
{
    $result = WTSEC_LIBRARY_WT::checkStatus($id,$service);
    $data = $result['data'][strtolower($service) . 'ServiceChecks'][0];
    $is_active = (boolean)$data['config']['isActive'];
    $config_id = $data['config']['id'];
    $status = $data['status'];
    if ($status == -300 && $is_active) {
        wtsec_app()->set($service . '_connected_' . $id, false);
    }
    return ['active' => $is_active, 'status' => $status, 'config_id' => $config_id];
}



function wtsec_generateFile($id, $service)
{
    $result = WTSEC_LIBRARY_WT::generateFile($id,$service);
    $name = $result['data']['generateAgent']['agentName'] . '.' . strtolower($service) . '.php';
    $file = WTSEC_LIBRARY_WT::requestURL(WTSEC_FILE_URL . '/' . $name);
    if(empty($file)){
        $file = WTSEC_LIBRARY_WT::requestURL(WTSEC_FILE_URL . '/' . $name);
    }
    return ['file' => $file, 'name' => $name];
}

function wtsec_serviceConnect($id, $service,$domain = '')
{
    $result = WTSEC_LIBRARY_WT::serviceConnect($id,$service);
    $status = (isset($result['errors']) || isset($result['data']['checkAgent']['lockFor']) || (isset($result['data']['checkAgent']['installed']) && $result['data']['checkAgent']['installed'] === false)) ? false : true;
    if (!$status) {
        WTSEC_LIBRARY_Session::setNotification("warning",WTSEC_LIBRARY_Localization::lmsg('unable_to_connect_to_service', ['service' => $service,'site' => $domain]));
    }
    return $status;
}


function wtsec_servicePing($service,$domain = '')
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
        WTSEC_LIBRARY_Session::setNotification("warning",WTSEC_LIBRARY_Localization::lmsg('unable_to_connect_to_service', ['service' => $service,'site' => $domain]));
        return false;
    }
}

function wtsec_button($label = '',$class = ''){
    return '<button class="ww-button '.$class.'">'.$label.'</button>';
}

function wtsec_main_button($label = '',$class = ''){
    return '<button class="ww-button '.$class.'">'.$label.'</button>';
}

function wtsec_generateButtons($uid,$_service, $service, $status,$domain = '')
{
    $url = esc_url(admin_url('admin-post.php'));
    $buttons = [];

    $form = '<form action="'.$url.'" method="post" style="display:inline-block;">
        <input type="hidden" name="service" value="'.$_service.'">
        <input type="hidden" name="action" value="ajax_cmd">
        <input type="hidden" name="uid" value="'.$uid.'">
        <input type="hidden" name="cmd" value="{{{cmd}}}">{{{button}}}</form>';
    $result = [];
    $installedFile = wtsec_checkInstalledFile($_service);
    $runned = $status['active'];

    if($service === "WAF"){
        $first_class = "";
    }else{
        $first_class = "ww-button--block ";
    }
    $disable_uninstall = false;
    if (!$installedFile['status']) {
        $cmd = $_service . '_install';
        $buttons[] = ['place' => 'first','button' => wtsec_button(WTSEC_LIBRARY_Localization::lmsg('install'),$first_class."ww-button--success"),'cmd' => $cmd];
    } else {
        if($service === "WAF"){
            $url = $domain.'/?ping='.$installedFile['file'];
        }else{
            $url = $domain.'/'.$installedFile['file'];
        }
        $connected = wtsec_servicePing($service,$url);
        if (!$connected) {
            $disable_uninstall = true;
            $cmd = $_service . '_connect';
            $buttons[] = ['place' => 'first','button' => wtsec_button(WTSEC_LIBRARY_Localization::lmsg('connect'),$first_class."ww-button--success"),'cmd' => $cmd];
        } else {
            if ($runned) {
                $cmd = $_service . '_stop';
                $buttons[] = ['place' => 'second','button' => wtsec_button(WTSEC_LIBRARY_Localization::lmsg('stop'),"ww-button--primary"),'cmd' => $cmd];
            } else {
                $cmd = $_service . '_start';
                $buttons[] = ['place' => 'second','button' => wtsec_button(WTSEC_LIBRARY_Localization::lmsg('run'),"ww-button--primary"),'cmd' => $cmd];
            }
        }
        if(!$disable_uninstall && !$runned){
            $cmd = $_service . '_uninstall';
            $buttons[] = ['place' => 'first','button' => wtsec_button(WTSEC_LIBRARY_Localization::lmsg('uninstall'),$first_class."ww-button--attention"),'cmd' => $cmd];
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

function wtsec_checkInstalledFile($service)
{
    $file = wtsec_getInstalledFile($service);
    return ['status' => (bool) $file,'file' => $file];
}

function wtsec_getInstalledFile($service){
    return wtsec_app()->get($service."_installed_file");
}