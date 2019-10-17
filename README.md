# Webtotem Security
**Webtotem Security** - is an extension for Plesk that prevents hacking and attacks on a website. All client domains of the Plesk account will be automatically added to Webtotem Security for analysis and will be marked as “Local”, and other sites not included in the Plesk account, but those included in the Webtotem Security account will be marked as “Global”. The extension uses hooks to display the extension for quick access in the sidebar and in the domains section.
To use the extension, you will need to register (then confirm the email) or log in with an account account in WebTotem Securiy. Only Plesk account e-mail is allowed.

**Website monitoring is carried out by special internal and external utilities such as:**

## Internal utilities: ##
> 1) **Antivirus** - check for (shell, virus, obfuscation) or file changes.
Installation process:
Automatic installation in one click to the root folder «httpdocs». If an autoloader is found in the “index.php” file in the “public” folder (usually used by PSR-4 standard frameworks), the “public” folder becomes the root directory and is installed in this directory.
Antivirus scans every file in the root directory. Shell and virus codes will be sent to the server for research.
> 2) **Firewall** - checking all requests coming from the client to the server. To prevent SQL injection, XSS or DDOS attacks.
Installation process:
Automatic installation and implementation of the module in “index.php” in one click into the root folder “httpdocs”. If an autoloader is found in the “index.php” file in the “public” folder (usually used by PSR-4 standard frameworks), the “public” folder becomes the root directory and is installed in this directory. When the Firewall is running, it collects all malicious requests into the log file, then the server downloads log files to itself every n minutes for examination.

**Internal utilities use the file system (pm_File_Manager) with user rights to work with files inside Plesk.
Antivirus and Firewall use the WebTotem Security API to generate the file of the services and transfer the contents of the generated file to the root directory with the generated name.**

## External utilities: ##

> 1) **Malicious scripts** - a utility in which customers will be assured of the absence of Trojans, viruses, malware and other malicious programs that harm visitors to the site. The WebTotem Security virus scanner will warn you when threats are detected.
> 2) **Deface scanner** - a utility in which customers will be immediately notified in case their site is threatened with a deface.
> 3) **SSL** is a utility in which customers will always be updated on SSL certificate updates thanks to our monitoring system.

> 4) **Port scanner** is a utility in which clients will know everything about the state of open TCP / UDP ports on the server hosting your site. We will inform you about the opening of new ports.
> 5) **Reputation** is a utility in which customers will know if their domain / IP is blacklisted (for example, Google, Safebrowsing).
> 6) **Accessibility** - a utility in which customers will be assured of the availability of the site around the world, every minute.
> 7) **Domain Monitoring** is a utility in which, customers will know about the expiration of their domain and cases of its hacking. They will be warned about updates and domain name changes.
> 8) **Security Update** - In the case of using the CMS platform on your website, we will report on current security issues, monitor the vulnerability databases and notify customers about the platform update with the new version.


**All external utilities work through the WebTotem Security API and do not have any collective functions on the part of Plesk.**

**Detailed Reports are sent in the form of statistics and a report in PDF, CSV or XML format.**