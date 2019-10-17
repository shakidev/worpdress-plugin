<?php
/*
Plugin Name: Webtotem Security
Plugin URI: https://wtotem.com/
Description: WebTotem is a SaaS which provides powerful tools for securing and monitoring your website in one place in easy and flexible way.
Author: Webtotem LLC
Version: 2.0
*/
define("WTOTEMSEC_PAGE_TITLE", 'Sites');
define("WTOTEMSEC_MENU_TITLE", 'Webtotem Security');
define("WTOTEMSEC_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("WTOTEMSEC_PLUGIN_NAME", 'webtotem-security');
define("WTOTEMSEC_PLUGIN_PREFIX", 'wtotemsec_');
define("WTOTEMSEC_PAGE_PREFIX", WTOTEMSEC_PLUGIN_NAME . '-');
//define("WTOTEMSEC_FILE_URL", "https://m875vnqd.wtotem.com");
define("WTOTEMSEC_FILE_URL", "https://api.wtotem.com");
define("WTOTEMSEC_ROOT", WP_PLUGIN_DIR . '/');
define("WTOTEMSEC_MODULES_DIR", WTOTEMSEC_PLUGIN_PATH . 'uploads/');
if (is_dir(WTOTEMSEC_MODULES_DIR) && is_writable(WTOTEMSEC_MODULES_DIR)) {
    define("WTOTEMSEC_INSTALLATION_DIR", WTOTEMSEC_MODULES_DIR);
} else {
    define("WTOTEMSEC_INSTALLATION_DIR", wp_upload_dir()['basedir'] . '/');
}
define("WTOTEMSEC_PLUGIN_URL", plugins_url("", __FILE__));
define("WTOTEMSEC_SITE_URL", str_replace(['http://', 'https://', 'www.', '//', '://'], '', get_site_url()));

define("WTOTEMSEC_PLUGIN_INFORMATION_VERSION", "2.0");

add_action('admin_init', 'wtotemsec_admin_init');
add_action('wp_loaded', 'wtotemsec_init');

function wtotemsec_app()
{
    return new WTOTEMSEC_LIBRARY_App();
}

require_once WTOTEMSEC_PLUGIN_PATH . 'routes.php';
require_once WTOTEMSEC_PLUGIN_PATH . 'library/App.php';
require_once WTOTEMSEC_PLUGIN_PATH . 'library/Request.php';
require_once WTOTEMSEC_PLUGIN_PATH . 'library/WebTotem.php';
require_once WTOTEMSEC_PLUGIN_PATH . 'library/Localization.php';
require_once WTOTEMSEC_PLUGIN_PATH . 'library/Render.php';
require_once WTOTEMSEC_PLUGIN_PATH . 'library/Idn.php';
require_once WTOTEMSEC_PLUGIN_PATH . 'library/Session.php';
require_once WTOTEMSEC_PLUGIN_PATH . 'services.php';

function wtotemsec_init()
{
    function wtotemsec_request()
    {
        return new WTOTEMSEC_LIBRARY_Request();
    }

    if (WTOTEMSEC_LIBRARY_App::getOption('waf_status') === "start") {
        $waf = wtotemsec_getInstalledFile("waf");
        if (!is_null($waf)) {
            include WTOTEMSEC_INSTALLATION_DIR . '/' . $waf;
        }
    }
}

function wtotemsec_admin_init()
{
    if (is_admin() && stripos(wtotemsec_request()->page, WTOTEMSEC_PLUGIN_NAME) !== false) {
        add_action('admin_head', function () {
            echo "<script>
    window.change_status_url = '" . wtotemsec_getUrl('changestatus') . "';
    window.logout_url = 'logout';
    window.service = '';
    window.notification_lang = {token_expired: '" . WTOTEMSEC_LIBRARY_Localization::lmsg('invalid_token') . "',time:'" . WTOTEMSEC_LIBRARY_Localization::lmsg('sec') . "',text:'" . WTOTEMSEC_LIBRARY_Localization::lmsg('page_reload') . "'};</script>";
        });
        wp_register_style("css", plugins_url('/htdocs/css/src.c37ba8ac.css', __FILE__));
        wp_enqueue_style('css');
        wp_enqueue_script('chart', plugins_url('/htdocs/js/Chart.min.js', __FILE__), [], false, true);
        wp_enqueue_script('chart');
        wp_enqueue_script('app', plugins_url('/htdocs/js/src.e31bb0bc.js', __FILE__), [], false, true);
        wp_enqueue_script('app');
        $page = wtotemsec_request()->page;
        function wtotemsec_renderTabs()
        {
            $nav = '<nav class="nav-tab-wrapper wp-clearfix">';
            foreach (wtotemsec_pages() as $page => $arguments) {
                $nav .= '<a href="' . wtotemsec_getUrl($page) . '" class="nav-tab ' . (wtotemsec_request()->page === wtotemsec_getRoute($page) ? 'nav-tab-active' : '') . '">' . $arguments['menu_title'] . '</a>';
            }
            $nav .= '</nav>';
            return $nav;
        }

        if ($page === wtotemsec_getRoute("login")) {
            if (WTOTEMSEC_LIBRARY_App::authorized()) {
                wp_redirect(wtotemsec_getUrl('dashboard'));
            }
        } elseif ($page === wtotemsec_getRoute("logout")) {
            WTOTEMSEC_LIBRARY_App::logout();
            if (!WTOTEMSEC_LIBRARY_App::authorized()) {
                wp_redirect(wtotemsec_getUrl('login'));
            }
        } elseif ($page === wtotemsec_getRoute("activate")) {
            if (WTOTEMSEC_LIBRARY_App::authorized()) {
                wp_redirect(wtotemsec_getUrl('sites'));
            }
        } else {
            if (!WTOTEMSEC_LIBRARY_App::authorized()) {
                wp_redirect(wtotemsec_getUrl('login'));
            }
        }
    }
}

function wtotemsec_filesystem_init($form_url, $method, $context, $fields = null)
{
    global $wp_filesystem;
    if (false === ($creds = request_filesystem_credentials($form_url, $method, false, $context, $fields))) {
        return false;
    }
    if (!WP_Filesystem($creds)) {
        request_filesystem_credentials($form_url, $method, true, $context);
        return false;
    }
    return true;
}


function wtotemsec_locale($lmsg, $args = [])
{
    return WTOTEMSEC_LIBRARY_Localization::lmsg($lmsg, $args);
}

function dd($value)
{
    die("<pre>" . print_r($value) . "</pre>");
}
