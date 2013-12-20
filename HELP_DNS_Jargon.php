<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<style type="text/css">
<!--
.style2 {font-weight: bold}
.style3 {color: #FF0000}
-->
</style>
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699">
<table width=780 border=0 cellpadding=0 cellspacing=0 height="383" bgcolor="#FFFFFF">
<?php include("header.html"); ?>
  <tr> 
    <td colspan=3 background="images/links.gif"> 
     <?php include("navigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan=3 height="233"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr> 
          <td height="212"><div align="center">
            <p class="BodyHeader">&nbsp;</p>
            <p class="BodyHeader">Help :: DNS Jargon &amp; Acronyms</p>
            <p class="menu">&nbsp;</p>
            <p align="center"><span class="menu">DNS Record Definitions DNS terminology can be very confusing and hard to catch on to at first. After all , there are quite a few records that one can stumble upon when getting into DNS configurations. We have taken the liberty of discussing a few of them here today for a reference for you. If you have any questions about a term or record that we may have left out here , please feel free to contact us and let us know. We will be sure to respond with an answer and update this page. 
            </span>
            <p align="center" class="menu style3">Common Records </p>
            <span class="highlight"><em>A-Records (Host address)            </em></span>
            <p align="center" class="menu">The A-record is the most basic and the most important DNS record. They are used to translate domain names such as "www.pc-technics.com"&nbsp; into IP addresses such as 1.2.3.4 A-records are the DNS server equivalent of the hosts file - a simple domain name to IP-address mapping. A-records are not required for all computers, but is needed for any computer that shares resources on a network. This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1035.txt" target="_blank">RFC1035. </a></p>
            <p align="center" class="highlight"><em>CNAME-Records (Canonical name for an alias) </em></p>
            <p align="center" class="menu">CNAME-records are domain name aliases. Often computers on the Internet have multiple functions such as a web server ,&nbsp; FTP server ,&nbsp; mail server , etc. To mask this , CNAME-records can be used to give a single computer multiple names (aliases). For example computer "pti.com" may be both a web server and an FTP server , so two CNAME-records are defined: "www.pti.com" = "pti.com" and "ftp.pti.com" = "pti.com". Sometimes a single server computer hosts many different domain names so CNAME-records may be defined such as "www.abc.com" = "www.xyz.com". The most popular use the CNAME-record type is to provide access to a web server using both the standard "www.domain.com" and "domain.com" (without the www). This is usually done by creating an A-record for the short name (without www), and a CNAME-record for the www name pointing to the short name. CNAME-records can also be used when a computer or service needs to be renamed, to temporarily allow access through both the old and new name. A CNAME-record should always point to an A-record to avoid circular references. This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1035.txt">RFC1035 </a>. </p>
            <p align="center" class="highlight"><em>MX-Records (Mail Server) </em></p>
            <p align="center" class="menu">MX-records identify mail server(s) responsible for a domain name. When sending an E-mail to "user@xyz.com" ,&nbsp; the mail server must first look up the MX-record for "xyz.com" to see which mail server actually handles mail for "xyz.com" (this could be "mail.xyz.com" - or someone else's mail server like "mail.isp.com"). Then it looks up the A-record for the mail server to connect to its IP-address. An MX-record has a "Preference" number indicating the order in which the mail server should be used. (Only relevant when multiple MX-records are defined for the same domain name). <br>
  Mail servers will attempt to deliver mail to the server with the lowest preference number first , and if unsuccessful continue with the next lowest and so on.&nbsp;An MX-record identifies the name of a mail server ,&nbsp; not the IP address. Because of this , it is important that an A-record for the referenced mail server exists (not necessarily on your server, but wherever it belongs) , otherwise there may not be any way to find that mail server and communicate with it. Do not point an MX record to a CNAME-record. Many e-mail servers don't handle this. Add another A-record instead.&nbsp;This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1035.txt" target="_blank">RFC1035 </a>. </p>
            <p align="center" class="highlight"><em>NS-Records (Authoritative name server) </em></p>
            <p align="center" class="menu">NS-records identify DNS servers responsible (authoritative) for a domain. A domain should contain one NS-record for each of its own DNS servers (primary and secondaries). This mostly is used for zone transfer purposes. These NS-records have the same name as the domain&nbsp; in which they are located.But the most important function of the NS-record is delegation. Delegation means that part of a domain is delegated to other DNS servers. For example all ".com" sub-names (such as "pc-technics.com") are delegated from the "com" domain (hosted by the "InterNic"). <br>
  The "com" domain contains NS-records for all ".com" sub-names (a lot!). You can also delegate sub-names of your own domain name (such as "subname.yourname.com") to other DNS servers. <br>
  You are in effect the "InterNic" for all sub-names of your own domain name. To delegate "subname.yourname.com" , create NS-records for "subname.yourname.com" in the "yourname.com" domain. These NS-records must point to the DNS server responsible for "subname.yourname.com" for example "ns1.subname.yourname.com"&nbsp; or a DNS server somewhere else like "ns1.othername.net". An NS-record identifies the name of a DNS server not the IP-address. Because of this, it is important that an A-record for the referenced DNS server exists (not necessarily on your server, but wherever it belongs) , otherwise there may not be any way to find that DNS server and communicate with it. If an NS-record delegates a sub-name ("subname.yourname.com") to a DNS server with a name in that sub-name ("ns1.subname.yourname.com"), an A-record for that server (""ns1.subname.yourname.com") must exist in the parent domain ("yourname.com"). This A-record is referred to as a "glue" record, because it doesn't really belong in the parent domain , but is necessary to locate the DNS server for the delegated sub-name. This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1035.txt" target="_blank">RFC1035 </a>. </p>
            <p align="center" class="highlight"><em>PTR-Records (domain name pointer) </em></p>
            <p align="center" class="menu">PTR records maps IP addresses to domain names (reverse of A-records ). A PTR record's name is the IP address written in backward order with "in-addr.arpa." appended to the end. <br>
  As an example, looking up the domain name for IP address "1.2.3.4" is done through a query for the PTR-record for "4.3.2.1.in-addr.arpa." This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1035.txt" target="_blank">RFC1035 </a>. </p>
            <p align="center" class="highlight"><em>SOA-Records (Start of authority) </em></p>
            <p align="center" class="menu">Each domain contains exactly one SOA-record, which holds the following properties for the zone: </p>
            <ul class="style2">
              <li>
                <p align="center" class="menu">Name of primary DNS server <br>
      The domain name of the primary DNS server for the zone. <br>
      The zone should contain a matching NS-record. <br>
                </p>
              <li>
                <p align="center" class="menu">Mailbox of responsible person <br>
      The E-mail address (replace @ with a dot) of the person responsible for maintenance of the zone. <br>
      The standard for this is the "hostmaster" username - such as "hostmaster.pc-technics.com"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (= hostmaster@pc-technics.com). <br>
                </p>
              <li>
                <p align="center" class="menu">Serial number (see Zone Transfers) <br>
      Used by secondary DNS servers to check if the zone has changed. <br>
      If the serial number is higher than what the secondary server has, a zone transfer will be initiated. <br>
      This number is automatically increased by Simple DNS Plus when changes to the zone or its records are made (when another zone is selected or "Edit DNS Records" is closed). <br>
      Unless you have a specific reason for changing this numbers, it is best to let Simple DNS Plus manage it. <br>
      You should never decrease (lower) a serial number. <br>
                </p>
              <li>
                <p align="center" class="menu">Refresh Interval (see Zone Transfers) <br>
      How often secondary DNS servers should check if changes are made to the zone. <br>
                </p>
              <li>
                <p align="center" class="menu">Retry Interval (see Zone Transfers) <br>
      How often secondary DNS server should retry checking if changes are made , if the first refresh fails. <br>
                </p>
              <li>
                <p align="center" class="menu">Expire Interval (see Zone Transfers) <br>
      How long the zone will be valid after a refresh. <br>
      Secondary servers will discard the zone if no refresh could be made within this interval. <br>
                </p>
              <li>
                <p align="center" class="menu">Minimum (default) TTL <br>
      Used as the default TTL for new records created within the zone. <br>
      Also used by other DNS server to cache negative responses (such as record does not exist etc.) </p>
              </li>
            </ul>
            <p align="center" class="menu">This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1035.txt" target="_blank">RFC1035 </a>. </p>
            <p align="center" class="menu style3">Other Record Types </p>
            <p align="center" class="highlight"><em>A6-Records (IPv6 host address) </em></p>
            <p align="center" class="menu">IPv6 is the future replacement for the current IP address system (also known as IPv4). The current IPv4 addresses are 32 bits long ( x . x . x . x = 4 bytes), and therefore "only" support a total of 4,294,967,296 addresses - less than the global population. With this limitation there is an increasing shortage of IPv4 addresses. To solve the problem, the whole Internet will eventually be migrated to IPv6. IPv6 addresses are 128 bits long and and are written in hexadecimal numbers separated by colons (:) at every four digits. Zeros can be skipped - for example: 4C2F::1:2:3:4:567:89AB. Few applications and network devices currently support IPv6 and IPv6 addresses are not yet generally available., but this is expected to change rapidly. An A6-record is used to specify the IPv6 address (or part of the IPv6 address) for a host. A6-records expands the functionality of A- and AAAA-records by adding support for aggregation and renumbering. A lookup for an IPv6 records could involve several A6-records which each specify only part of the final address. This is achieved through the additional prefix-length and prefix name fields. A6-records are supposed to replace AAAA-records (see below). This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc2874.txt" target="_blank">RFC2874 </a>. </p>
            <p align="center" class="highlight"><em>AAAA-Records (IPv6 host address) </em></p>
            <p align="center" class="menu">An AAAA record specifies an absolute IPv6 address. This record type is supposed to be replaced by the A6 record type (see above). This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1886.txt" target="_blank">RFC1886 </a>. </p>
            <p align="center" class="highlight"><em>AFSDB-Records (AFS Data Base location) </em></p>
            <p align="center" class="menu">An AFSDB-record maps a domain name to an AFS (Andrew File System) database server. The server name points to an A-record  for the database server, and the sub-type indicates server type: 1 = AFS version 3.0 volume location server for the named AFS cell. 2 = DCE authenticated server. This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1183.txt" target="_blank">RFC1183 </a>. </p>
            <p align="center" class="highlight"><em>ATMA-Records (Asynchronous Transfer Mode address) </em></p>
            <p align="center" class="menu">An ATMA-record maps a domain name to an ATM address. The ATM address can be specified in either E.164 format (decimal) or NSAP format (hexadecimal). This record type is defined in " <a href="http://www.jhsoft.com/rfc/af-saa-0069.000.rtf" target="_blank">ATM Name System Specification Version 1.0 </a>"&nbsp; published by the ATM Forum. </p>
            <p align="center" class="highlight"><em>DNAME-Records (Non-Terminal DNS Name Redirection) </em></p>
            <p align="center" class="menu">A DNAME-record is used to map / rename an entire subtree of the DNS name space to another domain. It differs from the CNAME-record  which maps only a single node of the name space. This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc2672.txt" target="_blank">RFC2672 </a>. </p>
            <p align="center" class="highlight"><em>HINFO-Records (Host information) </em></p>
            <p align="center" class="menu">A HINFO-record specifies the host / server's type of CPU and operating system. This information can be used by application protocols such as FTP, which use special procedures when communicating with computers of a known CPU and operating system type. Standard CPU and operating system types are defined in <a href="http://www.jhsoft.com/rfc/rfc1700.txt" target="_blank">RFC1700 </a>. The standard for a Windows PC is "INTEL-386" / "WIN32". This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1035.txt" target="_blank">RFC1035 </a>. </p>
            <p align="center" class="highlight"><em>ISDN-Records (ISDN address) </em></p>
            <p align="center" class="menu">The ISDN-record maps a domain name to an ISDN (Integrated Services Digital Network) telephone number. The ISDN phone numbers / DDI (Direct Dial In) used should follow ITU-T E.163/E.164 international telephone numbering standards. For example 12121234567 ( 1=USA, 212=New York area code, 1234567=number) The ISDN sub-address is an optional hexadecimal number. This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1183.txt" target="_blank">RFC1183 </a>. </p>
            <p align="center" class="highlight"><em>MB, MG, MINFO, MR Records (mailbox records) </em></p>
            <p align="center" class="menu">Most Internet mail servers only support MX-records. Only use MB, MG, MINFO and MR records if you have specific requirements for these. To specify "mailbox" names, replace the E-mail address @ sign with a dot (.). </p>
            <p align="center" class="highlight"><em>MB-records (Mailbox) </em></p>
            <p align="center" class="menu">Maps a mailbox to a host (server). The host must be the same as a valid A-record already defined in the same zone. </p>
            <p align="center" class="highlight"><em>MG-records (Mail group member) </em></p>
            <p align="center" class="menu">Used to specify mail group members (one MG-record per member). Each member mailbox must be identical to a valid mailbox (MB-record). </p>
            <p align="center" class="highlight"><em>MINFO-records (Mailbox or mail list information) </em></p>
            <p align="center" class="menu">Specifies the mailbox of the responsible person and optionally a mailbox for errors for this mailbox or list. Each mailbox must be the same as a valid mailbox (MB-record) that already exist in the zone. </p>
            <p align="center" class="highlight"><em>MR-records (Renamed mailbox) </em></p>
            <p align="center" class="menu">Specifies a renamed mailbox. <br>
  An MR-record can be used as a forwarding entry for a user who has moved to a different mailbox. These record types are defined in <a href="http://www.jhsoft.com/rfc/rfc1035.txt" target="_blank">RFC1035 </a>. </p>
            <p align="center" class="highlight"><em>NSAP-Records (NSAP address) </em></p>
            <p align="center" class="menu">An NSAP-record maps a domain name to an NSAP address. The NSAP address is entered using hexadecimal digits - any NSAP address format is allowed. This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1706.txt" target="_blank">RFC1706 </a>. </p>
            <p align="center" class="highlight"><em>RP-Records (Responsible person) </em></p>
            <p align="center" class="menu">An RP-record specifies the mailbox of the person responsible for the host (domain name). </p>
            <p align="center" class="menu">A SOA-record defines the responsible person for an entire domain , but a zone may contain a large number of individual hosts / domain names for which different people are assigned responsibility. <br>
  The RP-record type makes it possible to identify the responsible person for individual domain names contained within the zone. To specify the "mailbox", replace the E-mail address @ sign with a dot (.). Optionally specify the domain name for a TXT-record with additional information (such as phone and address). This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1183.txt" target="_blank">RFC1183 </a>. </p>
            <p align="center" class="highlight"><em>RT-Records (Route through) </em></p>
            <p align="center" class="menu">An RT-record specifies an intermediate host that provides routing to the domain name (host) of the record. This can be used by computers which are not directly connected to the Internet, or wide area network (WAN). A preference value is used to set priority if multiple intermediate routing hosts are specified - lower values tried first. <br>
  For each intermediate host specified, a corresponding host (A) address resource record is needed in the current zone.&nbsp;This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1183.txt" target="_blank">RFC1183 </a>. </p>
            <p align="center" class="highlight"><em>SRV-records (location of service) </em></p>
            <p align="center" class="menu">SRV-records are used to specify the location of a service. They are recently being used in connection with different directory servers such as LDAP&nbsp; (Lightweight Directory Access Protocol), and Windows 2000 directory services. They can also be used for advanced load balancing and to specify specific ports for services - for example that a web-server is running on port 8080 instead of the usual port 80. This record type is however still considered experimental, and is NOT supported by most programs in use today, including web-browsers. The name of a SRV-record is defined as "_service._protocol.domain" - for example "_ftp._tcp.xyz.com". <br>
  Most internet services are defined in <a href="http://www.jhsoft.com/rfc/rfc1700.txt">RFC1700 </a> , and the protocol is generally TCP or UDP. The "service location" is specified through a target, priority, weight, and port: <br>
  - Target is the domain name of the server (referencing an A-record). <br>
  - Priority is a preference number used when more servers are providing the same service (lower numbers are tried first). <br>
  - Weight is used for advanced load balancing. <br>
  - Port is the TCP/UDP port number on the server that provides this service. </p>
            <p align="center" class="menu">This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc2782.txt" target="_blank">RFC2782 </a>. </p>
            <p align="center" class="highlight"><em>TXT-Records (Descriptive text) </em></p>
            <p align="center" class="menu">TXT-records are used to hold descriptive text. They are often used to hold general information about a domain name such as who is hosting it, contact person, phone numbers, etc. TXT-records are informational for people only and are not required for any DNS functions. This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1035.txt" target="_blank">RFC1035 </a>. </p>
            <p align="center" class="highlight"><em>X25-Records (X.25 PSDN address) </em></p>
            <p align="center" class="menu">An X25-records maps a domain name to a Public Switched Data Network (PSDN) address number. Numbers used with this record should follow the X.121 international numbering plan. This record type is defined in <a href="http://www.jhsoft.com/rfc/rfc1183.txt" target="_blank">RFC1183 </a>. </p>
            <p align="center" class="menu">This content has been provided compliments of the folks at <a href="http://www.jhsoft.com" target="_blank">JH Software </a>. Thanks guys! </p>
            <p class="menu">&nbsp;</p>
            <table width="50%" border="0" align="center" class="menu">
              <tr>
                <td width="195"><div align="center">
                    <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Display=AddDomain">Add a new domain </a></p>
                </div></td>
                <td width="177"><div align="center">
                    <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Display=DeleteDomain">Delete a domain </a></p>
                </div></td>
              </tr>
              <tr>
                <td><div align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Display=AddRecord">Add a new record </a></div></td>
                <td><div align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Display=DeleteRecord">Delete a record </a></div></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2"><div align="center"><a href="HELP.php">Return To Help Menu</a> </div></td>
              </tr>
            </table>
            <p class="menu">&nbsp;</p>
            <p class="menu">&nbsp;</p>
          </div></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td colspan=3 height="14"> 
      <div align="center"> 
        <table width="100%" border="0" cellspacing="0" cellpadding="0" height="35" align="center">
          <tr> 
            <td background="images/index_08.gif" height="35"> 
              <?php include("footer.html"); ?>
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
</BODY>
</HTML>