<?php defined('ABSPATH') or die("Protected By WT!");

/*
Plugin Name: WT Security
Description: WT is a SaaS which provides powerful tools for securing and monitoring your website in one place in easy and flexible way.
Author: WT Security
Version: 1.0.3
*/
define("WTSEC_PAGE_TITLE", 'Dashboard');
define("WTSEC_MENU_TITLE", 'WT Security');
define("WTSEC_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("WTSEC_PLUGIN_NAME", 'wt-security');
define("WTSEC_PLUGIN_PREFIX", 'wtsec_');
define("WTSEC_PAGE_PREFIX", WTSEC_PLUGIN_NAME . '-');
define("WTSEC_FILE_URL", "https://api.wtotem.com");
define("WTSEC_ROOT", WP_PLUGIN_DIR . '/');
define("WTSEC_MODULES_DIR", WTSEC_PLUGIN_PATH . 'uploads/');
if (is_dir(WTSEC_MODULES_DIR) && is_writable(WTSEC_MODULES_DIR)) {
    define("WTSEC_INSTALLATION_DIR", WTSEC_MODULES_DIR);
    define("WTOTEMSEC_INSTALLATION_DIR", WTSEC_MODULES_DIR);
} else {
    define("WTSEC_INSTALLATION_DIR", wp_upload_dir()['basedir'] . '/');
    define("WTOTEMSEC_INSTALLATION_DIR", wp_upload_dir()['basedir'] . '/');
}
define("WTSEC_PLUGIN_URL", plugins_url("", __FILE__));
define("WTSEC_SITE_URL", str_replace(['http://', 'https://', 'www.', '//', '://'], '', get_site_url()));

define("WTSEC_PLUGIN_INFORMATION_VERSION", "1.0");

add_action('admin_init', 'wtsec_admin_init');
add_action('wp_loaded', 'wtsec_init');

function wtsec_init()
{
    require_once WTSEC_PLUGIN_PATH . 'library/App.php';
    require_once WTSEC_PLUGIN_PATH . 'library/Request.php';
    if (in_array(WTSEC_LIBRARY_App::getOption('waf_status'), ["start", "uninstalled"])) {
        if ($waf = WTSEC_LIBRARY_App::getOption(("waf_installed_file"))) {
            $path_to_waf = WTSEC_INSTALLATION_DIR . '/' . $waf;
            if (is_file($path_to_waf) && is_readable($path_to_waf)) {
                include $path_to_waf;
            }
        }
    }
    if (is_admin()) {
        require_once WTSEC_PLUGIN_PATH . 'library/WT.php';
        require_once WTSEC_PLUGIN_PATH . 'library/Localization.php';
        require_once WTSEC_PLUGIN_PATH . 'library/Idn.php';
        require_once WTSEC_PLUGIN_PATH . 'library/Session.php';
        require_once WTSEC_PLUGIN_PATH . 'routes.php';
        require_once WTSEC_PLUGIN_PATH . 'services.php';
        function wtsec_request()
        {
            return new WTSEC_LIBRARY_Request();
        }
        function wtsec_app()
        {
            return new WTSEC_LIBRARY_App();
        }
        function wtsec_filesystem_init($form_url, $method, $context, $fields = null)
        {
            global $wp_filesystem;
            if (!$creds = request_filesystem_credentials($form_url, $method, false, $context, $fields)) {
                return false;
            }
            if (!WP_Filesystem($creds)) {
                request_filesystem_credentials($form_url, $method, true, $context);
                return false;
            }
            return true;
        }
        function wtsec_locale($lmsg, $args = [])
        {
            return WTSEC_LIBRARY_Localization::lmsg($lmsg, $args);
        }
        function wtsec_getImagePath($image)
        {
            return plugins_url('/htdocs/images/' . $image, __FILE__);
        }
        function wtsec_SIS($arr, $key, $form = null)
        {
            $key = explode(".", $key);
            foreach ($key as $_key) {
                if (!isset($arr[$_key])) {
                    $arr = "";
                    break;
                }
                $arr = $arr[$_key];
            }
            return !empty($form) ? str_replace("$" . $key, $arr, $form) : $arr;
        }
    }
}

function wtsec_admin_init()
{
    if (is_admin() && stripos(wtsec_request()->page, WTSEC_PLUGIN_NAME) !== false) {
        wp_register_style("css", plugins_url('/htdocs/css/src.c37ba8ac.css', __FILE__));
        wp_enqueue_style('css');
        wp_enqueue_script('chart', plugins_url('/htdocs/js/Chart.min.js', __FILE__), [], false, true);
        wp_enqueue_script('chart');
        wp_enqueue_script('app', plugins_url('/htdocs/js/src.e31bb0bc.js', __FILE__), [], false, true);
        wp_enqueue_script('app');
        $page = wtsec_request()->page;
        if ($page === wtsec_getRoute("login")) {
            if (WTSEC_LIBRARY_App::authorized()) {
                wp_redirect(wtsec_getUrl('dashboard'));
            }
        } elseif ($page === wtsec_getRoute("logout")) {
            WTSEC_LIBRARY_App::logout();
            if (!WTSEC_LIBRARY_App::authorized()) {
                wp_redirect(wtsec_getUrl('login'));
            }
        } elseif ($page === wtsec_getRoute("activate")) {
            if (WTSEC_LIBRARY_App::authorized()) {
                wp_redirect(wtsec_getUrl('sites'));
            }
        } else {
            if (!WTSEC_LIBRARY_App::authorized()) {
                wp_redirect(wtsec_getUrl('login'));
            }
        }
    }
}
