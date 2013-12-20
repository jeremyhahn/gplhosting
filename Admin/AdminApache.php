<?php
session_start();
require("../includes/AdminSecurity.php");
require("../includes/DB_ConnectionString.php");
require("../includes/GlobalConfigs.php");

if (!($ApacheQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='Apache2.0'"))) {
    $Result .= "Could not get Apache 2.0 configs from the database.";
  } else {
    $Apache = mysql_fetch_array($ApacheQuery);
    $ArrApache = explode(",",$Apache['Value']);
    $DocumentRoot = $ArrApache[0];
    $DocumentLogs = $ArrApache[1];
    $HTTPD_CONF = $ArrApache[2];
    $NameVhost = $ArrApache[3];
	$HTTPD_Reload = $ArrApache[4];
	$RotateLogPath = $ArrApache[5];
	$RotateLogArg = $ArrApache[6];
	$CustomIndex = $ArrApache[7];
	$AutoStats = $ArrApache[8];
}
if (!($LogSpiderQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='IntApacheStatSpider'"))) {
   $Result .= "Could not get membership plan configs from the database.";
} else {
   $LogSpiderInterval = mysql_fetch_array($LogSpiderQuery);
}
if (!($CustDirectiveQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='ApacheCustDirectives'"))) {
  $Result .= "Could not get Apache custom configs from the database.";
} else {
  $CustDirectives = mysql_fetch_array($CustDirectiveQuery);
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
		 $Start = strpos($Data,"/* APACHE 2.0 CONFIGURATION */");	 
		 $End = strpos($Data,"/* END APACHE 2.0 CONFIGURATION */");			
		 $Length = $End - $Start;   
		 if (isset($_POST['EnableRotateLogs']) && $_POST['EnableRotateLogs'] == 1) {
		    $RotateLogPath = $_POST['RotateLogPath'];
			$RotateLogArg = $_POST['RotateLogArg'];
		 } else {
		    $RotateLogPath = 0;
			$RotateLogArg = 0;
		 }
		 if ($_POST['CustomIndex'] != "1") {
		    $CustomIndex = 0;
		 } else {
		    $CustomIndex = 1;
		 }
		 if ($_POST['AutoStats'] != "1") {
		    $AutoStats = 0;
		 } else {
		    $AutoStats = 1;
		 }
		 $ApacheConfigs = $_POST['DocumentRoot'] . "," . $_POST['LogRoot'] . "," . $_POST['ApacheConfig'] .
						"," . $_POST['NameVhost'] . "," . $_POST['HTTPD_Reload'] . "," . $RotateLogPath . "," . $RotateLogArg .
						"," . $CustomIndex . "," . $AutoStats;	
		 $Configs2write .= "/* APACHE 2.0 CONFIGURATION */\r\n// This Apache 2.0 configuration was last updated by the control panel on " . date("m-d-y") . " by " . ucfirst($_SESSION['Username'] ."\r\n");
		 $Configs2write .= "\$Apache2 = \"" . $ApacheConfigs . "\";\r\n";
		 $Configs2write .= "\$IntApacheStatSpider = \"" . $_POST['IntApacheStatSpider'] . "\";\r\n";
		 $Configs2write .= "\$ApacheCustDirectives = \"" . str_replace("\r\n","\\r\\n\";\r\n\$ApacheCustDirectives .= \"",$_POST['CustomApacheDirectives']) . "\";\r\n";
		 $Configs2write .= "/* END APACHE 2.0 CONFIGURATION */";	
		 if ($Start === false) {
		   $NewConfigs = str_replace("?>",$Configs2write . "\r\n?>",$Data);
		 } else { 	  
		   $NewConfigs = substr_replace($Data,$Configs2write,$Start,$Length+34);
		 }	 
		 if (!($NewGlobalConfig = fopen("../includes/GlobalConfigs.php","w"))) {
		   $Result .= "Could not open includes/GlobalConfigs.php to write new Apache settings.";
		 } else {
			if (!fwrite($NewGlobalConfig,trim($NewConfigs))) {
			   $Result .= "Could not write the new configurations to the GlobalConfigs.php file located at includes/GlobalConfigs.php";
			}
			  fclose($NewGlobalConfig);
			  mysql_query("UPDATE Globals Set Value='" . $ApacheConfigs . "' WHERE Variable='Apache2.0'");
			  mysql_query("UPDATE Globals Set Value='" . $_POST['IntApacheStatSpider'] . "' WHERE Variable='IntApacheStatSpider'");
			  mysql_query("UPDATE Globals Set Value='" . trim($_POST['CustomApacheDirectives']) . "' WHERE Variable='ApacheCustDirectives'");
			  mysql_query("UPDATE Globals Set Value='" . $_POST['CustomHTML'] . "' WHERE Variable='ApacheCustomIndex'");
			  echo "<script language=\"JavaScript\">alert('Your Apache configuration has been successfully updated.');location.href = 'Admin.php';</script>"; exit();
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
<script language="javascript">
function SwitchView(Div) {
  if (document.getElementById(Div).style.display == '') {
    document.getElementById(Div).style.display = 'none';
  } else {
    document.getElementById(Div).style.display = '';
  }    
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
                <td><a href="http://httpd.apache.org/" target="_blank"><img src="../images/Apache.gif" width="125" height="40" border="0" align="right"></a></td>
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
                <td><table width="60%"  border="1" align="center" bordercolor="#F9F9F9">
                  <tr>
                    <td>
					<form name="ApacheSettings" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update" method="post">
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0" class="menu">
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2"><strong>Apache Web Server </strong></td>
                        <td width="42%">&nbsp;</td>
                        <td width="2%" bgcolor="#F9F9F9">&nbsp;</td>
                        <td width="31%" bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td width="1%">&nbsp;</td>
                        <td width="24%"><li>Data Directory</li></td>
                        <td><input name="DocumentRoot" type="text" id="DocumentRoot" size="30" value="<?php echo $DocumentRoot; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">The location of your 'Document Root'. This is where you keep all of your users websites. (The default location on a fresh Red Hat 9 install is /var/www/html.)</td>
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
                        <td><li>Log Directory</li></td>
                        <td><input name="LogRoot" type="text" id="LogRoot" size="30" value="<?php echo $DocumentLogs; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">The location you want to store all of your users website log files. </td>
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
                        <td><li>Config File</li></td>
                        <td><input name="ApacheConfig" type="text" id="ApacheConfig" size="30" value="<?php echo $HTTPD_CONF; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">The location of your Apache web server configuration file. (The default location on a fresh Red Hat 9 installation is /etc/httpd/conf/httpd.conf.)</td>
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
                        <td><li>Name V-Host</li></td>
                        <td><input name="NameVhost" type="text" id="NameVhost" size="30" value="<?php echo $NameVhost; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">The IP or domain to use for the NameVirtualHost Apache directive. (You may also specify a port such as 1.2.3.4:80 or domain.com:8080)</td>
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
                        <td><li>Reload Command </li></td>
                        <td><input name="HTTPD_Reload" type="text" id="HTTPD_Reload" size="30" value="<?php echo $HTTPD_Reload; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the location and argument which the control panel will pass to do a reload of the httpd.conf config file after a new website has been added.</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td ></td>
                        <td><li>Automatic Statistics</li></td>
                        <td>
						<?php if ($AutoStats == 1) { ?>
						          <input name="AutoStats" id="AutoStats" type="checkbox" value="1" checked> <label style="cursor:hand;" for="AutoStats">Enable</label>
						<?php } else { ?>
						          <input name="AutoStats" id="AutoStats" type="checkbox" value="1"> <label style="cursor:hand;" for="AutoStats">Enable</label>
						<?php } ?>						    
						</td>                     
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">Statistics are generated when a user clicks on the 'Web Statistics' icon from the control panel. <strong><br>(Not recommended for large logfiles/website accounts)</strong></td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td ></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td ></td>
                        <td><li>Log Spider</li></td>
						<td><input name="IntApacheStatSpider" type="text" id="IntApacheStatSpider" value="<?php echo $LogSpiderInterval['Value']; ?>" size="30"></td>                     					
						<td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">How often you run your 'CommonLogSpider' utility.</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td ></td>
                        <td><li>Log Rotation </li></td>
                        <td><p>&nbsp;
						<?php
						   if (strlen($RotateLogPath) > 1) {
						 ?> 
						  <input type="checkbox" style="cursor:hand;" id="EnableRotateLogs" name="EnableRotateLogs" value="1" onClick="JavaScript:SwitchView('RotateLog');" checked> <label style="cursor:hand;" for="EnableRotateLogs">Enable</label></p>
						    <div id="RotateLog"><b>Location of rotatelogs utility:</b><br>
							      <input type="text" name="RotateLogPath" size="30" value="<?php echo $RotateLogPath; ?>">
								   <br>
								   <br>
								     <b>Argument</b><br><span style="font-size:9px;">[ rotationtime [ offset ]] | [ filesizeM ]</span><br><input type="text" name="RotateLogArg" value="<?php echo $RotateLogArg; ?>" size="5">
							</div>  
						 <?php   
						   } else {						   
						 ?>
						  <input type="checkbox" style="cursor:hand;" id="EnableRotateLogs" name="EnableRotateLogs" value="1" onClick="JavaScript:SwitchView('RotateLog');"> <label style="cursor:hand;" for="EnableRotateLogs">Enable</label></p>
						    <div id="RotateLog" style="display:none;"><b>Location of rotatelogs utility:</b><br>
							      <input type="text" name="RotateLogPath" size="30" value="<?php echo $RotateLogPath; ?>">
								   <br>
								   <br>
								     <b>Argument</b><br><span style="font-size:9px;">[ rotationtime [ offset ]] | [ filesizeM ]</span><br><input type="text" name="RotateLogArg" value="<?php echo $RotateLogArg; ?>" size="5">
							</div>  
						<?php } ?>						                             
						</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">Enabling log rotation will configure all virtual hosts to pipe the access and error logs to the 'rotatelogs' utility which comes packaged with the source Apache source code.</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td></td>
						<td><li>Custom Index</li></td>
                        <td><?php if ($CustomIndex == 1) { ?>
                            <table width="99%"  border="0" align="center" class="menu">
                              <tr>
                                <td width="8%"><input type="checkbox" style="cursor:hand;" name="CustomIndex" id="CustomIndex" value="1" checked></td>
                                <td width="92%"><label style="cursor:hand;" for="CustomIndex">Enable</label></td>
                              </tr>
                            </table>
                            <?php } else { ?>
                            <table width="99%"  border="0" align="center" class="menu">
                              <tr>
                                <td width="8%"><input style="cursor:hand;" name="CustomIndex" id="CustomIndex" type="checkbox" value="1"></td>
                                <td width="92%"><label style="cursor:hand;" for="CustomIndex">Enable</label></td>
                              </tr>
                            </table>
                            <?php } ?>
                        </td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">Enabling custom index will copy all the contents of /Custom/html (in the control panel install directory) to each virtual host created as their default index page. </td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td colspan="2"><li>Custom Directives</li>
                            <textarea name="CustomApacheDirectives" cols="40" rows="8" id="CustomApacheDirectives"><?php echo $CustDirectives['Value']; ?></textarea></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">These are global directives which get written to <strong><em>every</em></strong> virtual host container that the control panel will create within the httpd.conf Apache configuration file. Seperate each directive with a newline. </td>
                      </tr>
					  <tr bordercolor="#F9F9F9">
					    <td>&nbsp;</td>
					    <td colspan="2">&nbsp;</td>
					    <td>&nbsp;</td>
					    <td>&nbsp;</td>
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