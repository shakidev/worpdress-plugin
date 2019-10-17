<?php

class WTOTEMSEC_LIBRARY_WebTotem
{
    const URL = "https://api.wtotem.com/v1/graphql";

    public static function auth($key)
    {
        $payload = '{"query":"mutation{\n  apiServiceMutation{\n    auth(apiKey:\"'.$key.'\"){\n      value\n      refreshToken\n      expiresIn\n    }\n  }\n}\n\n"}';
        return self::requestApi($payload);
    }


    public static function requestURL($url)
    {
        $args = [
            'timeout' => '15',
            'sslverify' => false,
        ];
        $response = wp_remote_post($url,$args);
        $httpcode = wp_remote_retrieve_response_code($response);
        if($httpcode < 200){
                WTOTEMSEC_LIBRARY_Session::setNotification("error",WTOTEMSEC_LIBRARY_Localization::lmsg('could_not_connect_to_the_server'));
        }
        $response = wp_remote_retrieve_body($response);
        return $response;
    }

    protected static function requestApi($payload, $token = false,$repeat = false)
    {
        if ($token) {
            $token = WTOTEMSEC_LIBRARY_App::getToken();
        }
        $args = [
            'body' => $payload,
            'timeout' => '15',
            'sslverify' => false,
            'headers' => ['Content-Type:application/json'],
        ];
        if (!is_null($token) && $token) {
            $authorization = "Bearer " . $token;
            $args['headers'] = array_merge($args['headers'],["Authorization" => $authorization]);
        }
        $response = wp_remote_post(self::URL,$args);
        $httpcode = wp_remote_retrieve_response_code($response);

        if($httpcode < 200){
                WTOTEMSEC_LIBRARY_Session::setNotification("error",WTOTEMSEC_LIBRARY_Localization::lmsg('could_not_connect_to_the_server'));
        }
        $response = wp_remote_retrieve_body($response);

        $result = json_decode($response, true);
        if (isset($result['errors'][0]['message'])) {
            $message = self::diffMesageForHuman($result['errors'][0]['message']);
            if (stripos($result['errors'][0]['message'], "token") !== false && !$repeat) {
                $result = WTOTEMSEC_LIBRARY_Webtotem::auth(wtotemsec_app()->get("api_key"));
                if (isset($result['data']['apiServiceMutation']['auth']['value'])) {
                    $token_ = $result['data']['apiServiceMutation']['auth']['value'];
                    WTOTEMSEC_LIBRARY_App::login($token_);
                    return self::requestApi($payload,$token,true);
                }else{
                WTOTEMSEC_LIBRARY_App::logout();
                }
            }else{
                if($message !== false){
                    WTOTEMSEC_LIBRARY_Session::setNotification("warning",$message);
                }
            }
        }
        return $result;
    }

    public static function diffMesageForHuman($message)
    {
        $definition = $message;
        $excepts = [
            "RESOURCE_NOT_FOUND","DUPLICATE_HOST","INVALID_CREDENTIALS","INVALID_API_KEY","Invalid token",
        ];
        if(in_array($message,$excepts)){
            return false;
        }
        switch ($message) {
            case 'HOSTS_LIMIT_EXCEEDED':
                $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('hosts_limit_exceed');
                break;
            case 'RESOURCE_NOT_FOUND':
                $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('resource_not_found');
                break;
            case 'Invalid token':
                $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('invalid_token');
                break;
            case 'USER_ALREADY_REGISTERED':
                $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('user_already_exist');
                break;
            case 'DUPLICATE_HOST':
                $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('duplicate_host');
                break;
            case 'Agent does not exist or has been already verified':
                $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('agent_does_not_exist_or_has_been_already_verified');
                break;
            case 'INVALID_DOMAIN_NAME':
                $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('invalid_domain_name');
                break;
        }
        return $definition;
    }

    public static function register($name, $email, $password)
    {
        $payload = '{"query":"mutation ($input: CreateUserInput!) {\n  createUser(input: $input)\n}\n","variables":{"input":{"email":"' . $email . '","name":"' . $name . '","password":"' . $password . '","source":"WORDPRESS","tariffCycle":"MONTHLY","tariffName":"FREE","timezone":1,"tz_name":"Asia/Almaty"}}}';
        return self::requestApi($payload, true);
    }

