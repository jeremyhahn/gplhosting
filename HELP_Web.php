<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<style type="text/css">
<!--
.style2 {color: #FF0000}
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
          <td height="212"><p align="center" class="BodyHeader">&nbsp;</p>
            <p align="center" class="BodyHeader">Help :: Website Administration</p>
            <p align="center" class="menu">The GPL Hosting network offers a very intuitive interface for managing your websites. If you already have a DNS server which is pointing your domain to our webserver, then creating a website is as simple as typing in a domain name. If you do not have DNS already configured, you will be asked whether or not you would like a DNS domain name set up for you. This is your cue to act! If you answer yes, build the DNS domain, you will have to point the name servers at your domain registrar to use ns1 and ns2.gplhosting.org as your primary and secondary name servers. Failing to complete this step will leave your website unavailable until you do so! Please also be aware that making changes to your authoritative name servers can take up to 78 hours to propegate throughout the internet.</p>
            <p align="center" class="menu">&nbsp;</p>
            <p><span class="BodyHeader">Adding A New Website</span><br>
                <span class="menu">Adding a new website is a breeze. Simply select a currently configured DNS domain name from the drop-down list, or select 'Other' to create a new domain. If this is your first website, type a password for the root FTP user and then submit the form. If you selected 'Other', you will be prompted with a dialog box asking you if a DNS domain should be built. If you answer OK, then a DNS domain will be automatically built to support your new website. If you would like mail or any custom records, you may set up these options after the addition of your new website/domain. If you cancel, no DNS zone will be built for your website, so unless you configure your own DNS server to point to our web server IP, your website will be unavailable until a resolver is configured for your zone. Please also be aware that unless you are using one of our free top level domains, you must purchase the domain from a domain registrar before you can use the domain on the internet. <span class="style2">NOTE: It may take up to 24 hours for your website to begin producing stats! </span></span></p>
            <p>&nbsp;</p>
            <p><span class="BodyHeader">Deleting  A Website</span><br>
              <span class="menu">To delete a website, point to the Web Hosting link in the control panel, and then click on 'Delete Website' from the submenu that rolls out. From here, simply click on the website that you want to delete, and you are done. Please be advised that by deleting a website, you are also deleting any FTP users associated with that website, as well as ALL files in the website home directory!</span></p>
            <p>&nbsp;</p>
            <p><span class="BodyHeader">Website Statistics </span><br>
              <span class="menu">The website statistics generator is a two part application. the GPL Hosting statistics generator displays all of the statistics which you would be in search of first in an easy to view format. You may select a drop down box next to each bandwidth header to change the displayed size for your calculating purposes. If you wish to see a more detailed output of your log files, you may click on the link under the summary which says 'Read Detailed Statistics' which will pop a window of stats generated by <a href="http://www.webalizer.com">webalizer</a>, a popular website statistic analyzer for the linux platform. Please be aware that a large number of websites means a large number of logfiles. When GPL Hosting statistics page is loaded, it must open each website logfile, and parse out all of the information to display on the loaded page. This can take a little bit of time, depending on the size of your log files. We will eventually be switching to a piped log program which will do all of the parsing to speed up the process, but for the time being, just be patient!</span></p>
            <p>&nbsp;</p>
            <p align="center"><a href="HELP.php" class="menu">Return To Main Help Menu</a></p>
            <p>&nbsp;</p></td>
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