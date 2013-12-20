<?php
session_start();
require("../includes/AdminSecurity.php");
require("../includes/DB_ConnectionString.php");
require("../includes/GlobalConfigs.php");
if (!($WebalizerConfQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='WebalizerConf'"))) {
   $Result .= "Could not get Webalizer custom configs from the database.";
} else {
   $WebalizerConf = mysql_fetch_array($WebalizerConfQuery);
}
if (!($WebalizerHomeQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='WebalizerHome'"))) {
   $Result .= "Could not get Webalizer script config from the database.";
} else {
   $WebalizerHome = mysql_fetch_array($WebalizerHomeQuery);
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
		 $Start = strpos($Data,"/* WEBALIZER CONFIGURATION */");	 
		 $End = strpos($Data,"/* END WEBALIZER CONFIGURATION */");			
		 $Length = $End - $Start;   
		 
		 $Configs2write = "/* WEBALIZER CONFIGURATION */\r\n// This Webalizer configuration was last updated by the control panel on " . date("m-d-y") . " by " . ucfirst($_SESSION['Username'] ."\r\n");
		 $Configs2write .= "\$WebalizerConf = \"" . str_replace("\r\n","\\r\\n\";\r\n\$WebalizerConf .= \"",trim($_POST['WebalizerConfig'])) . "\";\r\n";
         $Configs2write .= "\$WebalizerHome = \"" . trim($_POST['WebalizerHome']) . "\";\r\n";
		 $Configs2write .= "/* END WEBALIZER CONFIGURATION */";	
		 if ($Start === false) {
		   $NewConfigs = str_replace("?>",$Configs2write . "\r\n?>",$Data);
		 } else { 	  
		   $NewConfigs = substr_replace($Data,$Configs2write,$Start,$Length+33);		
		 }	 
		 if (!($NewGlobalConfig = fopen("../includes/GlobalConfigs.php","w"))) {
		   $Result .= "Could not open includes/GlobalConfigs.php to write new Webalizer settings.";
		 } else {
			if (!fwrite($NewGlobalConfig,trim($NewConfigs))) {
			   $Result .= "Could not write the new configurations to the GlobalConfigs.php file located at includes/GlobalConfigs.php";
			}
			  fclose($NewGlobalConfig);
			  mysql_query("UPDATE Globals Set Value='" . trim($_POST['WebalizerConfig']) . "' WHERE Variable='WebalizerConf'");
	          mysql_query("UPDATE Globals Set Value='" . trim($_POST['WebalizerHome']) . "' WHERE Variable='WebalizerHome'");
			  echo "<script language=\"JavaScript\">alert('Your Webalizer configuration has been successfully updated.');location.href = 'Admin.php';</script>"; exit();
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
                <td><a href="http://www.webalizer.com" target="_blank"><img src="../images/webalizer.gif" width="88" height="31" border="0" align="right"></a></td>
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
                <td><table width="92%"  border="1" align="center" bordercolor="#F9F9F9">
                  <tr>
                    <td>
					<form name="WebalizerSettings" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update" method="post">
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="menu">
                      <tr bordercolor="#F9F9F9">
                        <td colspan="3"><strong>Webalizer Statistics</strong></td>
                        <td width="2%" bgcolor="#F9F9F9">&nbsp;</td>
                        <td width="31%" bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td width="1%">&nbsp;</td>
                        <td width="24%"><li>Global Directives:</li></td>
                        <td width="42%"><textarea name="WebalizerConfig" cols="35" rows="8" id="WebalizerConfig"><?php echo $WebalizerConf['Value']; ?></textarea></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">These are the global directives which get written to every virtual host's webalizer configuration file. Enter each value followed by a newline, and substitude the usual [TAB] character (usual webalizer syntax) for a comma.</td>
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
                        <td><li>Webalizer Home </li></td>
                        <td><input name="WebalizerHome" type="text" id="WebalizerHome" size="40" value="<?php echo $WebalizerHome['Value']; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the location of the folder which you keep your webalizer configuration files.</td>
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