    public static function isSiteUrl($url){
            return WTOTEMSEC_LIBRARY_Idn::idn_to_utf8(WTOTEMSEC_SITE_URL) == $url;
    }


    public static function getOwnSite(){
        $payload = '{"query":"{\n  userHostsList{\n    id\n    hostname\n  }\n}"}';
        $result = self::requestApi($payload, true);
        if (isset($result['data']['userHostsList'])) {
            //mutator
            foreach ($result['data']['userHostsList'] as &$m){
                if(isset($m['hostname'])) {
                    $m['hostname'] = WTOTEMSEC_LIBRARY_Idn::idn_to_utf8($m['hostname']);
                    if(self::isSiteUrl($m['hostname'])){
                        return $result['data']['userHostsList'] = $m;
                        break;
                    }
                }
            }
            $add_site = self::addSite(WTOTEMSEC_SITE_URL);
            if(isset($add_site['errors'])){
                return $result['data']['userHostsList'] = [];
            }else{
                return self::getOwnSite();
            }
        }
        return [];
    }

    public static function getAllChecks($host_id){
        $from = time() - (60 * 60 * 24);
        $to = time();
        $from_waf = time() - (60 * 60 * 24 * 7);
        $payload = '{"query":"query getAllChecks( $hostId:Int!, $dateRange: DateRangeInput!, $dateRangeWaf:DateRangeInput! ){\n  userHost(id:$hostId){\n    id\n    title\n    hostname\n    stack\n    createdAt\n    services {\n      id\n      name\n      configs {\n        id\n      \tdata\n        isActive\n      }\n    }\n  }\n  waServiceChecks(userHostId:$hostId){\n    config{\n      id\n      isActive\n    }\n    isDown\n    status\n    average(dateRange: $dateRange)\n    responseTime(dateRange: $dateRange)\n    testsResults(dateRange: $dateRange)\n  }\n  sslServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    status\n    issued\n    expires\n    daysLeft\n    tls\n  }\n  decServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    email\n    daysLeft\n    created\n    expires\n    registrar\n    owner\n    status\n  }\n  avServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    lastTestId\n    lastTestTime\n    status\n    count\n    list\n  }\n  cmsServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    lastTestId\n    lastTestTime\n    status\n    count\n    list\n  }\n  dcServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    lastTestTime\n    status\n    count\n    list\n  }\n  psServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    ip\n    lastTestId\n    lastTestTime\n    status\n    count\n    openTCPs\n    openUDPs\n  }\n  wafServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    lastTestTime\n    status\n    count\n    countIP       \n    chart(dateRangeWaf:$dateRangeWaf){\n      date\n      count\n    }\n  }\n  vcServiceChecks(userHostId:$hostId){\n        config{\n      id\n      isActive\n    }\n    status\n    list\n    fileChangesCount\n    errorsCount\n    signaturesCount\n  }\n  \n}\n\n\n\n","variables":{"hostId":'.$host_id.',"dateRange":{"from":'.$from.',"to":'.$to.'},"dateRangeWaf":{"to":'.$to.',"from":'.$from_waf.'}}}';
        return self::requestApi($payload, true);
    }

    public static function getSites()
    {
        $payload = '{"query":"query ($first: Int, $after: Int, $filter: String, $category: Int) {\n        userHostsList(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          category: $category\n        ) {\n          id\n          title\n          hostname\n          stack\n          createdAt\n          services {\n            id\n            name\n            configs {\n              id\n              data\n              isActive\n            }\n          }\n        }\n        userHostsListCount(filter: $filter, category: $category)\n      }","variables":{"first":100,"after":0,"filter":""}}';
        $result = self::requestApi($payload, true);
        if (isset($result['data']['userHostsList'])) {
            //mutator
            foreach ($result['data']['userHostsList'] as &$m){
                if(isset($m['hostname'])) {
                    $m['hostname'] = WTOTEMSEC_LIBRARY_Idn::idn_to_utf8($m['hostname']);
                    if(self::isSiteUrl($m['hostname'])){
                        return $result['data']['userHostsList'] = [$m];
                        break;
                    }
                }
            }
            $add_site = self::addSite(WTOTEMSEC_SITE_URL);
            if(isset($add_site['errors'])){
            return $result['data']['userHostsList'] = [];
            }else{
                return self::getSites();
            }
        }
        return [];
    }

