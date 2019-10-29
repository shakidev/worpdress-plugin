<?php defined('ABSPATH') or die("Protected By WT!");


class WTSEC_LIBRARY_WT
{
    const URL = "https://api.wtotem.com/v1/graphql";

    public static function auth($key)
    {
        $payload = '{"query":"mutation{\n  apiServiceMutation{\n    auth(apiKey:\"' . $key . '\"){\n      value\n      refreshToken\n      expiresIn\n    }\n  }\n}\n\n"}';
        return self::requestApi($payload);
    }

    protected static function requestApi($payload, $token = false, $repeat = false)
    {
        if ($token) {
            $token = WTSEC_LIBRARY_App::getToken();
        }
        $args = [
            'body' => $payload,
            'timeout' => '15',
            'sslverify' => false,
            'headers' => ['Content-Type:application/json'],
        ];
        if (!is_null($token) && $token) {
            $authorization = "Bearer " . $token;
            $args['headers'] = array_merge($args['headers'], ["Authorization" => $authorization]);
        }
        $response = wp_remote_post(self::URL, $args);
        $httpcode = wp_remote_retrieve_response_code($response);

        if ($httpcode < 200) {
            WTSEC_LIBRARY_Session::setNotification("error", WTSEC_LIBRARY_Localization::lmsg('could_not_connect_to_the_server'));
        }
        $response = wp_remote_retrieve_body($response);

        $result = json_decode($response, true);
        if (isset($result['errors'][0]['message'])) {
            $message = self::diffMesageForHuman($result['errors'][0]['message']);
            if (stripos($result['errors'][0]['message'], "token") !== false && !$repeat) {
                $result = WTSEC_LIBRARY_WT::auth(wtsec_app()->get("api_key"));
                if (isset($result['data']['apiServiceMutation']['auth']['value'])) {
                    $token_ = $result['data']['apiServiceMutation']['auth']['value'];
                    WTSEC_LIBRARY_App::login($token_);
                    return self::requestApi($payload, $token, true);
                } else {
                    WTSEC_LIBRARY_App::logout();
                }
            } else {
                if ($message !== false) {
                    WTSEC_LIBRARY_Session::setNotification("warning", $message);
                }
            }
        }
        return $result;
    }

    public static function diffMesageForHuman($message)
    {
        $definition = $message;
        $excepts = [
            "RESOURCE_NOT_FOUND", "DUPLICATE_HOST", "INVALID_CREDENTIALS", "INVALID_API_KEY", "Invalid token",
        ];
        if (in_array($message, $excepts)) {
            return false;
        }
        switch ($message) {
            case 'HOSTS_LIMIT_EXCEEDED':
                $definition = WTSEC_LIBRARY_Localization::lmsg('hosts_limit_exceed');
                break;
            case 'RESOURCE_NOT_FOUND':
                $definition = WTSEC_LIBRARY_Localization::lmsg('resource_not_found');
                break;
            case 'Invalid token':
                $definition = WTSEC_LIBRARY_Localization::lmsg('invalid_token');
                break;
            case 'USER_ALREADY_REGISTERED':
                $definition = WTSEC_LIBRARY_Localization::lmsg('user_already_exist');
                break;
            case 'DUPLICATE_HOST':
                $definition = WTSEC_LIBRARY_Localization::lmsg('duplicate_host');
                break;
            case 'Agent does not exist or has been already verified':
                $definition = WTSEC_LIBRARY_Localization::lmsg('agent_does_not_exist_or_has_been_already_verified');
                break;
            case 'INVALID_DOMAIN_NAME':
                $definition = WTSEC_LIBRARY_Localization::lmsg('invalid_domain_name');
                break;
        }
        return $definition;
    }

    public static function requestURL($url)
    {
        $args = [
            'timeout' => '15',
            'sslverify' => false,
        ];
        $response = wp_remote_post($url, $args);
        $httpcode = wp_remote_retrieve_response_code($response);
        if ($httpcode < 200) {
            WTSEC_LIBRARY_Session::setNotification("error", WTSEC_LIBRARY_Localization::lmsg('could_not_connect_to_the_server'));
        }
        $response = wp_remote_retrieve_body($response);
        return $response;
    }

