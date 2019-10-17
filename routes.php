<?php

add_action('admin_menu', 'wtotemsec_add_menu_link');
add_action('wp_ajax_change_status', 'wtotemsec_ajax_changeStatus');
add_action('admin_post_change_status', 'wtotemsec_changeStatus');
add_action('wp_ajax_cmd', 'wtotemsec_cmd_ajax');
add_action('admin_post_login_form', 'wtotemsec_login_form');
add_action('admin_post_ajax_cmd', 'wtotemsec_cmd_ajax');

function wtotemsec_add_menu_link()
{
    add_menu_page(
        WTOTEMSEC_PAGE_TITLE,
        WTOTEMSEC_MENU_TITLE,
        'manage_options',
        wtotemsec_getRoute('dashboard'),
        'wtotemsec_index_page',
        '',
        '1'
    );
    $parent = wtotemsec_getRoute('dashboard');
    if (WTOTEMSEC_LIBRARY_App::authorized()) {
        $capability = 'manage_options';
        foreach (wtotemsec_pages() as $page => $arguments) {
            add_submenu_page($parent, $arguments['page_title'], $arguments['menu_title'], $capability, wtotemsec_getRoute($page), $arguments['function']);
        }
        add_submenu_page(null, WTOTEMSEC_LIBRARY_Localization::lmsg('sign_in'), WTOTEMSEC_LIBRARY_Localization::lmsg('sign_in'), 'manage_options', wtotemsec_getRoute('login'), 'wtotemsec_login');
        add_submenu_page($parent, WTOTEMSEC_LIBRARY_Localization::lmsg('logout'), WTOTEMSEC_LIBRARY_Localization::lmsg('logout'), 'manage_options', wtotemsec_getRoute('logout'), 'wtotemsec_logout');
    } else {
        $capability = 'manage_options';
        foreach (wtotemsec_pages() as $page => $arguments) {
            add_submenu_page(null, $arguments['page_title'], $arguments['menu_title'], $capability, wtotemsec_getRoute($page), function () {
                wp_redirect(wtotemsec_getUrl('login'));
            });
        }
        add_submenu_page($parent, WTOTEMSEC_LIBRARY_Localization::lmsg('sign_in'), WTOTEMSEC_LIBRARY_Localization::lmsg('sign_in'), 'manage_options', wtotemsec_getRoute('login'), 'wtotemsec_login');
        add_submenu_page(null, WTOTEMSEC_LIBRARY_Localization::lmsg('logout'), WTOTEMSEC_LIBRARY_Localization::lmsg('logout'), 'manage_options', wtotemsec_getRoute('logout'), 'wtotemsec_logout');
    }

}

