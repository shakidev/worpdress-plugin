<?php defined('ABSPATH') or die("Protected By WT!");

add_action('admin_menu', 'wtsec_add_menu_link');
add_action('wp_ajax_change_status', 'wtsec_ajax_changeStatus');
add_action('admin_post_change_status', 'wtsec_changeStatus');
add_action('wp_ajax_cmd', 'wtsec_cmd_ajax');
add_action('admin_post_login_form', 'wtsec_login_form');
add_action('admin_post_ajax_cmd', 'wtsec_cmd_ajax');

function wtsec_add_menu_link()
{
    add_menu_page(
        WTSEC_PAGE_TITLE,
        WTSEC_MENU_TITLE,
        'manage_options',
        wtsec_getRoute('dashboard'),
        'wtsec_index_page',
        '',
        '1'
    );
    $parent = wtsec_getRoute('dashboard');
    if (WTSEC_LIBRARY_App::authorized()) {
        $capability = 'manage_options';
        foreach (wtsec_pages() as $page => $arguments) {
            add_submenu_page($parent, $arguments['page_title'], $arguments['menu_title'], $capability, wtsec_getRoute($page), $arguments['function']);
        }
        add_submenu_page(null, WTSEC_LIBRARY_Localization::lmsg('sign_in'), WTSEC_LIBRARY_Localization::lmsg('sign_in'), 'manage_options', wtsec_getRoute('login'), 'wtsec_login');
        add_submenu_page($parent, WTSEC_LIBRARY_Localization::lmsg('logout'), WTSEC_LIBRARY_Localization::lmsg('logout'), 'manage_options', wtsec_getRoute('logout'), 'wtsec_logout');
    } else {
        $capability = 'manage_options';
        foreach (wtsec_pages() as $page => $arguments) {
            add_submenu_page(null, $arguments['page_title'], $arguments['menu_title'], $capability, wtsec_getRoute($page), function () {
                wp_redirect(wtsec_getUrl('login'));
            });
        }
        add_submenu_page($parent, WTSEC_LIBRARY_Localization::lmsg('sign_in'), WTSEC_LIBRARY_Localization::lmsg('sign_in'), 'manage_options', wtsec_getRoute('login'), 'wtsec_login');
        add_submenu_page(null, WTSEC_LIBRARY_Localization::lmsg('logout'), WTSEC_LIBRARY_Localization::lmsg('logout'), 'manage_options', wtsec_getRoute('logout'), 'wtsec_logout');
    }

}

