<?php
session_start();
require("../includes/AdminSecurity.php");
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="../style.css">
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699">
<table width="780" border="0" cellpadding="0" cellspacing="0" height="383" bgcolor="#FFFFFF">
<?php include("../SubHeader.html"); ?>
  <tr> 
    <td colspan=3 background="../images/links.gif"> 
     <?php include("../navigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan="3" height="233"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr>
		<td height="212"><table class="menu" width="100%" border="0">
			  <tr>
			    <td width="18%" rowspan="9"><?php include("../SubCP_Navigation.php"); ?></td><td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
                <td><div align="center" class="BodyHeader">Administration Management </div></td>
			  </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><div align="center">Sample SUDO Configuration </div></td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td><table width="85%"  border="0" align="center" class="menu">
                  <tr>
                    <td># sudoers file.<br>
                      #<br>
                      # This file MUST be edited with the 'visudo' command as root.<br>
                      #<br>
                      # See the sudoers man page for the details on how to write a sudoers file.<br>
                      #
                      <p>Runas_Alias OP = root</p>
                      <p># Host alias specification</p>
                      <p># User alias specification<br>
                        User_Alias APACHE = apache</p>
                      <p># Cmnd alias specification</p>
                      <p># ------------- NEEDED BY DNS -----------------<br>
                        Cmnd_Alias RELOAD = /usr/sbin/rndc reload<br>
                        # ------------------------------------------------</p>
                      <p># -------------- NEEDED BY FTP -----------------<br>
                        Cmnd_Alias CHPASSWD = /usr/sbin/chpasswd<br>
                        Cmnd_Alias ADDUSER = /usr/sbin/useradd<br>
                        Cmnd_Alias DELUSER = /usr/sbin/userdel<br>
                        Cmnd_Alias GROUPDEL = /usr/sbin/groupdel<br>
                        Cmnd_Alias CHMOD = /bin/chmod<br>
                        # -------------------------------------------------</p>
                      <p># ------------------------------- NEEDED BY HTTP --------------------------------<br>
                        Cmnd_Alias GRACEFUL = /etc/init.d/httpd graceful<br>
                        Cmnd_Alias WEBALIZER = /usr/bin/webalizer<br>
						Cmnd_Alias STATS = /var/www/html/GPL_Hosting/Utilities/ApacheStatSpider<br>
                          # -----------------------------------------------------------------------------------<br>
                      </p>
                      <p>                        # Defaults specification</p>
                      <p># User privilege specification<br>
                        root ALL=(ALL) ALL<br>
                        APACHE ALL=(OP) RELOAD, CHPASSWD, ADDUSER, DELUSER, CHMOD, GRACEFUL, WEBALIZER, STATS</p>
                      <p># Uncomment to allow people in group wheel to run all commands<br>
                        # %wheel ALL=(ALL) ALL</p>
                      <p># Same thing without a password<br>
                        # %wheel ALL=(ALL) NOPASSWD: ALL</p>
                      <p># Samples<br>
                        # %users ALL=/sbin/mount /cdrom,/sbin/umount /cdrom<br>
                      # %users localhost=/sbin/shutdown -h now</p></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
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
            <td background="../images/index_08.gif" height="35"> 
              <?php include("../footer.html"); ?>
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
</BODY>
</HTML>