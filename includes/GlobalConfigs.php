<?php 
/*
 YOU SHOULD NOT CHANGE ANYTHING IN THIS CONFIGURATION FILE!
 This file was automatically generated by the control panel on 07-02-04.
*/

/* WEBALIZER CONFIGURATION */
// This Webalizer configuration was last updated by the control panel on 07-05-04 by Jeremy
$WebalizerConf = "PageType,htm*\r\n";
$WebalizerConf .= "PageType,php*\r\n";
$WebalizerConf .= "DNSChildren,10\r\n";
$WebalizerConf .= "Quiet,yes\r\n";
$WebalizerConf .= "HideURL,*.gif\r\n";
$WebalizerConf .= "HideURL,*.GIF\r\n";
$WebalizerConf .= "HideURL,*.jpg\r\n";
$WebalizerConf .= "HideURL,*.JPG\r\n";
$WebalizerConf .= "HideURL,*.png\r\n";
$WebalizerConf .= "HideURL,*.PNG\r\n";
$WebalizerConf .= "HideURL,*.ra\r\n";
$WebalizerConf .= "SearchEngine,yahoo.com,p=\r\n";
$WebalizerConf .= "SearchEngine,altavista.com,q=\r\n";
$WebalizerConf .= "SearchEngine,google.com,q=\r\n";
$WebalizerConf .= "SearchEngine,eureka.com,q=\r\n";
$WebalizerConf .= "SearchEngine,lycos.com,query=\r\n";
$WebalizerConf .= "SearchEngine,hotbot.com,MT=\r\n";
$WebalizerConf .= "SearchEngine,msn.com,MT=\r\n";
$WebalizerConf .= "SearchEngine,infoseek.com,qt=\r\n";
$WebalizerConf .= "SearchEngine,webcrawler,searchText=\r\n";
$WebalizerConf .= "SearchEngine,excite,search=\r\n";
$WebalizerConf .= "SearchEngine,netscape.com,search=\r\n";
$WebalizerConf .= "SearchEngine,mamma.com,query=\r\n";
$WebalizerConf .= "SearchEngine,alltheweb.com,query=\r\n";
$WebalizerConf .= "SearchEngine,northernlight.com,qr=";
$WebalizerHome = "/etc/webalizer";
/* END WEBALIZER CONFIGURATION */
/* SITE CONFIGURATION */
// This site configuration was last updated by the control panel on 07-10-04 by Jeremy
$ThirdLevelDomains = "cyberdataengineering.com,gplhosting.org,mycorporatewebsite.com,pc-technics.com";
$ExternalWAN_IP = "24.233.183.35";
$SudoPassword = "YXBhY2hlX2wyMDk0OQ==";
$HomeServer = "gplhosting.org";
$CoLoServers = "192.168.2.2,192.168.2.3";
$UseSSL = "0";
/* END SITE CONFIGURATION */
/* APACHE 2.0 CONFIGURATION */
// This Apache 2.0 configuration was last updated by the control panel on 07-10-04 by Jeremy
$Apache2 = "/Apache/,/Apache/logs/,/usr/local/apache2/conf/httpd.conf,192.168.2.30,/etc/init.d/httpd graceful,/usr/sbin/rotatelogs,5M,1,1";
$IntApacheStatSpider = "4 hours";
$ApacheCustDirectives = "LogFormat \"%h %l %u %t \\\"%r\\\" %>s %b \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\"\" combined";
/* END APACHE 2.0 CONFIGURATION */
/* XMAIL CONFIGURATION */
// This XMail configuration was last updated by the control panel on 07-03-04 by Jeremy
$Xmail = "192.168.2.30,6017,0416010f030e0d04091,02150908040c090d0a16110c0b02555552,/var/MailRoot";
/* END XMAIL CONFIGURATION */
/* BIND CONFIGURATION */
// This BIND configuration was last updated by the control panel on 07-03-04 by Jeremy
$BIND = "/etc/named.conf,/var/named/,/usr/sbin/rndc";
/* END BIND CONFIGURATION */
/* MEMBER CONFIGURATION */
// These membership settings were last updated by the control panel on 07-03-04 by Jeremy
$MemberPlans = "Basic,10,1,10,5120,10,10,10,10,10,20971520,20971520,10.95\r\n";
$MemberPlans .= "Premium,15,1,15,10240,15,15,15,15,15,41943040,41943040,19.95\r\n";
$MemberPlans .= "Gold,20,1,20,10240,20,20,20,20,20,167772160,167772160,29.95\r\n";
$MemberPlans .= "Platinum,20,1,20,20480,20,20,20,20,20,838860800,838860800,39.95\r\n";
$MemberPlans .= "Site Admin,0,1,0,0,0,0,0,0,0,0,0,0\r\n";
$MemberPlans .= "Guest,5,1,5,2048,5,5,5,5,5,10485760,104857600,10.95,1\r\n";
$MemberPlans .= "GURU User,0,1,0,0,0,0,0,0,0,0,0,0.00";
/* END MEMBER CONFIGURATION */
?>