<?php
session_start();
require("../includes/AdminSecurity.php");
require("../includes/DB_ConnectionString.php");
require("../includes/GlobalConfigs.php");
if (!($BindQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='BIND'"))) {
    $Result .= "Could not get BIND configs from the database.";
  } else {
    $BIND = mysql_fetch_array($BindQuery);
    $ArrBIND = explode(",",$BIND['Value']);
    $BIND_CONF = $ArrBIND[0];
    $BIND_DATA = $ArrBIND[1];
    $RNDC_PATH = $ArrBIND[2];
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
		 $Start = strpos($Data,"/* BIND CONFIGURATION */");	 
		 $End = strpos($Data,"/* END BIND CONFIGURATION */");			
		 $Length = $End - $Start;   		 
		 $BIND_Configs = $_POST['BIND_CONF'] . "," . $_POST['BIND_DATA'] . "," . $_POST['RNDC_Path'];
		 $Configs2write = "/* BIND CONFIGURATION */\r\n// This BIND configuration was last updated by the control panel on " . date("m-d-y") . " by " . ucfirst($_SESSION['Username'] ."\r\n");
		 $Configs2write .= "\$BIND = \"" . $BIND_Configs . "\";\r\n";
		 $Configs2write .= "/* END BIND CONFIGURATION */";	
		 if ($Start === false) {
		   $NewConfigs = str_replace("?>",$Configs2write . "\r\n?>",$Data);
		 } else { 	  
		   $NewConfigs = substr_replace($Data,$Configs2write,$Start,$Length+29);		
		 }	 
		 if (!($NewGlobalConfig = fopen("../includes/GlobalConfigs.php","w"))) {
		   $Result .= "Could not open includes/GlobalConfigs.php to write new BIND settings.";
		 } else {
			if (!fwrite($NewGlobalConfig,trim($NewConfigs))) {
			   $Result .= "Could not write the new configurations to the GlobalConfigs.php file located at includes/GlobalConfigs.php";
			}
			  fclose($NewGlobalConfig);
			  mysql_query("UPDATE Globals Set Value='" . $BIND_Configs . "' WHERE Variable='BIND'");  
			  echo "<script language=\"JavaScript\">alert('Your BIND configuration has been successfully updated.');location.href = 'Admin.php';</script>"; exit();
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
.style2 {color: #FF0000}
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
                <td><a href="http://www.isc.org" target="_blank"><img src="../images/BIND.gif" width="122" height="30" border="0" align="right"></a></td>
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
					<form name="BINDSettings" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update" method="post">
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="menu">
                      <tr bordercolor="#F9F9F9">
                        <td colspan="2"><strong>BIND DNS </strong></td>
                        <td width="42%">&nbsp;</td>
                        <td width="2%" bgcolor="#F9F9F9">&nbsp;</td>
                        <td width="31%" bgcolor="#F9F9F9">&nbsp;</td>
                      </tr>
                      <tr bordercolor="#F9F9F9">
                        <td width="1%">&nbsp;</td>
                        <td width="24%"><li>Named.conf Location </li></td>
                        <td><input name="BIND_CONF" type="text" id="BIND_Conf" size="30" value="<?php echo $BIND_CONF; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the path to your BIND DNS server's named.conf config file. (The default location on a fresh Red Hat 9 installation is /etc/named.conf)</td>
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
                        <td><li>Data Directory</li></td>
                        <td><input name="BIND_DATA" type="text" id="BIND_Data" size="30" value="<?php echo $BIND_DATA; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the path to your BIND DNS server's data directory (Where the zone files are stored). The default location on a fresh Red Hat 9 installation is /var/named/. <span class="style2">NOTE: Include trailing slash!</span></td>
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
                        <td><li>RNDC Path </li></td>
                        <td><input name="RNDC_Path" type="text" id="BIND_Data" size="30" value="<?php echo $RNDC_PATH; ?>"></td>
                        <td bgcolor="#F9F9F9">&nbsp;</td>
                        <td bgcolor="#F9F9F9">This is the path to your BIND DNS server's 'RNDC' utility. (The default location on a fresh Red Hat 9 installation is /usr/sbin/rndc)</td>
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