function wtsec_index_page()
{
    $host = WTSEC_LIBRARY_WT::getOwnSite();
    $services = [];
    if (!empty($host)) {
        $checks = WTSEC_LIBRARY_WT::getAllChecks($host['id']);
        foreach ($checks['data'] as $service => $site) {
            if (empty($site) || !is_array($site)) {
                continue;
            }
            $site = $site[0];
            switch ($service) {
                case "waServiceChecks":
                    $services["wa"] = [
                        "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "wa"),
                        "response_time" => $site['responseTime']['avg'] . ' ' . WTSEC_LIBRARY_Localization::lmsg('ms'),
                        "availability" => ceil($site['average']['uptimePercent'] * 100) . '%',
                        "availability_percent" => ceil($site['average']['uptimePercent'] * 100),
                        "downtime" => ceil($site['average']['totalDown'] / 1000) . ' ' . WTSEC_LIBRARY_Localization::lmsg('sec'),
                        "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.availability')
                    ];
                    break;
                case "sslServiceChecks":
                    $services["ssl"] = [
                        "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "information" => wtsec_getInformation($service, $site['status']),
                        "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "ssl"),
                        "days_left" => $site['daysLeft'],
                        "issue_date" => date("Y-m-d H:i", $site['issued'] / 1000),
                        "expiry_date" => date("Y-m-d H:i", $site['expires'] / 1000),
                        "tls" => $site['tls'],
                        "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.ssl')
                    ];
                    break;
                case "decServiceChecks":
                    $services["dec"] = [
                        "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "registrar" => $site['registrar'],
                        "owner" => $site['owner'],
                        "information" => wtsec_getInformation($service, $site['status']),
                        "email" => $site['email'],
                        "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "dec"),
                        "days_left" => $site['daysLeft'],
                        "created" => date("Y-m-d", $site['created'] / 1000),
                        "expiry_date" => date("Y-m-d", $site['expires'] / 1000),
                        "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.domain')
                    ];
                    break;
                case "avServiceChecks":
                    $services["av"] = [
                        "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "av"),
                        "time_of_the_last_test" => date("Y-m-d H:i", $site['lastTestTime'] / 1000),
                        "blacklists_entries" => $site['count'],
                        "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.reputation')
                    ];
                    break;
                case "cmsServiceChecks":
                    $services["cms"] = [
                        "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "cms"),
                        "time_of_the_last_test" => date("Y-m-d H:i", $site['lastTestTime'] / 1000),
                        "detected_keywords" => $site['count'],
                        "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.malicious')
                    ];
                    break;
                case "dcServiceChecks":
                    $services["dc"] = [
                        "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "dc"),
                        "time_of_the_last_test" => date("Y-m-d H:i", $site['lastTestTime'] / 1000),
                        "number" => $site['count'],
                        "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.deface')
                    ];
                    break;
                case "psServiceChecks":
                    $services["ps"] = [
                        "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $host['id']),
                        "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "ps"),
                        "ip" => $site['ip'],
                        "time_of_the_last_test" => date("Y-m-d H:i", $site['lastTestTime'] / 1000),
                        "number" => $site['count'],
                        "tcp" => !empty($site['openTCPs']) ? implode(",", $site['openTCPs']) : '',
                        "udp" => !empty($site['openUDPs']) ? implode(",", $site['openUDPs']) : '',
                        "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.port')
                    ];
                    break;
                case "wafServiceChecks":
                    $service = "WAF";
                    $_service = strtolower($service);
                    $domain = $host['hostname'];
                    $uid = $host['id'];
                    $status = wtsec_checkStatus($uid, $service);
                    $services["waf"] = [
                        "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $uid),
                        "site_address" => $domain,
                        "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "waf"),
                        "time_of_the_last_check" => empty($site['lastTestTime']) ? '' : date("Y-m-d H:i", $site['lastTestTime'] / 1000),
                        "signatures" => $site['count'],
                        "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.firewall'),
                        "actions" => wtsec_generateButtons($uid, $_service, $service, $status, WTSEC_SITE_URL),
                        "chart" => json_encode((array)generateChart($site['chart']), true)
                    ];
                    break;
                case "vcServiceChecks":
                    $service = "VC";
                    $_service = strtolower($service);
                    $domain = $host['hostname'];
                    $uid = $host['id'];
                    $status = wtsec_checkStatus($uid, $service);
                    $services["vc"] = [
                        "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $uid),
                        "site_address" => $domain,
                        "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "vc"),
                        "signatures" => $site['signaturesCount'],
                        "changes" => $site['fileChangesCount'],
                        "list" => $site["list"],
                        "actions" => wtsec_generateButtons($uid, $_service, $service, $status, WTSEC_SITE_URL),
                        "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.antivirus')
                    ];
                    break;
            }
        }
    }
    wtsec_layout("dashboard", $services);
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

    //unset keys, because the js parser sorting by key in integer
    $result = [];
    foreach ($c as $d) {
        $result[] = $d;
    }
    return $result;
}

function wtsec_getInformation($service, $status)
{
    $information = [
        "sslServiceChecks" => [
            WTSEC_LIBRARY_Localization::lmsg("statuses.ok"),
            WTSEC_LIBRARY_Localization::lmsg("statuses.invalid"),
            WTSEC_LIBRARY_Localization::lmsg("statuses.expired"),
            WTSEC_LIBRARY_Localization::lmsg("statuses.expires"),
            "unknown_status" => WTSEC_LIBRARY_Localization::lmsg("statuses.missing"),
        ],
        "decServiceChecks" => [
            WTSEC_LIBRARY_Localization::lmsg("statuses.ok"),
            WTSEC_LIBRARY_Localization::lmsg("statuses.expires"),
            "unknown_status" => WTSEC_LIBRARY_Localization::lmsg("statuses.error"),
        ]
    ];
    return isset($information[$service][$status]) ? $information[$service][$status] : $information[$service]["unknown_status"];
}
function wtsec_getStatusStartPauseIcon($status, $config_id, $host_id)
{
    $icon = '';
    switch ($status) {
        case "1":
            $icon = '<div class="v-pause ww-icon ww-icon--pause" style="border:none" data-config_id="' . $config_id . '" data-host_id="' . $host_id . '"></div>';
            break;
        case "0":
            $icon = '<div class="v-pause ww-icon ww-icon--play" style="border:none" data-config_id="' . $config_id . '" data-host_id="' . $host_id . '"></div>';
            break;
    }
    return $icon;
}

function wtsec_options_page()
{
    $host = WTSEC_LIBRARY_WT::getOwnSite();
    $result = WTSEC_LIBRARY_WT::getOptions($host['id']);
    $options = [];
    if (isset($result['data']['userHost']['services'])) {
        foreach ($result['data']['userHost']['services'] as $service) {
            $configs = $service['configs'][0];
            $options[$service['name']] = ['config_id' => $configs['id'], 'is_active' => $configs['isActive']];
        }
    }
    $options['host_id'] = $host['id'];
    wtsec_layout("options", $options);
}

function wtsec_services_page()
{
    wtsec_layout("services");
}