    public static function getOwnSite()
    {
        $payload = '{"query":"{\n  userHostsList{\n    id\n    hostname\n  }\n}"}';
        $result = self::requestApi($payload, true);
        if (isset($result['data']['userHostsList'])) {
            //mutator
            foreach ($result['data']['userHostsList'] as &$m) {
                if (isset($m['hostname'])) {
                    $m['hostname'] = WTSEC_LIBRARY_Idn::idn_to_utf8($m['hostname']);
                    if (self::isSiteUrl($m['hostname'])) {
                        return $result['data']['userHostsList'] = $m;
                        break;
                    }
                }
            }
            $add_site = self::addSite(WTSEC_SITE_URL);
            if (isset($add_site['errors'])) {
                return $result['data']['userHostsList'] = [];
            } else {
                return self::getOwnSite();
            }
        }
        return [];
    }

    public static function isSiteUrl($url)
    {
        return WTSEC_LIBRARY_Idn::idn_to_utf8(WTSEC_SITE_URL) === $url;
    }

    public static function addSite($url)
    {
        $payload = '{"query":"\n        mutation($input: AddUserHostInput!) {\n          addUserHost(input: $input) {\n            id\n            title\n            hostname\n            tags\n            stack\n            services {\n              id\n              name\n              configs {\n                id\n                data\n                isActive\n                createdAt\n              }\n            }\n            isActive\n            createdAt\n          }\n        }\n      ","variables":{"input":{"hostname":"' . $url . '","services":[{"id":1,"configs":[{"scheme":"http","port":80,"check_interval":1,"responsetime_threshold":30,"path":"/","http_errors":[400,401,402,403,404,500,501,502,503],"alert_after":0}]},{"id":2,"configs":[{"check_interval":5,"notify_expiry_day":true,"notify_expiry_month":true,"notify_expiry_week":true,"port":443}]},{"id":4,"configs":[{"check_interval":5,"path":"/","scheme":"http","port":80}]},{"id":5,"configs":[{"check_interval":5,"path":"/","scheme":"http","port":80}]},{"id":6,"configs":[{"check_interval":5,"path":"/","scheme":"http","port":80}]},{"id":7,"configs":[{"check_interval":5,"ports_udp":[18,19,53,27,29,31,71,74],"ports_tcp":[21,22,25,3306,5432,80,443,88,8000,8080]}]},{"id":9,"configs":[{"scheme":"http","port":80,"path":"/"}]},{"id":8,"configs":[{"check_interval":30,"scheme":"http","port":80,"path":"/"}]},{"id":3,"configs":[{"check_interval":60,"notify_expiry_day":true,"notify_expiry_month":true,"notify_expiry_week":true}]},{"id":10,"configs":[{"check_interval":30,"path":"/","scheme":"http","port":80}]}],"title":"' . $url . '"}}}';
        return self::requestApi($payload, true);
    }

    public static function getAllChecks($host_id)
    {
        $from = time() - (60 * 60 * 24);
        $to = time();
        $from_waf = time() - (60 * 60 * 24 * 7);
        $payload = '{"query":"query getAllChecks( $hostId:Int!, $dateRange: DateRangeInput!, $dateRangeWaf:DateRangeInput! ){\n  waServiceChecks(userHostId:$hostId){\n    config{\n      id\n      isActive\n    }\n    isDown\n    status\n    average(dateRange: $dateRange)\n    responseTime(dateRange: $dateRange)\n    testsResults(dateRange: $dateRange)\n  }\n  sslServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    status\n    issued\n    expires\n    daysLeft\n    tls\n  }\n  decServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    email\n    daysLeft\n    created\n    expires\n    registrar\n    owner\n    status\n  }\n  avServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    lastTestId\n    lastTestTime\n    status\n    count\n    list\n  }\n  cmsServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    lastTestId\n    lastTestTime\n    status\n    count\n    list\n  }\n  dcServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    lastTestTime\n    status\n    count\n    list\n  }\n  psServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    ip\n    lastTestId\n    lastTestTime\n    status\n    count\n    openTCPs\n    openUDPs\n  }\n  wafServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    lastTestTime\n    status\n    count\n    countIP       \n    chart(dateRangeWaf:$dateRangeWaf){\n      date\n      count\n    }\n  }\n  vcServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    status\n    list\n    fileChangesCount\n    errorsCount\n    signaturesCount\n  }\n  \n}\n\n\n\n","variables":{"hostId":' . $host_id . ',"dateRange":{"from":' . $from . ',"to":' . $to . '},"dateRangeWaf":{"to":' . $to . ',"from":' . $from_waf . '}}}';
        return self::requestApi($payload, true);
    }