function wtotemsec_index_page()
{
    $host = WTOTEMSEC_LIBRARY_Webtotem::getOwnSite();
    $services = [];
    if (!empty($host)) {
        $checks = WTOTEMSEC_LIBRARY_Webtotem::getAllChecks($host['id']);
        foreach ($checks['data'] as $service => $site) {
            if (empty($site)) {
                continue;
            }
            $site = $site[0];
            switch ($service) {
                case "userHost":
                    $services["sites"] = [
                        "site_address" => $site['hostname'],
                        "site_name" => $site['title'],
                        "created" => date("Y-m-d H:i:s", strtotime($site['createdAt'])),
                        "services" => WTOTEMSEC_LIBRARY_Webtotem::statusBar($site['services']),
                    ];
                    break;
                case "waServiceChecks":
                    $services["wa"] = [
                        "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "title" => $site['config']['userhost']['title'],
                        "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "wa"),
                        "response_time" => $site['responseTime']['avg'] . ' ' . WTOTEMSEC_LIBRARY_Localization::lmsg('ms'),
                        "availability" => ceil($site['average']['uptimePercent'] * 100) . '%',
                        "availability_percent" => ceil($site['average']['uptimePercent'] * 100),
                        "downtime" => ceil($site['average']['totalDown'] / 1000) . ' ' . WTOTEMSEC_LIBRARY_Localization::lmsg('sec'),
                        "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.availability')
                    ];
                    break;
                case "sslServiceChecks":
                    $information = '';
                    if ($site['status'] == 1) {
                        $information = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.invalid');
                    } elseif ($site['status'] == 0) {
                        $information = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                    } elseif ($site['status'] == 2) {
                        $information = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.expired');
                    } elseif ($site['status'] == 3) {
                        $information = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.expires');
                    } else {
                        $information = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.missing');
                    }
                    $services["ssl"] = [
                        "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "site_address" => $host['hostname'],
                        "information" => $information,
                        "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "ssl"),
                        "days_left" => $site['daysLeft'],
                        "issue_date" => date("Y-m-d H:i:s", $site['issued'] / 1000),
                        "expiry_date" => date("Y-m-d H:i:s", $site['expires'] / 1000),
                        "tls" => $site['tls'],
                        "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.ssl')
                    ];
                    break;
                case "decServiceChecks":
                    $information = '';
                    if ($site['status'] == 1) {
                        $information = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.expires');
                    } elseif ($site['status'] == 0) {
                        $information = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                    } else {
                        $information = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                    }
                    $services["dec"] = [
                        "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "site_address" => $host['hostname'],
                        "registrar" => $site['registrar'],
                        "owner" => $site['owner'],
                        "information" => $information,
                        "email" => $site['email'],
                        "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "dec"),
                        "days_left" => $site['daysLeft'],
                        "created" => date("Y-m-d", $site['created'] / 1000),
                        "expiry_date" => date("Y-m-d", $site['expires'] / 1000),
                        "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.domain')
                    ];
                    break;
                case "avServiceChecks":
                    $services["av"] = [
                        "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "site_address" => $host['hostname'],
                        "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "av"),
                        "time_of_the_last_test" => date("Y-m-d H:i:s", $site['lastTestTime'] / 1000),
                        "blacklists_entries" => $site['count'],
                        "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.reputation')
                    ];
                    break;
                case "cmsServiceChecks":
                    $services["cms"] = [
                        "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "site_address" => $host['hostname'],
                        "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "cms"),
                        "time_of_the_last_test" => date("Y-m-d H:i:s", $site['lastTestTime'] / 1000),
                        "detected_keywords" => $site['count'],
                        "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.malicious')
                    ];
                    break;
                case "dcServiceChecks":
                    $services["dc"] = [
                        "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "site_address" => $host['hostname'],
                        "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "dc"),
                        "time_of_the_last_test" => date("Y-m-d H:i:s", $site['lastTestTime'] / 1000),
                        "number" => $site['count'],
                        "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.deface')
                    ];
                    break;
                case "psServiceChecks":
                    $services["ps"] = [
                        "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "site_address" => $host['hostname'],
                        "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "ps"),
                        "ip" => $site['ip'],
                        "time_of_the_last_test" => date("Y-m-d H:i:s", $site['lastTestTime'] / 1000),
                        "number" => $site['count'],
                        "tcp" => !empty($site['openTCPs']) ? implode(",", $site['openTCPs']) : '',
                        "udp" => !empty($site['openUDPs']) ? implode(",", $site['openUDPs']) : '',
                        "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.port')
                    ];
                    break;
                case "wafServiceChecks":
                    $service = "WAF";
                    $_service = strtolower($service);
                    $modals = [];
                    $modal = false;
                    $domain = $host['hostname'];
                    if (!empty($site['list'])) {
                        $modals[$host['id']]['header'] = WTOTEMSEC_LIBRARY_Localization::lmsg('signatures');
                        $modals[$host['id']]['list'] = $site['list'];
                        $modal = true;
                    }
                    $uid = $host['id'];
                    $status = wtotemsec_checkStatus($uid, $service);
                    $services["waf"] = [
                        "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $uid),
                        "site_address" => $domain,
                        "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "waf"),
                        "time_of_the_last_check" => empty($site['lastTestTime']) ? '' : date("Y-m-d H:i:s", $site['lastTestTime'] / 1000),
                        "signatures" => $site['count'],
                        "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.firewall'),
                        "actions" => $buttons = wtotemsec_generateButtons($uid, $_service, $service, $status, WTOTEMSEC_SITE_URL),
                        "chart" => json_encode((array)generateChart($site['chart']),true)
                    ];
                    break;
                case "vcServiceChecks":
                    $service = "VC";
                    $_service = strtolower($service);
                    $signatures = [];
                    $changes = [];
                    $errors = [];
                    $sign_count = 0;
                    $changes_count = 0;
                    $domain = $host['hostname'];
                    if (!empty($site['list'])) {
                        foreach ($site['list'] as $list) {
                            if ($list['event'] > -1 && empty($list['matches'])) {
                                $changes[$host['id']][] = $list;
                                $changes_count++;
                            } elseif ($list['event'] > -1 && !empty($list['matches'])) {
                                $signatures[$host['id']][] = $list;
                                $sign_count++;
                            } else {
                                $errors[$host['id']][] = $list;
                            }
                        }
                    }
                    $uid = $host['id'];
                    $status = wtotemsec_checkStatus($uid, $service);
                    $services["vc"] = [
                        "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $uid),
                        "site_address" => $domain,
                        "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "vc"),
                        "signatures" => $sign_count,
                        "changes" => $changes_count,
                        "list" => $site["list"],
                        "actions" => $buttons = wtotemsec_generateButtons($uid, $_service, $service, $status, WTOTEMSEC_SITE_URL),
                        "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.antivirus')
                    ];
                    break;
            }
        }
    }
    wtotemsec_layout("dashboard", $services);
}