function wtsec_antivirus_page()
{
    $host = WTSEC_LIBRARY_WT::getOwnSite();
    $site = WTSEC_LIBRARY_WT::getAntivirus($host['id']);
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
        $status = wtsec_checkStatus($uid, $service);
        $services["vc"] = [
            "pause" => wtsec_getStatusStartPauseIcon($site['config']['isActive'], $site['config']['id'], $uid),
            "site_address" => $domain,
            "status" => WTSEC_LIBRARY_WT::getStatusIcon($site['status'], "vc"),
            "signatures" => $sign_count,
            "changes" => $changes_count,
            "list" => $site["list"],
            "actions" => $buttons = wtsec_generateButtons($uid, $_service, $service, $status, WTSEC_SITE_URL),
            "description" => WTSEC_LIBRARY_Localization::lmsg('descriptions.antivirus')
        ];
    }
    wtsec_layout("antivirus", $services);
}

function wtsec_login_form()
{
    if (wtsec_request()->method === "POST") {
        $result = WTSEC_LIBRARY_WT::auth(wtsec_request()->key);
        if (isset($result['data']['apiServiceMutation']['auth']['value'])) {
            WTSEC_LIBRARY_Session::setNotification("success", WTSEC_LIBRARY_Localization::lmsg('successfully_activated'));
            $token = $result['data']['apiServiceMutation']['auth']['value'];
            WTSEC_LIBRARY_App::login($token);
            wtsec_app()->set("api_key", wtsec_request()->key);
            wp_redirect(wtsec_getUrl('dashboard'));
            exit;
        } else {
            WTSEC_LIBRARY_Session::setNotification("error", WTSEC_LIBRARY_Localization::lmsg("form.incorrect"));
        }
    }
    wp_redirect(wtsec_getUrl('login'));
}

function wtsec_cmd_ajax()
{
    $service = strtoupper(wtsec_request()->service);
    $_service = strtolower(wtsec_request()->service);
    $uid = wtsec_request()->uid;
    $cmd = wtsec_request()->cmd;
    $status = wtsec_checkStatus($uid, $service);
    global $wp_filesystem;
    if (mb_stripos($cmd, "install") === false) {
        wtsec_cmd($wp_filesystem, $cmd, $service, $_service, $uid, $status, WTSEC_SITE_URL);
        wp_safe_redirect(wp_get_referer());
    } else {
        $redirect = wtsec_getUrl("dashboard");
        $form_url = wp_nonce_url(admin_url('admin-post.php?cmd=' . $cmd . '&action=ajax_cmd&service=' . $service . '&uid=' . $uid), "ajax_cmd");
        if (wtsec_filesystem_init($form_url, '', false, false)) {
            wtsec_cmd($wp_filesystem, $cmd, $service, $_service, $uid, $status, WTSEC_SITE_URL);
            wp_safe_redirect($redirect);
        }
    }
}

function wtsec_login()
{
    wtsec_layout("login");
}

function wtsec_logout()
{
    WTSEC_LIBRARY_App::logout();
}


function wtsec_layout($template, $arguments = [], $faq = "faq")
{
    $body = WTSEC_PLUGIN_PATH . "includes/" . $template . ".php";
    $faq = WTSEC_PLUGIN_PATH . "includes/" . $faq . ".php";
    require_once WTSEC_PLUGIN_PATH . "includes/layout.php";
}


function wtsec_ajax_changeStatus()
{
    if (wtsec_request()->method === "POST") {
        $host_id = wtsec_request()->host_id;
        $config_id = wtsec_request()->config_id;
        $result = WTSEC_LIBRARY_WT::changeStatus($config_id, $host_id);
        echo json_encode($result);
    }
    wp_die();
}

function wtsec_changeStatus()
{
    if (wtsec_request()->method === "POST") {
        $host_id = wtsec_request()->host_id;
        $config_id = wtsec_request()->config_id;
        WTSEC_LIBRARY_WT::changeStatus($config_id, $host_id);
    }
    wp_safe_redirect(wp_get_referer());
}

function wtsec_page($page)
{
    return wtsec_pages()[$page];
}

function wtsec_getUrl($page)
{
    return admin_url('admin.php?page=' . WTSEC_PAGE_PREFIX . $page);
}

function wtsec_getRoute($page)
{
    return WTSEC_PAGE_PREFIX . $page;
}

function wtsec_pages()
{
    return [
        "options" => [
            "page_title" => WTSEC_LIBRARY_Localization::lmsg('options'),
            "menu_title" => WTSEC_LIBRARY_Localization::lmsg('options'),
            "function" => "wtsec_options_page",
        ],
        "services" => [
            "page_title" => WTSEC_LIBRARY_Localization::lmsg('services'),
            "menu_title" => WTSEC_LIBRARY_Localization::lmsg('services'),
            "function" => "wtsec_services_page",
        ],
        "vc" => [
            "page_title" => WTSEC_LIBRARY_Localization::lmsg('remote_antivirus'),
            "menu_title" => WTSEC_LIBRARY_Localization::lmsg('remote_antivirus'),
            "function" => "wtsec_antivirus_page",
            "vc_action" => "vc-action",
            "vc_function" => "wtsec_vc_function"
        ],
    ];
}