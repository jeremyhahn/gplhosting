<?php
session_start();
require("../includes/AdminSecurity.php");
require("../includes/DB_ConnectionString.php");
require("../includes/GlobalConfigs.php");
 if (!($XmailQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='Xmail'"))) {
   $Result .= "Could not get Xmail configs from the database.";
 } else {
   $Xmail = mysql_fetch_array($XmailQuery);
   $ArrXmail = explode(",",$Xmail['Value']);
   $XmailHost = $ArrXmail[0];
   $XmailPort = $ArrXmail[1];
   $XmailUser = $ArrXmail[2];
   $XmailPass = $ArrXmail[3];
   $XmailMailRoot = $ArrXmail[4];
 }
// ------------------------------------------------------------------------------------------------------------------------------->
if (isset($_GET['Action']) && $_GET['Action'] == "Update") {
	
	if (!($GlobalConfig = fopen("../includes/GlobalConfigs.php","r"))) {
	   $Result = "Could not open includes/GlobalConfigs.php to read in current settings.";
	} else {
	  while ($ThisLine = fgets($GlobalConfig)) {
		 $Data .= $ThisLine;
	  }
		fclose($GlobalConfig);		 
		 $Start = strpos($Data,"/* XMAIL CONFIGURATION */");	 
		 $End = strpos($Data,"/* END XMAIL CONFIGURATION */");			
		 $Length = $End - $Start;   
		 $XmailConfigs = trim(strtolower($_POST['CTRL_Address'])) . "," . strtolower($_POST['CTRL_Port']) . "," . strtolower($_POST['CTRL_Username']) .
	                "," . strtolower($_POST['CTRL_Password']) . "," . $_POST['MailRoot'];
	
		 $Configs2write .= "/* XMAIL CONFIGURATION */\r\n// This XMail configuration was last updated by the control panel on " . date("m-d-y") . " by " . ucfirst($_SESSION['Username'] ."\r\n");
		 $Configs2write .= "\$Xmail = \"" . $XmailConfigs . "\";\r\n";
		 $Configs2write .= "/* END XMAIL CONFIGURATION */";	
		 if ($Start === false) {
		   $NewConfigs = str_replace("?>",$Configs2write . "\r\n?>",$Data);
		 } else { 	  
		   $NewConfigs = substr_replace($Data,$Configs2write,$Start,$Length+30);		
		 }	 
		 if (!($NewGlobalConfig = fopen("../includes/GlobalConfigs.php","w"))) {
		   $Result .= "Could not open includes/GlobalConfigs.php to write new XMail settings.";
		 } else {
			if (!fwrite($NewGlobalConfig,trim($NewConfigs))) {
			   $Result .= "Could not write the new configurations to the GlobalConfigs.php file located at includes/GlobalConfigs.php";
			}
			  fclose($NewGlobalConfig);
			  mysql_query("UPDATE Globals Set Value='" . $XmailConfigs . "' WHERE Variable='Xmail'");
			  echo "<script language=\"JavaScript\">alert('Your XMail configuration has been successfully updated.');location.href = 'Admin.php';</script>"; exit();
		 }
			fclose($NewGlobalConfig);
       }	
}



?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
@import url("../style.css");
-->
</style>
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
			    <td width="18%" rowspan="11"><?php include("../SubCP_Navigation.php"); ?></td><td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
                <td><div align="center" class="BodyHeader">Administration/Configuration Management </div></td>
			  </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><a href="http://www.xmailserver.org" target="_blank"><img src="../images/Xmail.gif" width="125" height="50" border="0" align="right"></a></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td><table width="76%"  border="1" align="center" bordercolor="#F9F9F9">
                  <tr>
                    <td>
					<form name="XMailSettings" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update" method="post">
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="menu">
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2"><strong>Xmail Server </strong></td>
                        <td width="42%">&nbsp;</td>
                        <td width="2%" bgcolor="#F9F9F9">&nbsp;</td>
                        <td width="31%" bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td><li>Xmail Root</li></td>
                        <td><input name="MailRoot" type="text" id="MailRoot" size="30" value="<?php echo $XmailMailRoot; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the path to your XMail 'MailRoot'. (The default for linux RPM is /var/MailRoot) </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td><li>CTRL Address:</li></td>
                        <td><input name="CTRL_Address" type="text" id="CTRL_Address" size="30" value="<?php echo $XmailHost; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the IP address or FQDN of the XMail server CTRL server on your network. Remember to use private addresses for security! (192.168.xxx.xxx) </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td><li>CTRL Port</li></td>
                        <td><input name="CTRL_Port" type="text" id="CTRL_Port" size="8" maxlength="5" value="<?php echo $XmailPort; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the port where the CTRL protocol server is listening. (Default is 6017) </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td><li>Username</li></td>
                        <td><input name="CTRL_Username" type="text" id="CTRL_Username" size="30" value="<?php echo $XmailUser; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the admin user name which the control panel will use to connect to the CTRL protocol. </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td><li>Password</li></td>
                        <td><input name="CTRL_Password" type="text" id="CTRL_Password" size="30" value="<?php echo $XmailPass; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the admin password ENCRYPTED (using XMCrypt in bin directory) which the control panel will use to connect to the CTRL protocol. </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td width="1%">&nbsp;</td>
                        <td width="24%">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
					  <tr bordercolor="#F9F9F9">
					    <td>&nbsp;</td>
					    <td colspan="2">
						  <table width="100%"  border="0">
                            <tr>
                              <td width="77%">&nbsp;</td>
                              <td width="23%"><input align="right" type="submit" name="Submit" value="Update"></td>
                            </tr>
                          </table></td>
					    <td>&nbsp;</td>
					    <td>&nbsp;</td>
					    </tr>
					  <tr bordercolor="#F9F9F9">
					  <td>&nbsp;</td>
					  <td colspan="2">&nbsp;</td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					  </tr>
                    </table>
					</form>
					</td>
                  </tr>
                </table>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
			<p>&nbsp;</p>
		  </td>
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