    public static function changeStatus($config_id, $host_id)
    {
        $payload = '{"query":"\n        mutation {\n          toggleServiceConfig(\n            id: ' . $config_id . '\n            userhostId: ' . $host_id . '\n          ) {\n            isActive\n          }\n        }\n      "}';
        return self::requestApi($payload, true);
    }

    public static function serviceConnect($id, $service)
    {
        $payload = '{"query":"{ checkAgent(userHostId: ' . $id . ', service: ' . strtoupper($service) . ') }"}';
        return self::requestApi($payload, true);
    }

    public static function generateFile($id, $service)
    {
        $payload = '{"query":"{ generateAgent(userHostId: ' . $id . ', service: ' . strtoupper($service) . ') }"}';
        return self::requestApi($payload, true);
    }

    public static function checkStatus($id, $service)
    {
        $payload = '{"query":"{\n  ' . strtolower($service) . 'ServiceChecks(userHostId:' . $id . '){\n    status\n    config{\n      isActive\n  id\n  }\n  }\n}"}';
        return self::requestApi($payload, true);
    }

    public static function getOptions($host_id)
    {
        $payload = '{"query":"query{\nuserHost(id:' . $host_id . '){\n    id\n    title\n    hostname\n    stack\n    createdAt\n    services {\n      id\n      name\n      configs {\n        id\n      \tdata\n        isActive\n      }\n    }\n  }\n}"}';
        return self::requestApi($payload, true);
    }

    public static function getAntivirus($host_id)
    {
        $payload = '{"query":"query{\n  vcServiceChecks(userHostId:' . $host_id . '){\n    config{\n      id,\n      isActive\n    }\n    status\n    list\n    fileChangesCount\n    errorsCount\n    signaturesCount\n  }\n}\n\n"}';
        return self::requestApi($payload, true);
    }

    public static function getStatusIcon($status, $service)
    {
        $color = '';
        $definition = '';
        switch ($service) {
            case 'wa':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor("error");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor("success");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor("error");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.unavailable');
                        break;
                }
                break;
            case 'ssl':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor("green");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.invalid');
                        break;
                    case '2':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.expired');
                        break;
                    case '3':
                        $color = self::getColor("orange");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.expires');
                        break;
                    case '4':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.expires_today');
                        break;
                }
                break;
            case 'dec':
                switch ((string)$status) {
                    case '-3':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.not_registered');
                        break;
                    case '-2':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.unsupported');
                        break;
                    case '-1':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor("green");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor("orange");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.expires');
                        break;
                    case '2':
                        $color = self::getColor("orange");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.expired');
                        break;
                    case '3':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.expires_today');
                        break;
                }
                break;
            case 'av':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor("green");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.blacklisted');
                        break;
                }
                break;
            case 'cms':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor("green");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.miner_detected');
                        break;
                }
                break;
            case 'dc':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor("green");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.deface');
                        break;
                    case '2':
                        $color = self::getColor("orange");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.modified');
                        break;
                }
                break;
            case 'ps':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor("green");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor("orange");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.open');
                        break;
                }
                break;
            case 'waf':
                switch ((string)$status) {
                    case '-400':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.blocked');
                        break;
                    case '-300':
                        $color = self::getColor("orange");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.not_installed');
                        break;
                    case '-1':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor("green");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.attacks_detected');
                        break;
                }
                break;
            case 'vc':
                switch ((string)$status) {
                    case '-400':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.blocked');
                        break;
                    case '-300':
                        $color = self::getColor("orange");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.not_installed');
                        break;
                    case '-1':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor("green");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor("orange");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.file_changes');
                        break;
                    case '2':
                        $color = self::getColor("red");
                        $definition = WTSEC_LIBRARY_Localization::lmsg('statuses.signature_found');
                        break;
                }
                break;
        }
        return ["icon" => $color["icon"], "color" => $color["color"], "text" => "<span class='" . $color["color"] . "'>" . $definition . "</span>"];
    }

    public static function getColor($type)
    {
        $types = [
            "green" => "is--status--ok",
            "red" => "is--status--error",
            "success" => "is--status--ok",
            "error" => "is--status--error",
            "orange" => "is--status--warning",
            "grey" => "ww--status_unknow"
        ];
        $icon_types = [
            "green" => "ww-icon--status_ok",
            "red" => "ww-icon--status_error",
            "success" => "ww-icon--status_ok",
            "error" => "ww-icon--status_error",
            "orange" => "ww-icon--status_warning",
            "grey" => "ww-icon--status_unknow"
        ];
        return ["icon" => $icon_types[$type], "color" => $types[$type]];
    }

}