    public static function getService($service)
    {
        $payload = '';
        switch ($service) {
            case 'wa':
                $yesterday = time() - (60 * 60 * 24);
                $now = time();
                $payload = '{"query":"query (\n        $first: Int,\n        $after: Int,\n        $dateRange: DateRangeInput!,\n        $filter: String,\n        $userHostId: Int,\n        $status: WaModuleStatus,\n        $category: Int\n      ) {\n        waServiceChecks(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          userHostId: $userHostId,\n          withStatus: $status,\n          category: $category\n        ) {\n          config {\n            service {\n              id\n            }\n            id\n            isActive\n            userhost {\n              id\n              title\n              hostname\n              isActive\n              tags\n            }\n            data\n          }\n          isDown\n          status\n          average(dateRange: $dateRange)\n          responseTime(dateRange: $dateRange)\n          testsResults(dateRange: $dateRange)\n        }\n      }","variables":{"first":100,"after":0,"filter":"","dateRange":{"from":' . $yesterday . ',"to":' . $now . '}}}';
                break;
            case 'ssl':
                $payload = '{"query":"query (\n        $first: Int,\n        $after: Int,\n        $filter: String,\n        $userHostId: Int,\n        $status: SslModuleStatus,\n        $category: Int\n      ) {\n        sslServiceChecks(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          userHostId: $userHostId,\n          withStatus: $status,\n          category: $category\n        ) {\n          config {\n            service {\n              id\n            }\n            id\n            isActive\n            userhost {\n              id\n              title\n              hostname\n            }\n            data\n          }\n          status\n          issued\n          expires\n          daysLeft\n          tls\n        }\n      }","variables":{"first":100,"after":0,"filter":""}}';
                break;
            case 'dec':
                $payload = '{"query":"query (\n          $first: Int,\n          $after: Int,\n          $filter: String,\n          $userHostId: Int,\n          $status: DecModuleStatus,\n          $category: Int\n        ) {\n        decServiceChecks(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          userHostId: $userHostId,\n          withStatus: $status,\n          category: $category\n        ) {\n          config {\n            service {\n              id\n            }\n            id\n            isActive\n            userhost {\n              id\n              title\n              hostname\n            }\n            data\n          }\n          email\n          daysLeft\n          created\n          expires\n          registrar\n          owner\n          status\n        }\n      }","variables":{"first":100,"after":0,"filter":""}}';
                break;
            case 'av':
                $payload = '{"query":"query (\n        $first: Int,\n        $after: Int,\n        $filter: String,\n        $userHostId: Int,\n        $status: AvModuleStatus,\n        $category: Int\n      ) {\n        avServiceChecks(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          userHostId: $userHostId,\n          withStatus: $status,\n          category: $category\n        ) {\n          config {\n            service {\n              id\n            }\n            id\n            isActive\n            userhost {\n              id\n              title\n              hostname\n            }\n            data\n          }\n          lastTestId\n          lastTestTime\n          status\n          count\n          list\n        }\n      }","variables":{"first":100,"after":0,"filter":""}}';
                break;
            case 'cms':
                $payload = '{"query":"query (\n        $first: Int,\n        $after: Int,\n        $filter: String,\n        $userHostId: Int,\n        $status: CmsModuleStatus,\n        $category: Int\n      ) {\n        cmsServiceChecks(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          userHostId: $userHostId,\n          withStatus: $status,\n          category: $category\n        ) {\n          config {\n            service {\n              id\n            }\n            id\n            isActive\n            userhost {\n              id\n              title\n              hostname\n            }\n            data\n          }\n          lastTestId\n          lastTestTime\n          status\n          count\n          list\n        }\n      }","variables":{"first":100,"after":0,"filter":""}}';
                break;
            case 'dc':
                $payload = '{"query":"query (\n        $first: Int,\n        $after: Int,\n        $filter: String,\n        $userHostId: Int,\n        $status: DcModuleStatus,\n        $category: Int\n      ) {\n        dcServiceChecks(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          userHostId: $userHostId,\n          withStatus: $status,\n          category: $category\n        ) {\n          config {\n            service {\n              id\n            }\n            id\n            isActive\n            userhost {\n              id\n              title\n              hostname\n            }\n            data\n          }\n          lastTestTime\n          status\n          count\n          list\n        }\n      }","variables":{"first":100,"after":0,"filter":""}}';
                break;
            case 'ps':
                $payload = '{"query":"query (\n        $first: Int,\n        $after: Int,\n        $filter: String,\n        $userHostId: Int,\n        $status: PsModuleStatus,\n        $category: Int\n      ) {\n        psServiceChecks(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          userHostId: $userHostId,\n          withStatus: $status,\n          category: $category\n        ) {\n          config {\n            service {\n              id\n            }\n            id\n            isActive\n            userhost {\n              id\n              title\n              hostname\n            }\n            data\n          }\n          ip\n          lastTestId\n          lastTestTime\n          status\n          count\n          openTCPs\n          openUDPs\n        }\n      }","variables":{"first":100,"after":0,"filter":""}}';
                break;
            case 'waf':
                $payload = '{"query":"query (\n        $first: Int,\n        $after: Int,\n        $filter: String,\n        $userHostId: Int,\n        $status: WafModuleStatus,\n        $category: Int\n      ) {\n        wafServiceChecks(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          userHostId: $userHostId,\n          withStatus: $status,\n          category: $category\n        ) {\n          config {\n            service {\n              id\n            }\n            id\n            isActive\n            userhost {\n              id\n              title\n              hostname\n            }\n            data\n          }\n          lastTestTime\n          status\n          count\n        }\n        wafServiceChecksCount(\n          filter: $filter\n          withStatus: $status\n          category: $category\n        )\n      }","variables":{"first":100,"after":0,"filter":""}}';
                break;
            case 'vc':
                $payload = '{"query":"query (\n        $first: Int,\n        $after: Int,\n        $filter: String,\n        $userHostId: Int,\n        $status: VcModuleStatus,\n        $category: Int\n      ) {\n        vcServiceChecks(\n          first: $first,\n          after: $after,\n          filter: $filter,\n          userHostId: $userHostId,\n          withStatus: $status,\n          category: $category\n        ) {\n          config {\n            service {\n              id\n            }\n            id\n            isActive\n            userhost {\n              id\n              title\n              hostname\n            }\n            data\n          }\n          status\n    list\n       fileChangesCount\n          errorsCount\n          signaturesCount\n        }\n      }","variables":{"first":100,"after":0,"filter":""}}';
                break;
        }
        $result = self::requestApi($payload, true);
        if (isset($result['data'][$service . 'ServiceChecks'])) {
            //mutator
            foreach ($result['data'][$service . 'ServiceChecks'] as &$m){
                if(isset($m['config']['userhost']['hostname'])) {
                    $m['config']['userhost']['hostname'] = WTOTEMSEC_LIBRARY_Idn::idn_to_utf8($m['config']['userhost']['hostname']);
                    if(self::isSiteUrl($m['config']['userhost']['hostname'])){
                        return $result['data'][$service . 'ServiceChecks'] = [$m];
                        break;
                    }
                }
            }
            $add_site = self::addSite(WTOTEMSEC_SITE_URL);
            if(isset($add_site['errors'])){
                return $result['data'][$service . 'ServiceChecks'] = [];
            }else{
                return self::getService($service);
            }
        }
        return [];
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

    public static function addSite($url){
        $payload = '{"query":"\n        mutation($input: AddUserHostInput!) {\n          addUserHost(input: $input) {\n            id\n            title\n            hostname\n            tags\n            stack\n            services {\n              id\n              name\n              configs {\n                id\n                data\n                isActive\n                createdAt\n              }\n            }\n            isActive\n            createdAt\n          }\n        }\n      ","variables":{"input":{"hostname":"'.$url.'","services":[{"id":1,"configs":[{"scheme":"http","port":80,"check_interval":1,"responsetime_threshold":30,"path":"/","http_errors":[400,401,402,403,404,500,501,502,503],"alert_after":0}]},{"id":2,"configs":[{"check_interval":5,"notify_expiry_day":true,"notify_expiry_month":true,"notify_expiry_week":true,"port":443}]},{"id":4,"configs":[{"check_interval":5,"path":"/","scheme":"http","port":80}]},{"id":5,"configs":[{"check_interval":5,"path":"/","scheme":"http","port":80}]},{"id":6,"configs":[{"check_interval":5,"path":"/","scheme":"http","port":80}]},{"id":7,"configs":[{"check_interval":5,"ports_udp":[18,19,53,27,29,31,71,74],"ports_tcp":[21,22,25,3306,5432,80,443,88,8000,8080]}]},{"id":9,"configs":[{"scheme":"http","port":80,"path":"/"}]},{"id":8,"configs":[{"check_interval":30,"scheme":"http","port":80,"path":"/"}]},{"id":3,"configs":[{"check_interval":60,"notify_expiry_day":true,"notify_expiry_month":true,"notify_expiry_week":true}]},{"id":10,"configs":[{"check_interval":30,"path":"/","scheme":"http","port":80}]}],"title":"'.$url.'"}}}';
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

    public function getLog($id){
        $payload = '{"query":"{\n        vcServiceChecks(userHostId: '.$id.') {\n          list\n          fileChangesCount\n          errorsCount\n          signaturesCount\n          status\n        }\n      }"}';
        return self::requestApi($payload, true);
    }


    public static function getOptions($host_id){
        $payload = '{"query":"query{\nuserHost(id:'.$host_id.'){\n    id\n    title\n    hostname\n    stack\n    createdAt\n    services {\n      id\n      name\n      configs {\n        id\n      \tdata\n        isActive\n      }\n    }\n  }\n}"}';
        return self::requestApi($payload, true);
    }

    public static function getAntivirus($host_id){
        $payload = '{"query":"query{\n  vcServiceChecks(userHostId:'.$host_id.'){\n    config{\n      id,\n      isActive\n    }\n    status\n    list\n    fileChangesCount\n    errorsCount\n    signaturesCount\n  }\n}\n\n"}';
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
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor("grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor( "success");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor( "error");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.unavailable');
                        break;
                }
                break;
            case 'ssl':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor( "green");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.invalid');
                        break;
                    case '2':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.expired');
                        break;
                    case '3':
                        $color = self::getColor( "orange");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.expires');
                        break;
                    case '4':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.expires_today');
                        break;
                }
                break;
            case 'dec':
                switch ((string)$status) {
                    case '-3':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.not_registered');
                        break;
                    case '-2':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.unsupported');
                        break;
                    case '-1':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor( "green");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor( "orange");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.expires');
                        break;
                    case '2':
                        $color = self::getColor( "orange");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.expired');
                        break;
                    case '3':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.expires_today');
                        break;
                }
                break;
            case 'av':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor( "green");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.blacklisted');
                        break;
                }
                break;
            case 'cms':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor( "green");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.miner_detected');
                        break;
                }
                break;
            case 'dc':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor( "green");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.deface');
                        break;
                    case '2':
                        $color = self::getColor( "orange");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.modified');
                        break;
                }
                break;
            case 'ps':
                switch ((string)$status) {
                    case '-1':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor( "green");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor( "orange");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.open');
                        break;
                }
                break;
            case 'waf':
                switch ((string)$status) {
                    case '-400':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.blocked');
                        break;
                    case '-300':
                        $color = self::getColor( "orange");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.not_installed');
                        break;
                    case '-1':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor( "green");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.attacks_detected');
                        break;
                }
                break;
            case 'vc':
                switch ((string)$status) {
                    case '-400':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.blocked');
                        break;
                    case '-300':
                        $color = self::getColor( "orange");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.not_installed');
                        break;
                    case '-1':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.error');
                        break;
                    case '-200':
                        $color = self::getColor( "grey");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.pending');
                        break;
                    case '0':
                        $color = self::getColor( "green");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.ok');
                        break;
                    case '1':
                        $color = self::getColor( "orange");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.file_changes');
                        break;
                    case '2':
                        $color = self::getColor( "red");
                        $definition = WTOTEMSEC_LIBRARY_Localization::lmsg('statuses.signature_found');
                        break;
                }
                break;
        }
        return ["icon" => $color["icon"],"color" => $color["color"], "text" => "<span class='".$color["color"]."'>".$definition."</span>"];
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

    public static function getStatusStartPauseIcon($status, $config_id, $host_id)
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
//        $url = esc_url(admin_url('admin-post.php'));
//        $form = '<form action="'.$url.'" method="post">
//        <input type="hidden" name="action" value="change_status">
//        <input type="hidden" name="config_id" value="'.$config_id.'">
//        <input type="hidden" name="host_id" value="'.$host_id.'">
//        '.$icon.'
//        </form>';
        return $icon;
    }

    public function addDomain($domain)
    {
        $payload = '{"query":"\n        mutation($input: AddUserHostInput!) {\n          addUserHost(input: $input) {\n            id\n            title\n            hostname\n            tags\n            stack\n            services {\n              id\n              name\n              configs {\n                id\n                data\n                isActive\n                createdAt\n              }\n            }\n            isActive\n            createdAt\n          }\n        }\n      ","variables":{"input":{"hostname":"' . $domain . '","services":[{"id":1,"configs":[{"scheme":"http","port":80,"check_interval":1,"responsetime_threshold":30,"path":"/","http_errors":[400,401,402,403,404,500,501,502,503],"alert_after":0}]},{"id":2,"configs":[{"check_interval":5,"notify_expiry_day":true,"notify_expiry_month":true,"notify_expiry_week":true,"port":443}]},{"id":4,"configs":[{"check_interval":5,"path":"/","scheme":"http","port":80}]},{"id":5,"configs":[{"check_interval":5,"path":"/","scheme":"http","port":80}]},{"id":6,"configs":[{"check_interval":5,"path":"/","scheme":"http","port":80}]},{"id":7,"configs":[{"check_interval":5,"ports_udp":[18,19,53,27,29,31,71,74],"ports_tcp":[21,22,25,3306,5432,80,443,88,8000,8080]}]},{"id":9,"configs":[{"scheme":"http","port":80,"path":"/"}]},{"id":8,"configs":[{"check_interval":30,"scheme":"http","port":80,"path":"/"}]},{"id":3,"configs":[{"check_interval":60,"notify_expiry_day":true,"notify_expiry_month":true,"notify_expiry_week":true}]},{"id":10,"configs":[{"check_interval":30,"path":"/","scheme":"http","port":80}]}],"title":"' . $domain . '"}}}';
        return self::requestApi($payload, true);
    }

    public static function diffService($service){
        $services = [
            "wa" => WTOTEMSEC_LIBRARY_Localization::lmsg('availability'),
            "ssl" => WTOTEMSEC_LIBRARY_Localization::lmsg('ssl'),
            "av" => WTOTEMSEC_LIBRARY_Localization::lmsg('reputation'),
            "cms" => WTOTEMSEC_LIBRARY_Localization::lmsg('malicious_scripts'),
            "dc" => WTOTEMSEC_LIBRARY_Localization::lmsg('deface_scanner'),
            "ps" => WTOTEMSEC_LIBRARY_Localization::lmsg('port_scanner'),
            "waf" => WTOTEMSEC_LIBRARY_Localization::lmsg('firewall'),
            "vc" => WTOTEMSEC_LIBRARY_Localization::lmsg('remote_antivirus'),
            "dec" => WTOTEMSEC_LIBRARY_Localization::lmsg('domain'),
        ];
        return $services[$service];
    }

    public static function statusBar($services)
    {
        if(empty($services)){
            return '';
        }
        $html = '';
        $icons = [];
        foreach ($services as $service) {
            $icon = '';
            switch ($service['name']) {
                case 'wa':
                    $icon = '<span style="padding-right:10px" class="v-tooltip v-tooltip--top tooltipped" data-position="top"><span><i aria-hidden="true" class="v-icon icon pl-2 pr-2 mdi mdi-clock theme--light"></i></span></span>';
                    break;
                case 'ssl':
                    $icon = '<span style="padding-right:10px" class="v-tooltip v-tooltip--top tooltipped" data-position="top"><span><i aria-hidden="true" class="v-icon icon pl-2 pr-2 mdi mdi-certificate theme--light"></i></span></span>';
                    break;
                case 'dec':
                    $icon = '<span style="padding-right:10px" class="v-tooltip v-tooltip--top tooltipped" data-position="top"><span><i aria-hidden="true" class="v-icon icon pl-2 pr-2 mdi mdi-web theme--light"></i></span></span>';
                    break;
                case 'av':
                    $icon = '<span style="padding-right:10px" class="v-tooltip v-tooltip--top tooltipped" data-position="top"><span><i aria-hidden="true" class="v-icon icon pl-2 pr-2 mdi mdi-star-half theme--light"></i></span></span>';
                    break;
                case 'cms':
                    $icon = '<span style="padding-right:10px" class="v-tooltip v-tooltip--top tooltipped" data-position="top"><span><i aria-hidden="true" class="v-icon icon pl-2 pr-2 mdi mdi-ghost theme--light"></i></span></span>';
                    break;
                case 'dc':
                    $icon = '<span style="padding-right:10px" class="v-tooltip v-tooltip--top tooltipped" data-position="top"><span><i aria-hidden="true" class="v-icon icon pl-2 pr-2 mdi mdi-guy-fawkes-mask theme--light"></i></span></span>';
                    break;
                case 'ps':
                    $icon = '<span style="padding-right:10px" class="v-tooltip v-tooltip--top tooltipped" data-position="top"><span><i aria-hidden="true" class="v-icon icon pl-2 pr-2 mdi mdi-ethernet theme--light"></i></span></span>';
                    break;
                case 'waf':
                    $icon = '<span style="padding-right:10px" class="v-tooltip v-tooltip--top tooltipped" data-position="top"><span><i aria-hidden="true" class="v-icon icon pl-2 pr-2 mdi mdi-wall theme--light"></i></span></span>';
                    break;
                case 'vc':
                    $icon = '<span style="padding-right:10px" class="v-tooltip v-tooltip--top tooltipped" data-position="top"><span><i aria-hidden="true" class="v-icon icon pl-2 pr-2 mdi mdi-server-security theme--light"></i></span></span>';
                    break;
            }
            if (strlen($icon) > 0) {
                $replace = '';
                $status = '';
                $place = 'class="';
                $place2 = 'tooltipped"';
                if (isset($service['configs'][0]['isActive'])) {
                    if ((boolean)$service['configs'][0]['isActive']) {
                        $status = WTOTEMSEC_LIBRARY_Localization::lmsg('active');
                        $replace = 'primary--text ';
                    } else {
                        $status = WTOTEMSEC_LIBRARY_Localization::lmsg('inactive');
                        $replace = 'amber--text ';
                    }
                } else {
                    $status = WTOTEMSEC_LIBRARY_Localization::lmsg('unavailable');
                    $replace = 'v-icon--disabled ';
                }
                $replace2 = 'tooltipped" data-tooltip="'.self::diffService($service['name']).' / '.$status.'"';
                $icon = str_replace([$place,$place2], [$place . $replace,$replace2], $icon);
            }
            $icons[$service['name']] = $icon;
        }
        $lists = ["wa","ssl","dec","av","cms","dc","ps","waf","vc"];
        $sort = [];
        foreach ($lists as $list){
            $sort[] = $icons[$list];
        }
        $html .= implode("",$sort);
        $html .= '';
        return $html;
    }

    public static function activate($code)
    {
        $payload = '{"query":"mutation {\n  pleskMutation {\n    confirmFree(code:\"'.$code.'\")\n  }\n}\n"}';
        return self::requestApi($payload);
    }


}