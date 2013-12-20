<?php
session_start();
require("../includes/DB_ConnectionString.php");
if (!($MembershipQuery = mysql_query("SELECT Value FROM Globals WHERE Variable='MemberPlans'"))) {
  echo "Could not get membership plan configs from the database.";
}
if (isset($_GET['Action']) && $_GET['Action'] == "SetValue") { 

      $OldMemberships = mysql_fetch_array($MembershipQuery);
	  $OldMembers = explode("\r\n",$OldMemberships['Value']);				       					   
	   foreach ($OldMembers as $Value) { 
			$ThisData = explode(",",$Value);
			if ($ThisData[0] != $_POST['MemberID']) {
			  $ThisRow = $ThisData[0] . "," . $ThisData[1] . "," . $ThisData[2] . "," . $ThisData[3] . "," . $ThisData[4] . "," . 
			             $ThisData[5] . "," . $ThisData[6] . "," . $ThisData[7] . "," . $ThisData[8] . "," . $ThisData[9] . "," . 
						 $ThisData[10] . "," . $ThisData[11] . "," . $ThisData[12] . "\r\n";
			  $OldData .= $ThisRow;
			}
			
	   } 	
     if ($_POST['DynDNS'] < 1) { $DynDNS = "0"; } else { $DynDNS = "1"; }
	   $UpdatedMember = $_POST['MemberID'] . "," . $_POST['DNS_Domains'] . "," . $DynDNS . "," . $_POST['POP3_Domains'] . "," .
	                 $_POST['MailboxQuota'] . "," . $_POST['Mailboxes'] . "," . $_POST['MX_Backup'] . "," . $_POST['Port25'] . "," .
					 $_POST['Websites'] . "," . $_POST['FTP_Users'] . "," . $_POST['DiskQuota'] . "," . $_POST['BandwidthQuota'] . "," .
					 money_format("%i", $_POST['FEE']);
	   $NewMemberPlans = $OldData . $UpdatedMember;

	 if (!($GlobalConfig = fopen("../includes/GlobalConfigs.php","r"))) {
	   $Result = "Could not open includes/GlobalConfigs.php to read in current settings.";
	} else {
	  while ($ThisLine = fgets($GlobalConfig)) {
		 $Data .= $ThisLine;
	  }
		fclose($GlobalConfig);		 
		 $Start = strpos($Data,"/* MEMBER CONFIGURATION */");	 
		 $End = strpos($Data,"/* END MEMBER CONFIGURATION */");			
		 $Length = $End - $Start;   		 
		 $Configs2write = "/* MEMBER CONFIGURATION */\r\n// These membership settings were last updated by the control panel on " . date("m-d-y") . " by " . ucfirst($_SESSION['Username'] ."\r\n");
         $Configs2write .= "\$MemberPlans = \"" . str_replace("\r\n","\\r\\n\";\r\n\$MemberPlans .= \"",trim($NewMemberPlans)) . "\";\r\n";
		 $Configs2write .= "/* END MEMBER CONFIGURATION */";	
		 if ($Start === false) {
		   $NewConfigs = str_replace("?>",$Configs2write . "\r\n?>",$Data);
		 } else { 	  
		   $NewConfigs = substr_replace($Data,$Configs2write,$Start,$Length+30);		
		 }	 
		 if (!($NewGlobalConfig = fopen("../includes/GlobalConfigs.php","w"))) {
		   $Result .= "Could not open includes/GlobalConfigs.php to write new membership settings.";
		 } else {
			if (!fwrite($NewGlobalConfig,trim($NewConfigs))) {
			   $Result .= "Could not write the new configurations to the GlobalConfigs.php file located at includes/GlobalConfigs.php";
			}
			  fclose($NewGlobalConfig);
              mysql_query("UPDATE Globals Set Value='" . trim($NewMemberPlans) . "' WHERE Variable='MemberPlans'");
			  echo "<script language=\"JavaScript\">alert('Your membership configuration has been successfully updated.');location.href = 'Admin.php';</script>"; exit();
		 }
			fclose($NewGlobalConfig);
       }	      
}
if (isset($_GET['Action']) && $_GET['Action'] == "Delete") {
      $OldMemberships = mysql_fetch_array($MembershipQuery);
	  $OldMembers = explode("\r\n",$OldMemberships['Value']);				       					   
	   foreach ($OldMembers as $Value) { 
			$ThisData = explode(",",$Value);
			if ($ThisData[0] != $_GET['Plan']) {
			  $ThisRow = $ThisData[0] . "," . $ThisData[1] . "," . $ThisData[2] . "," . $ThisData[3] . "," . $ThisData[4] . "," . 
			             $ThisData[5] . "," . $ThisData[6] . "," . $ThisData[7] . "," . $ThisData[8] . "," . $ThisData[9] . "," . 
						 $ThisData[10] . "," . $ThisData[11] . "," . $ThisData[12] . "\r\n";
			  $OldData .= $ThisRow;
			}
			
	   } 	
	   $NewMemberPlans = $OldData;
	 if (!($GlobalConfig = fopen("../includes/GlobalConfigs.php","r"))) {
	   $Result = "Could not open includes/GlobalConfigs.php to read in current settings.";
	} else {
	  while ($ThisLine = fgets($GlobalConfig)) {
		 $Data .= $ThisLine;
	  }
		fclose($GlobalConfig);		 
		 $Start = strpos($Data,"/* MEMBER CONFIGURATION */");	 
		 $End = strpos($Data,"/* END MEMBER CONFIGURATION */");			
		 $Length = $End - $Start;   		 
		 $Configs2write = "/* MEMBER CONFIGURATION */\r\n// These membership settings were last updated by the control panel on " . date("m-d-y") . " by " . ucfirst($_SESSION['Username'] ."\r\n");
         $Configs2write .= "\$MemberPlans = \"" . str_replace("\r\n","\\r\\n\";\r\n\$MemberPlans .= \"",trim($NewMemberPlans)) . "\";\r\n";
		 $Configs2write .= "/* END MEMBER CONFIGURATION */";	
		 if ($Start === false) {
		   $NewConfigs = str_replace("?>",$Configs2write . "\r\n?>",$Data);
		 } else { 	  
		   $NewConfigs = substr_replace($Data,$Configs2write,$Start,$Length+30);		
		 }	 
		 if (!($NewGlobalConfig = fopen("../includes/GlobalConfigs.php","w"))) {
		   $Result .= "Could not open includes/GlobalConfigs.php to write new membership settings.";
		 } else {
			if (!fwrite($NewGlobalConfig,trim($NewConfigs))) {
			   $Result .= "Could not write the new configurations to the GlobalConfigs.php file located at includes/GlobalConfigs.php";
			}
			  fclose($NewGlobalConfig);
              mysql_query("UPDATE Globals Set Value='" . trim($NewMemberPlans) . "' WHERE Variable='MemberPlans'");
			  echo "<script language=\"JavaScript\">alert('Your membership configuration has been successfully updated.');location.href = 'Admin.php';</script>"; exit();
		 }
			fclose($NewGlobalConfig);
       }	      
}
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="../style.css">
<script language="javascript">
function Delete(Plan) {
  if (Plan == "Guest") {
    alert('You can not delete the guest account!');
	return false;
  }
  if (Plan == "Site Admin") {
    alert('You can not delete the site administrator account!');
	return false;
  }
  var decision = confirm("Are you sure you want to delete "+Plan+" from your membership list?");
  if (decision) {
    location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?Action=Delete&Plan='+Plan;
  } else {
    return false;
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
                <td><div align="center" class="BodyHeader">Administration Management </div></td>
			  </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><div align="center" class="highlight">Choose a  plan you want to modify by clicking on it. </div></td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td>
				
				<table width="95%"  border="0" align="center" class="menu">    
				
				<?php if (isset($_GET['Action']) && $_GET['Action'] == "Update") { 
				
				      $Memberships = mysql_fetch_array($MembershipQuery);
					  $Member = explode("\r\n",$Memberships['Value']);
					   foreach ($Member as $Value) { 
					        $ThisData = explode(",",$Value);
							if ($ThisData[0] == $_GET['Plan']) {
							   $Data = $ThisData;
							}
					   }  				
				?>      			
							
				
				<form name="<?php echo str_replace(" ","",$Data[0]); ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=SetValue&Plan=<?php echo $Data[0]; ?>" method="post">
					<table align="center" width="60%" id="<?php echo $Data[0]; ?>" border="1" class="CustomTable">
                      <tr>
                        <td colspan="2">
						<?php if ($_GET['Plan'] == "NewMembership") { ?>
						        <div align="center" class="TableHeader HighLight"><input style="text-align:center; font-weight:bold;" type="text" size="40" name="MemberID" value="New Membership Name Here" onFocus="JavaScript:this.value = ''"></div>
						<?php } else { ?>
						        <div align="center" class="TableHeader HighLight"><em><?php echo $Data[0]; ?></em></div>
						<?php } ?>						
						</td>
                        </tr>
                      <tr class="TableRow">
                        <td width="78%">DNS Domains</td>
                        <td width="22%"><input type="text" class="menu" name="DNS_Domains" size="10" value="<?php echo $Data[1]; ?>"></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Allow Dynamic DNS </td>
                        <td><?php if ($Data[2] == 1) { echo "<input type=\"checkbox\" name=\"DynDNS\" value=\"1\" checked> (Enabled)"; } else { echo "<input type=\"checkbox\" name=\"DynDNS\" value=\"1\"> <font color=\"FF0000\">(Disabled)</font>"; } ?></td>
                      </tr>
                      <tr class="TableRow">
                        <td>POP3 Domains</td>
                        <td><input class="menu" name="POP3_Domains" type="text" size="10" value="<?php echo $Data[3]; ?>"></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Mailboxes</td>
                        <td><input class="menu" name="Mailboxes" type="text" size="10" value="<?php echo $Data[5]; ?>"></td>
                      </tr>
                      <tr class="TableRow">
                        <td>Mailbox Disk Quota (KiloBytes)</td>
                        <td><input class="menu" name="MailboxQuota" type="text" size="10" value="<?php echo $Data[4]; ?>"></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>MX Backup Domains</td>
                        <td><input name="MX_Backup" class="menu" type="text" size="10" value="<?php echo $Data[6]; ?>"></td>
                      </tr>
                      <tr class="TableRow">
                        <td>Port 25 Deflection Domains</td>
                        <td><input class="menu" name="Port25" type="text" size="10" value="<?php echo $Data[7]; ?>"></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Total Allowed Websites</td>
                        <td><input class="menu" name="Websites" type="text" size="10" value="<?php echo $Data[8]; ?>"></td>
                      </tr>
                      <tr class="TableRow">
                        <td>Total Allowed FTP Users Per Website </td>
                        <td><input class="menu" name="FTP_Users" type="text" size="10" value="<?php echo $Data[9]; ?>"></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Website Disk Quota (Bytes)</td>
                        <td><input class="menu" name="DiskQuota" type="text" size="10" value="<?php echo $Data[10]; ?>"></td>
                      </tr>
                      <tr class="TableRow">
                        <td>Website Bandwidth Quota (Bytes)</td>
                        <td><input class="menu" name="BandwidthQuota" type="text" size="10" value="<?php echo $Data[11]; ?>"></td>
                      </tr>
                      <tr class="TableRow2">
                        <td><strong>Cost of Membership</strong></td>
                        <td class="highlight"><input class="menu" name="FEE" type="text" size="10" value="<?php echo $Data[12]; ?>"></td>
                      </tr>
					  <tr class="TableRow">
					    <td align="right">
						    <?php if ($_GET['Plan'] != "NewMembership") { ?><input type="hidden" name="MemberID" value="<?php echo $Data[0]; ?>">
						    <input class="menu" style="font-weight:bold;" type="button" value="Delete" onClick="JavaScript:Delete('<?php echo $Data[0]; ?>')">
					        <?php } ?>
						</td>
						<td><input class="menu" style="font-weight:bold;" type="submit" value="Update"></td>
					  </tr>
                    </table>
				   </form>		 
				<?php } elseif (!isset($_GET['Action'])) {
					  
				 ?>
				 <tr><td>
				 <form name="NewMembership" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update&Plan=NewMembership" method="post">
					<table style="cursor:hand;" width="100%" id="<?php echo $Data[0]; ?>" border="1" class="CustomTable" onClick="JavaScript:document.NewMembership.submit();">
                      <tr>
                        <td colspan="2"><div align="center" class="TableHeader"><em>NEW Membership</em></div></td>
                        </tr>
                      <tr class="TableRow">
                        <td width="78%">DNS Domains</td>
                        <td width="22%">&nbsp;</td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Allow Dynamic DNS </td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow">
                        <td>POP3 Domains</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Mailboxes</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow">
                        <td>Mailbox Disk Quota (KiloBytes)</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow2">
                        <td>MX Backup Domains</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow">
                        <td>Port 25 Deflection Domains</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Total Allowed Websites</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow">
                        <td>Total Allowed FTP Users Per Website </td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Website Disk Quota (Bytes)</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow">
                        <td>Website Bandwidth Quota (Bytes)</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr class="TableRow2">
                        <td><strong>Cost of Membership</strong></td>
                        <td class="highlight">&nbsp;</td>
                      </tr>
                    </table>
				   </form>
				   </td></tr>
				   
				   <?php
				 
				 
				      $Memberships = mysql_fetch_array($MembershipQuery);
					  $Member = explode("\r\n",$Memberships['Value']);   
					  foreach ($Member as $Value) { 
					        $Data = explode(",",$Value);
					        $El++; 
							  if ($El < 2) { 
							    echo "<tr><td width=\"50%\">";
							  } 
				  ?>				
				  <form name="<?php echo str_replace(" ","",$Data[0]); ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update&Plan=<?php echo $Data[0]; ?>" method="post">
					<table style="cursor:hand;" width="100%" id="<?php echo $Data[0]; ?>" border="1" class="CustomTable" onClick="JavaScript:document.<?php echo str_replace(" ","",$Data[0]); ?>.submit();">
                      <tr>
                        <td colspan="2"><div align="center" class="TableHeader HighLight"><em><?php echo $Data[0]; ?></em></div></td>
                        </tr>
                      <tr class="TableRow">
                        <td width="78%">DNS Domains</td>
                        <td width="22%"><?php echo $Data[1]; ?></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Allow Dynamic DNS </td>
                        <td><?php if ($Data[2] == 1) { echo "YES"; } else { echo "NO"; } ?></td>
                      </tr>
                      <tr class="TableRow">
                        <td>POP3 Domains</td>
                        <td><?php if ($Data[3] == 0) { echo "<font color=\"FF0000\">Unlimited</font>"; } else { echo $Data[3]; } ?></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Mailboxes</td>
                        <td><?php if ($Data[5] == 0) { echo "<font color=\"FF0000\">Unlimited</font>"; } else { echo $Data[5]; } ?></td>
                      </tr>
                      <tr class="TableRow">
                        <td>Mailbox Disk Quota (KiloBytes)</td>
                        <td><?php echo $Data[4]; ?></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>MX Backup Domains</td>
                        <td><?php if ($Data[6] == 0) { echo "<font color=\"FF0000\">Unlimited</font>"; } else { echo $Data[6]; } ?></td>
                      </tr>
                      <tr class="TableRow">
                        <td>Port 25 Deflection Domains</td>
                        <td><?php if ($Data[7] == 0) { echo "<font color=\"FF0000\">Unlimited</font>"; } else { echo $Data[7]; } ?></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Total Allowed Websites</td>
                        <td><?php if ($Data[8] == 0) { echo "<font color=\"FF0000\">Unlimited</font>"; } else { echo $Data[8]; } ?></td>
                      </tr>
                      <tr class="TableRow">
                        <td>Total Allowed FTP Users Per Website </td>
                        <td><?php if ($Data[9] == 0) { echo "<font color=\"FF0000\">Unlimited</font>"; } else { echo $Data[9]; } ?></td>
                      </tr>
                      <tr class="TableRow2">
                        <td>Website Disk Quota (Bytes)</td>
                        <td><?php if ($Data[10] == 0) { echo "<font color=\"FF0000\">Unlimited</font>"; } else { echo $Data[10]; } ?></td>
                      </tr>
                      <tr class="TableRow">
                        <td>Website Bandwidth Quota (Bytes)</td>
                        <td><?php if ($Data[11] == 0) { echo "<font color=\"FF0000\">Unlimited</font>"; } else { echo $Data[11]; } ?></td>
                      </tr>
                      <tr class="TableRow2">
                        <td><strong>Cost of Membership</strong></td>
                        <td class="highlight"><?php if ($Data[12] == 0) { echo "<font color=\"FF0000\">FREE!</font>"; } else { echo "\$" . $Data[12]; } ?></td>
                      </tr>
                    </table>
				   </form>
					<p></p>
			<?php
					if ($El < 2) { 
						echo "</td><td width=\"50%\">";					     
					  } else {					    
						echo "</td></tr>";
						$El = 0; 
					  }
				}
			  }
			 ?>
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