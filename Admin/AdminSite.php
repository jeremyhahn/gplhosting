<?php
session_start();
require("../includes/AdminSecurity.php");
require("../includes/DB_ConnectionString.php");
require("../includes/GlobalConfigs.php");
if (!($TldQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='ThirdLevelDomains'"))) {
    $Result = "Could not get third level domain configs from the database.";
} else {
    $ThirdLevelDomains = mysql_fetch_array($TldQuery);
}
if (!($WANQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='ExternalWAN_IP'"))) {
   $Result .= "Could not get external WAN IP config from the database.";
} else {
   $WAN_IP = mysql_fetch_array($WANQuery);
}
if (!($CoLoQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='ServerLocations'"))) {
   $Result .= "Could not get server loaction configs from the database.";
} else {
   $CoLoServers = mysql_fetch_array($CoLoQuery);
}
if (!($HomeServerQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='HomeServer'"))) {
   $Result .= "Could not get membership plan configs from the database.";
} else {
   $HomeServer = mysql_fetch_array($HomeServerQuery);
}
if (!($SudoQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='SudoPassword'"))) {
   $Result .= "Could not get Webalizer script config from the database.";
} else {
   $SudoPassword = mysql_fetch_array($SudoQuery);
}
if (!($SSL_Query = mysql_query("SELECT Value FROM Globals WHERE Variable='UseSSL'"))) {
   $Result .= "Could not get Webalizer script config from the database.";
} else {
   $SSL = mysql_fetch_array($SSL_Query);
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
		 $Start = strpos($Data,"/* SITE CONFIGURATION */");	 
		 $End = strpos($Data,"/* END SITE CONFIGURATION */");			
		 $Length = $End - $Start; 
		 if (isset($_POST['UseSSL']) && $_POST['UseSSL'] == 1) {
		   $SSL = 1;
		 } else {
		   $SSL = 0;
		 }
		   $Configs2write = "/* SITE CONFIGURATION */\r\n// This site configuration was last updated by the control panel on " . date("m-d-y") . " by " . ucfirst($_SESSION['Username'] ."\r\n");  		 
	       $Configs2write .= "\$ThirdLevelDomains = \"" . str_replace(" ","",strtolower($_POST['ThirdLevelDomains'])) . "\";\r\n";
           $Configs2write .= "\$ExternalWAN_IP = \"" . $_POST['External_IP'] . "\";\r\n";
           $Configs2write .= "\$SudoPassword = \"" . base64_encode(trim($_POST['SudoPassword'])) . "\";\r\n";
	       $Configs2write .= "\$HomeServer = \"" . $_POST['HomeServer'] . "\";\r\n";
           $Configs2write .= "\$CoLoServers = \"" . $_POST['CoLoServers'] . "\";\r\n";    
		   $Configs2write .= "\$UseSSL = \"" . $SSL . "\";\r\n"; 
		 $Configs2write .= "/* END SITE CONFIGURATION */";	
		 if ($Start === false) {
		   $NewConfigs = str_replace("?>",$Configs2write . "\r\n?>",$Data);
		 } else { 	  
		   $NewConfigs = substr_replace($Data,$Configs2write,$Start,$Length+28);		
		 }	 
		 if (!($NewGlobalConfig = fopen("../includes/GlobalConfigs.php","w"))) {
		   $Result .= "Could not open includes/GlobalConfigs.php to write new site configurations.";
		 } else {
			if (!fwrite($NewGlobalConfig,trim($NewConfigs))) {
			   $Result .= "Could not write the new configurations to the GlobalConfigs.php file located at includes/GlobalConfigs.php";
			}
			  fclose($NewGlobalConfig);
			  mysql_query("UPDATE Globals Set Value='" . $_POST['HomeServer'] . "' WHERE Variable='HomeServer'");
		 mysql_query("UPDATE Clients Set HomeServer='" . $_POST['HomeServer'] . "'");
			  mysql_query("UPDATE Globals Set Value='" . $_POST['CoLoServers'] . "' WHERE Variable='ServerLocations'");
			  mysql_query("UPDATE Globals Set Value='" . base64_encode(trim($_POST['SudoPassword'])) . "' WHERE Variable='SudoPassword'");
			  mysql_query("UPDATE Globals Set Value='" . str_replace(" ","",strtolower($_POST['ThirdLevelDomains'])) . "' WHERE Variable='ThirdLevelDomains'");
			  mysql_query("UPDATE Globals Set Value='" . $_POST['External_IP'] . "' WHERE Variable='ExternalWAN_IP'");
			  mysql_query("UPDATE Globals Set Value='" . $SSL . "' WHERE Variable='UseSSL'");
			 
			  echo "<script language=\"JavaScript\">alert('Your site configuration has been successfully updated.');location.href = 'Admin.php';</script>"; exit();
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
<script language="JavaScript">
function VerifySudo() {
 if (document.SiteSettings.SudoPassword.value.length <=0) {
   alert('Your SUDO password is blank. This is probably one of the most ridiculous things you would ever do. Please use a password a little bit more secure.');
   return false;
 }
 if (document.SiteSettings.SudoPassword.value != document.SiteSettings.SudoPassword2.value) {
   alert('SUDO passwords do not match!');
   return false;
 }
 document.SiteSettings.submit();
}
</script>
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
			    <td width="18%" rowspan="12"><?php include("../SubCP_Navigation.php"); ?></td><td colspan="2">&nbsp;</td>
		      </tr>
			  <tr>
			    <td colspan="2">&nbsp;</td>
		      </tr>
			  <tr>
                <td colspan="2"><div align="center" class="BodyHeader">Administration/Configuration Management </div></td>
			  </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td width="57%">&nbsp;</td>
                <td width="25%"><a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank"><img src="../images/SiteConfig.gif" width="31" height="32" border="0"></a><span class="BodyHeader">Site Configuration</span> </td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2"><strong>NOTE:</strong> When changing the SUDO password, you must also reset the local system account password for your web server. Also make sure that your SUDO configuration file is set to allow your web server user (ie. apache, nobody, etc.) to execute the required commands for this control panel to work. Here is a <a href="SampleSudo.php">sample</a> SUDO configuration file. </td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2"></td>
              </tr>
              <tr>
                <td colspan="2"></td>
              </tr>
              <tr>
                <td colspan="2"><table width="92%"  border="1" align="center" bordercolor="#F9F9F9">
                  <tr>
                    <td>
					<form name="SiteSettings" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update" method="post">
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="menu">
                      <tr bordercolor="#F9F9F9">
                        <td width="24%" colspan="2"><strong>Site Configurations</strong></td>
                        <td width="42%">&nbsp;</td>
                        <td width="2%" bgcolor="#F9F9F9">&nbsp;</td>
                        <td width="31%" bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">Third Level Domains</td>
                        <td><input name="ThirdLevelDomains" type="text" id="ThirdLevelDomains" size="30" value="<?php echo $ThirdLevelDomains['Value']; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">List your domains which you want website members to be able to use as third level domains. Seperate each domain with a comma (,). </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">External IP </td>
                        <td><input name="External_IP" type="text" id="External_IP" size="30" value="<?php echo $WAN_IP['Value']; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the external IP which your server's network interface is connected to. (Your WAN IP) </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">Sudo Password</td>
                        <td><input name="SudoPassword" type="password" id="SudoPassword" size="30" value="<?php echo base64_decode($SudoPassword['Value']); ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td rowspan="3" bgcolor="#F9F9F9">This is the password that the 'apache' web server will use to invoke root privileges using SUDO to perform 'root level' tasks. <strong>(Your <a href="SampleSudo.php">sudoers</a> file must be configured to allow these commands!) </strong></td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">Confirm Sudo Password</td>
                        <td><input name="SudoPassword2" type="password" id="SudoPassword2" size="30" value="<?php echo base64_decode($SudoPassword['Value']); ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">Home Server</td>
                        <td><input name="HomeServer" type="text" id="HomeServer" size="30" value="<?php echo $HomeServer['Value']; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the Fully Qualified Domain Name (FQDN) of your home server.</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">Redundant Servers </td>
                        <td><input name="CoLoServers" type="text" id="CoLoServers" size="30" value="<?php echo $CoLoServers['Value']; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">These are other servers for your network which you split your load with. </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">Use Secure Sockets Layer (SSL) </td>
                        <td>
						  <?php if ($SSL['Value'] == 1) { ?>
						    <input name="UseSSL" type="checkbox" id="UseSSL" value="1" checked>
                            <label style="cursor:hand;" for="UseSSL">Enable</label>
						  <?php } else { ?>
						    <input name="UseSSL" type="checkbox" id="UseSSL" value="1">
                            <label style="cursor:hand;" for="UseSSL">Enable</label>
						  <?php } ?>
					   </td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">Enabling SSL will use Secure Sockets Layer when transmitting sensitive information to and from a server/client session. Your webserver must already be configured to accept SSL connections. </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
					  <tr bordercolor="#F9F9F9">
					    <td width="24%">&nbsp;</td>
					    <td colspan="2">
						  <table width="100%"  border="0">
                            <tr>
                              <td width="77%">&nbsp;</td>
                              <td width="23%"><input align="right" type="button" name="button" value="Update" onClick="JavaScript:VerifySudo()"></td>
                            </tr>
                          </table></td>
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
                <td colspan="2">&nbsp;</td>
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