function generateChart($charts)
{
    $d = date("d");
    $m = date("m");
    $c = [];
    $first_day = $d - 6;
    if ($first_day < 1) {
        $lastmonth = (int)date('d', strtotime('last day of previous month'));
        $lastmonth_day = $lastmonth;
        $lastmonth_month = (int)date('m', strtotime('last day of previous month'));
        $lastmonth = $lastmonth - ($first_day * -1);
        for ($i = $lastmonth; $i <= $lastmonth_day; $i++) {
            $c[$i] = [
                'day' => "{$i}/$lastmonth_month",
                'count' => 0,
            ];
        }
        $first_day = 1;
    }
    for ($i = $first_day; $i <= $d; $i++) {
        $c[$i] = [
            'day' => "{$i}/$m",
            'count' => 0,
        ];
    }
    foreach ($charts as $chart) {
        $d = (int)date("d", strtotime($chart['date']));
        $c[$d]['count'] = $chart['count'];
    }

    //unset keys, because js parser sorting by key in integer
    $result = [];
    foreach ($c as $d){
        $result[] = $d;
    }
    return $result;
}

function wtotemsec_options_page()
{
    $host = WTOTEMSEC_LIBRARY_Webtotem::getOwnSite();
    $result = WTOTEMSEC_LIBRARY_Webtotem::getOptions($host['id']);
    $options = [];
    if (isset($result['data']['userHost']['services'])) {
        foreach ($result['data']['userHost']['services'] as $service) {
            $configs = $service['configs'][0];
            $options[$service['name']] = ['config_id' => $configs['id'], 'is_active' => $configs['isActive']];
        }
    }
    $options['host_id'] = $host['id'];
    wtotemsec_layout("options", $options);
}

function wtotemsec_services_page()
{
    wtotemsec_layout("services");
}

function wtotemsec_antivirus_page()
{
    $host = WTOTEMSEC_LIBRARY_Webtotem::getOwnSite();
    $site = WTOTEMSEC_LIBRARY_Webtotem::getAntivirus($host['id']);
    if (isset($site['data']['vcServiceChecks'][0])) {
        $site = $site['data']['vcServiceChecks'][0];
        $service = "VC";
        $_service = strtolower($service);
        $signatures = [];
        $changes = [];
        $errors = [];
        $sign_count = 0;
        $changes_count = 0;
        $domain = $host['hostname'];
        if (!empty($site['list'])) {
            foreach ($site['list'] as $list) {
                if ($list['event'] > -1 && empty($list['matches'])) {
                    $changes[$host['id']][] = $list;
                    $changes_count++;
                } elseif ($list['event'] > -1 && !empty($list['matches'])) {
                    $signatures[$host['id']][] = $list;
                    $sign_count++;
                } else {
                    $errors[$host['id']][] = $list;
                }
            }
        }
        $uid = $host['id'];
        $status = wtotemsec_checkStatus($uid, $service);
        $services["vc"] = [
            "pause" => WTOTEMSEC_LIBRARY_Webtotem::getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $uid),
            "site_address" => $domain,
            "status" => WTOTEMSEC_LIBRARY_Webtotem::getStatusIcon($site['status'], "vc"),
            "signatures" => $sign_count,
            "changes" => $changes_count,
            "list" => $site["list"],
            "actions" => $buttons = wtotemsec_generateButtons($uid, $_service, $service, $status, WTOTEMSEC_SITE_URL),
            "description" => WTOTEMSEC_LIBRARY_Localization::lmsg('descriptions.antivirus')
        ];
    }
    wtotemsec_layout("antivirus", $services);
}

function wtotemsec_settings_page()
{
    require_once WTOTEMSEC_PLUGIN_PATH . "includes/settings.php";
}

