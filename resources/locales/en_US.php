<?php defined('ABSPATH') or die("Protected By WT!");

$messages = [
    'welcome' => 'Welcome %%name%%!',
    'sites' => 'Sites',
    'site' => 'Site',
    'availability' => 'Availability',
    'ssl' => 'SSL',
    'domain' => 'Domain',
    'reputation' => 'Reputation',
    'malicious_scripts' => 'Malicious script',
    'deface_scanner' => 'Deface scanner',
    'port_scanner' => 'Port scanner',
    'firewall' => 'Firewall',
    'remote_antivirus' => 'Antivirus',
    'local_antivirus' => 'Local Antivirus',
    'settings' => 'Settings',
    'site_name' => 'Site name',
    'site_address' => 'Site address',
    'date_added' => 'Date added',
    'hostname' => 'Hostname',
    'owner' => 'Owner',
    'services' => 'Services',
    'actions' => 'Actions',
    'pause' => 'Pause',
    'title' => 'Title',
    'location' => 'Location',
    'local' => 'Local',
    'global' => 'Global',
    'response_time' => 'Response time',
    'downtime' => 'Downtime',
    'chart' => 'Chart',
    'information' => 'Information',
    'status' => 'Status',
    'days_left' => 'Days left',
    'issue_date' => 'Issue date',
    'expiry_date' => 'Expiry date',
    'tls' => 'TLS',
    'tcp' => 'TCP',
    'udp' => 'UDP',
    'email' => 'Email',
    'created' => 'Created',
    'registrar' => 'Registrar',
    'time_of_the_last_test' => 'Time of the last test',
    'blacklists_entries' => 'Blacklists entries',
    'detected_keywords' => 'Detected keywords',
    'ip' => 'IP',
    'number' => 'Number',
    'time_of_the_last_check' => 'Time of the last check',
    'signatures' => 'Signatures',
    'changes' => 'Changes',
    'instruction' => 'Instruction',
    'sec' => 'sec.',
    'ms' => 'ms.',
    'statuses' => [
        'invalid' => 'Invalid',
        'ok' => 'Everything is OK',
        'expired' => 'Expired',
        'expires' => 'Expires',
        'expires_today' => 'Expires today',
        'missing' => 'Missing',
        'error' => 'Error',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'available' => 'Available',
        'unavailable' => 'Unavailable',
        //
        'not_registered' => 'Not registered',
        'unsupported' => 'Unsupported',
        'clean' => 'Clean',
        'clear' => 'Clear',
        'blacklisted' => 'Infected',
        'miner_detected' => 'Infected',
        'deface' => 'Deface',
        'modified' => 'Modified',
        'detected' => 'Detected',
        'open' => 'Open',
        'blocked' => 'Blocked',
        'not_installed' => 'Not installed',
        'connected' => 'Connected',
        'attacks_detected' => 'Attacks detected',
        'signature_found' => 'Signature found',
        'file_changes' => 'File changes',
    ],
    'form' => [
        'password' => 'Password',
        'name' => 'Name',
        'email' => 'Email',
        'your_name' => 'Your name',
        'your_password' => 'Your password',
        'your_email' => 'Your email',
        'you_are_successfully_logged_in' => 'You are successfully logged in %%name%%.',
        'data_saved_successfully_confirm_your_email' => 'Data saved successfully. Confirm your e-mail.',
        'incorrect' => 'The entered Api Key is invalid',
    ],
    'logout' => 'Log out',
    'sign_in' => 'Log In',
    'sign_up' => 'Register',
    'create_account' => 'Create an account',
    'save' => 'Save',
    'notify_about_infection_by_email' => 'Notify about infection by email',
    'remove' => 'Remove',
    'uninstall' => 'Uninstall',
    'connect' => 'Connect',
    'run' => 'Activate',
    'stop' => 'Deactivate',
    'install' => 'Install',
    'unable_to_connect_to_service' => 'Unable to connect to %%service%% service by the url: %%site%%',
    'module_name' => 'Module %%name%%',
    'vc_description' => 'Secure your website from being hacked by regularly scanning the source code for "shells" and "backdoors" injected by attackers. Our Vulnerability Scanner tool has plenty of signatures and smartly detects potentially vulnerable code. It will also notify you of unsanctioned code changes.',
    'waf_description' => 'Prevent attackers from hacking your website. When a hacker attempts to exploit possible vulnerability on your website our WAF tool recognizes this suspicious behaviour and immediately blacklists his ip making it harder to proceed.',
    'data_saved_successfully' => 'Data saved successfully.',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'pending' => 'Pending',
    'available' => 'Available',
    'unavailable' => 'Unavailable',
    'file_not_found' => 'The %%service%% service file not found',
    'hosts_limit_exceed' => 'Limit of adding sites exceeded.',
    'invalid_token' => 'Token expired.',
    'agent_does_not_exist_or_has_been_already_verified' => 'Agent does not exist or has been already verified.',
    'resource_not_found' => 'File not found.',
    'filename_not_found' => 'File %%file%% not found.',
    'back' => 'Go back',
    'password_min_characters' => 'The password must contain at least %%number%% characters.',
    'user_already_exist' => 'A user with this email already exists.',
    'assistant' => [
        'service_install_button' => 'In order to use the %%service%% module, you need to click on the "Install" button.'
    ],
    'duplicate_host' => 'Duplicate host',
    'position' => 'Position',
    'signature' => 'Signature',
    'type' => 'Type',
    'scan' => 'Scan',
    'finished' => 'Finished',
    'cancel' => 'Cancel',
    'result' => 'Result',
    'infected' => 'Infected',
    'infected_number_files' => 'Infected %%number%% files',
    'checked_number_files' => 'Checked %%number%% files',
    'details' => 'Details',
    'undo' => 'Undo',
    'permission_denied' => 'Permission Denied',
    'antivirus' => [
        'signature' => "Signature name",
        'path' => "Path",
        'vulnerabilities' => "Vulnerabilities",
        'offset' => "Offset",
        'code' => "Code",
        'status' => [
            "critical" => "Critical",
            "warning" => "Warning",
        ],
    ],
    'invalid_domain_name' => 'Invalid Domain Name',
    'include_instruction' => 'Next, you need to connect the file of the module to the sites input script (usually index.php), by adding the following code to the beginning of the input file (index.php): <?php include "%%file%%"; ?>',
    'add' => 'Add',
    'add_site' => 'Add Site',
    'activation_code' => 'Activation code',
    'activate' => 'Activate',
    'successfully_activated' => 'Your plugin is successfully activated',
    'invalid_verify_code' => 'Invalid verify code',
    'page_reload' => 'Page reloading after',
    'chmod_dir' => 'To install the module, you need permissions 777 for the folder "<span style="color:red">%%path%%</span>"',
    'could_not_connect_to_the_server' => 'Could not connect to the server.',
    'could_not_install_file' => 'Unable to install the %%service%% file in the %%directory%%',
    'could_not_uninstall_file' => 'Unable to uninstall the %%service%% file in the %%directory%%',
    'wtotem_account' => 'WT Account:',
    'forgot_your_password' => 'Forgot your password?',
    'description' => 'Description:',
    'descriptions' => [
        'firewall' => 'Activate our real-time protection against intrusion and attacks to your site thanks to the WT.Firewall. It will establish a barrier for automated attacks and spam bots, proactively checking incoming traffic and blocking requests of unwanted nature.',
        'antivirus' => 'Keep track of changes in the files of your site in real time. Anti-Virus will instantly notify you about new, modified and deleted files without your knowledge, as a rule, indicating the penetration of malicious code.',
        'malicious' => 'Scan the site for malicious code inserts that redirect visitors coming from search engines, redirect visitors from mobile browsers, using users power for mining cryptocurrency, using user traffic to download unsolicited ads, usually of a destructive nature.',
        'availability' => 'Check the performance of your site every minute. Stay informed about the problems of accessibility of the site instantly using any available types of notifications: SMS, Email, Telegram, Slack and many others.',
        'domain' => 'Learn about the expiration of your domain. We will send you a notice a day, a week and a month before the expiration date. Keep track of the reputation of the domain in the anti-virus databases and the entry into the black lists of popular browsers.',
        'deface' => 'Check sites for problems and signs of hacking, in which the main page is replaced with another, usually containing advertisements, threats or callers. Sites that have been defaced, as well as system errors displayed on the page, are not credible to visitors.',
        'port' => 'Monitor the status of network ports on the servers of your sites. Port Scanner will promptly report on popularly vulnerable services that are launched and opened for exploitation by attackers.',
        'ssl' => 'Find out the status of the SSL certificate installed on the site. We examine it for evidence of health, relevance and compliance with safety standards.',
        'reputation' => 'Keep track of the reputation of the domain in the anti-virus databases and in the list of browsers.',
    ],
    'options' => 'Options',
    'services_page' => [
        "site_virus_treatment" => "Site virus treatment",
        "site_virus_treatment_desc" => "Site recovery procedure, which includes: search and treatment of infected files and virus inserts; search and remove shells and backdoors; search and neutralization of vulnerable code; search for hidden links in files; checking the site for blacklisting of anti-virus databases and recommendations for removing it from the list; dump of the completely cured and workable site’s database and files.",
        "load_testing_of_the_web_resourcers" => "Load testing of the web resourcers",
        "load_testing_of_the_web_resourcers_desc" => "Checking the reliability, efficiency and performance of firewall systems under load testing. Collection of indicators, determination of performance and response time of the software and hardware system or device in response to an external request in order to establish compliance with the requirements for this system.",
        "incident_investigation" => "Incident Investigation",
        "incident_investigation_desc" => "The procedure for restoring the progress of the incident, which includes: localization and liquidation of the consequences of the incident; identification of members of the incident; analysis of incidents and taking actions to prevent such incidents. Also, the customer will be provided with a detailed report on the incident investigation.",
        "hardering" => "Hardering",
        "hardering_desc" => "Hardening is the process of strengthening the security of the system in order to reduce the risks of possible threats. This process is applied to all components of the system, thus, ideally, making the server an impregnable fortress. The service includes: all procedures of \"manual\" treatment; security configuration and upgrade of CMS modules; configuring secure configuration of the web server; setting the site's file system security rights.",
        "penetration_testing" => "Penetration testing",
        "penetration_testing_desc" => "Upon penetration testing, a list of vulnerabilities that an attacker can exploit is compiled. Also, a detailed report and recommendations on how to eliminate the identified vulnerabilities are provided.",
    ],
    "learn_more" => "Learn More",
    "get_pro_service" => "Get a professional security service",
    "read_more" => "READ MORE",
    "get_a_pro" => "GET A PRO",
    "get_a_pro_desc" => "Our team of security experts will clean the infection and remove malicious content. Once your site is restored we will provide a detailed report of our findings.",
    "get_a_pro_ask" => "Need help with a hacked website?",
    "support" => "Support",
    "web_version" => "Web version",
    "viruses_not_found" => "Viruses not found",
    "results_found" => "Results found",
    "detected_signatures" => "Detected signatures",
    "attacks_blocked_weekly" => "Attacks blocked weekly",
    "last_test" => "Last test",
    "stats" => "Stats",
    "attacks_blocked" => "Attacks blocked",
    "enter_api_key" => "Please, enter your API KEY to use it.",
    "activation" => "WT Activation",
    "activation_desc" => "You can receive the keys in the mail or in your personal account",
    "auth_desc" => "To display WT page, please authorize in WT",
    "send" => "Send",
    "connection" => "WT connection",
    "activate_your_plugin" => "Activate your plugin",
    "faq" => [
        "faq_1_question" => "1. What do these widgets mean?",
        "faq_1_answer" => "<div>Widgets from the left side are the modules available for security and monitoring</div>",
        "faq_2_question" => "2. Is it free? / Why is it free?",
        "faq_2_answer" => "<div>WT is absolutely free service for individual website owners up to 10 websites. We prevent website infection epidemic and provide basic needed security support.
        Up to 10 websites FREE</div>",
        "faq_3_question" => "3. How WT is different from other plugins?",
        "faq_3_answer" => "<div>Using 8 modules for external and internal monitoring, just in two clicks  you can check your website vulnerability for Firewall requests and executable Antivirus files.</div>",
        "faq_4_question" => "4. Your website is under attack or hacked?",
        "faq_4_answer" => "<div>a) Go to your personal account and write in support. Describe in detail the situation (website, date and time of the incident)</div><div>b) <a href=\"https://wtotem.com/cabinet/\" target='_blank'>Go to your personal account</a> to  the One-time services section. Select the desired service, describing the incident and order the paid service.</div>",
        "faq_5_question" => "5. Your website is blacklisted?",
        "faq_5_answer" => "<div><a href=\"https://wtotem.com/cabinet/\" target='_blank'>Go to your personal account</a> to  the One-time services section. Select the desired service, describing the incident and order the paid service.</div>",
        "faq_6_question" => "6. How to uninstall this plugin?",
        "faq_6_answer" => "<div>Go to the Plugin section of the menu WORDPRESS. Click <b>\"deactivate\"</b> and <b>\"delete\"</b>.</div>",
        "faq_7_question" => "7. Contact us",
        "faq_7_answer" => "<div>You can reach us through the web-chat in the user panel in the right bottom corner</div>",
    ],
    "file" => "File",
    "file_changed" => "File has been modified",
];