function wtotemsec_login_form()
{
    if (wtotemsec_request()->method === "POST") {
        $result = WTOTEMSEC_LIBRARY_Webtotem::auth(wtotemsec_request()->key);
        if (isset($result['data']['apiServiceMutation']['auth']['value'])) {
            WTOTEMSEC_LIBRARY_Session::setNotification("success", WTOTEMSEC_LIBRARY_Localization::lmsg('successfully_activated'));
            $token = $result['data']['apiServiceMutation']['auth']['value'];
            WTOTEMSEC_LIBRARY_App::login($token);
            wtotemsec_app()->set("api_key", wtotemsec_request()->key);
            wp_redirect(wtotemsec_getUrl('dashboard'));
            exit;
        } else {
            WTOTEMSEC_LIBRARY_Session::setNotification("error", WTOTEMSEC_LIBRARY_Localization::lmsg("form.incorrect"));
        }
    }
    wp_redirect(wtotemsec_getUrl('login'));
}

function wtotemsec_cmd_ajax()
{
    $service = strtoupper(wtotemsec_request()->service);
    $_service = strtolower(wtotemsec_request()->service);
    $uid = wtotemsec_request()->uid;
    $cmd = wtotemsec_request()->cmd;
    $status = wtotemsec_checkStatus($uid, $service);
    global $wp_filesystem;
    if (mb_stripos($cmd, "install") === false) {
        wtotemsec_cmd($wp_filesystem, $cmd, $service, $_service, $uid, $status, WTOTEMSEC_SITE_URL);
        wp_safe_redirect(wp_get_referer());
    } else {
        $redirect = wtotemsec_getUrl("dashboard");
        $form_url = wp_nonce_url(admin_url('admin-post.php?cmd=' . $cmd . '&action=ajax_cmd&service=' . $service . '&uid=' . $uid), "ajax_cmd");
        if (wtotemsec_filesystem_init($form_url, '', false, false)) {
            wtotemsec_cmd($wp_filesystem, $cmd, $service, $_service, $uid, $status, WTOTEMSEC_SITE_URL);
            wp_safe_redirect($redirect);
        }
    }
}

function wtotemsec_login()
{
    wtotemsec_layout("login");
}

function wtotemsec_logout()
{
    WTOTEMSEC_LIBRARY_App::logout();
}

function wtotemsec_layout($template, $arguments = [], $faq = "faq")
{
    $body = WTOTEMSEC_PLUGIN_PATH . "includes/" . $template . ".php";
    $faq = WTOTEMSEC_PLUGIN_PATH . "includes/" . $faq . ".php";
    require_once WTOTEMSEC_PLUGIN_PATH . "includes/layout.php";
}

function wtotemsec_getImagePath($image)
{
    return plugins_url('/htdocs/images/' . $image, __FILE__);
}

function wtotemsec_changeStatus()
{
    if (wtotemsec_request()->method === "POST") {
        $host_id = wtotemsec_request()->host_id;
        $config_id = wtotemsec_request()->config_id;
        $result = WTOTEMSEC_LIBRARY_Webtotem::changeStatus($config_id, $host_id);
    }
    wp_safe_redirect(wp_get_referer());
}

function wtotemsec_ajax_changeStatus()
{
    if (wtotemsec_request()->method === "POST") {
        $host_id = wtotemsec_request()->host_id;
        $config_id = wtotemsec_request()->config_id;
        $result = WTOTEMSEC_LIBRARY_Webtotem::changeStatus($config_id, $host_id);
        echo json_encode($result);
    }
    wp_die();
}

function wtotemsec_page($page)
{
    return wtotemsec_pages()[$page];
}

function wtotemsec_getUrl($page)
{
    return admin_url('admin.php?page=' . WTOTEMSEC_PAGE_PREFIX . $page);
}

function wtotemsec_getRoute($page)
{
    return WTOTEMSEC_PAGE_PREFIX . $page;
}

function wtotemsec_pages()
{
    return [
        "options" => [
            "page_title" => WTOTEMSEC_LIBRARY_Localization::lmsg('options'),
            "menu_title" => WTOTEMSEC_LIBRARY_Localization::lmsg('options'),
            "function" => "wtotemsec_options_page",
        ],
        "services" => [
            "page_title" => WTOTEMSEC_LIBRARY_Localization::lmsg('services'),
            "menu_title" => WTOTEMSEC_LIBRARY_Localization::lmsg('services'),
            "function" => "wtotemsec_services_page",
        ],
        "vc" => [
            "page_title" => WTOTEMSEC_LIBRARY_Localization::lmsg('remote_antivirus'),
            "menu_title" => WTOTEMSEC_LIBRARY_Localization::lmsg('remote_antivirus'),
            "function" => "wtotemsec_antivirus_page",
            "vc_action" => "vc-action",
            "vc_function" => "wtotemsec_vc_function"
        ],
    ];
}