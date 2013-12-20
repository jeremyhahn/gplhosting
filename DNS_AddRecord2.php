<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");
require("includes/class.bind.php");


 if (isset($_GET['Action']) && $_GET['Action'] == "Insert") {   
    	
    switch ($_POST['RecType']) {	 
	 case "A":
     if (isset($_POST['ZoneID']) && $_POST['ZoneID'] != "") {
	     
		  if (!($Existence = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND Hostname='" . 
		        $_POST['Hostname'] . "' AND RecType='" . $_POST['RecType'] . "' AND ZoneID='" . $_POST['ZoneID'] . "' AND RecData='" . $_POST['IP_Address'] . "'"))) {
			    $Result = "Could not verify the uniqueness of the new record against the database.<br><b>MySQL Said:</b><br>" . mysql_error();
		  }	 else {
		        if (mysql_num_rows($Existence) > 0) {
			        echo "<script language=\"JavaScript\">alert('This A-Record already exists in your account!'); location.href = 'DNS.php';</script>";
			        exit();
			    }
		  }
		  if (!($A_RecQuery = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,ZoneID) VALUES('" .
											$_SESSION['Username'] . "','" . $_POST['Hostname'] . "','" . $_POST['RecType'] . "','" . 
											$_POST['IP_Address'] . "','" . $_POST['ZoneID'] . "')"))) {
			 $Result .= "Could not insert the requested A record at this time.<br><b>MySQL Said:</b><br>" . mysql_error();
		  }  else {	              
		      
			    if (!($A_RecQuery = mysql_query("SELECT RecID FROM DNS_Records WHERE Hostname='" . $_POST['Hostname'] . "' AND Username='" . $_SESSION['Username'] . "' AND RecType='" . $_POST['RecType'] . "'"))) {
			       $Result .= "Could not get newly inserted RecID for SQL query<br><b>MySQL Said:</b><br>" . mysql_error();
			    } else {
			       $PGPL->SudoLogin();
			       if (!$PGPL_BIND->RebuildZone($_POST['ZoneID'])) {
				       $Result .= "An error occurred while trying to rebuild the requested zone.";
				   }
				   $PGPL->SudoLogout();
			       $SQL_Handle_A = mysql_fetch_array($A_RecQuery);				
		           if (!$Result) {
				      echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php?OnLoad=" . $SQL_Handle_A['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
			       }
			   }		
		 }		
	 } 
	 break;
	 
	 case "CNAME":
	 if (substr($_POST['Hostname'],-1) == ".") {
	  $Hostname = substr_replace($_POST['Hostname'],"",strlen($RP)-1);
  	 } else { $Hostname = $_POST['Hostname']; }
	 if (substr_count($Hostname,".") >= 1) {
	   $Hostname = $Hostname . ".";
	 } else {
	   $Hostname = $Hostname;
	 }
     if (isset($_POST['ZoneID']) && $_POST['ZoneID'] != "") {
	    if (!($CNAME_Existence = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND Hostname='" . 
		    $Hostname . "' AND Alias='" . $_POST['Alias'] . "' AND ZoneID='" . $_POST['ZoneID'] . "'"))) {
			$Result = "Could not verify the uniqueness of the new record against the database.<br><b>MySQL Said:</b><br>" . mysql_error();
		} else {
		     if (mysql_num_rows($CNAME_Existence) > 0) {
			   echo "<script language=\"JavaScript\">alert('The specified alias name already exists in your account!'); location.href = 'DNS.php';</script>";
			   exit();
			 }
	    }
		if (!($CNAME_RecQuery = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,Alias,ZoneID) VALUES('" .
							               	$_SESSION['Username'] . "','" . $Hostname . "','" . $_POST['RecType'] . "','" . 
											$_POST['Alias'] . "','" . $_POST['ZoneID'] . "')"))) {
			 $Result = "Could not insert the requested CNAME record at this time.<br><b>MySQL Said:</b><br>" . mysql_error();
		}  else {	              
		     if (!($CNAME_RecQuery = mysql_query("SELECT RecID FROM DNS_Records WHERE Hostname='" . $Hostname . "' AND Username='" . $_SESSION['Username'] . "' AND RecType='" . $_POST['RecType'] . "'"))) {
			    $Result .= "Could not get newly inserted RecID for SQL query<br><b>MySQL Said:</b><br>" . mysql_error();
			 } else {
			   $PGPL->SudoLogin();
			    if (!$PGPL_BIND->RebuildZone($_POST['ZoneID'])) {
				   $Result .= "An error occurred while trying to rebuild the requested zone.";
				}
				$PGPL->SudoLogout();
			    $SQL_Handle_CNAME = mysql_fetch_array($CNAME_RecQuery);				
				if (!$Result) {
		           echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php?OnLoad=" . $SQL_Handle_CNAME['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
			    }
			 }		
		}		
	 } 
	 break;
	 
	 
	 case "MX":
	 if (substr($_POST['RecData'],-1) == ".") {
	  $RecData = substr_replace($_POST['RecData'],"",strlen($RP)-1);
  	 } else { $RecData = $_POST['RecData']; }
	 if (substr_count($RecData,".") >= 1) {
	   $RecData = $RecData . ".";
	 } else {
	   $RecData = $RecData;
	 }
	 if (isset($_POST['ZoneID']) && $_POST['ZoneID'] != "") {
        if (!($MX_Existence = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND Hostname='" . 
		                                  $_POST['Hostname'] . "' AND RecData='" . $RecData . "' AND ZoneID='" . $_POST['ZoneID'] . "'"))) {
			 $Result = "Could not verify the uniqueness of the new record against the database.<br><b>MySQL Said:</b><br>" . mysql_error();
		} else {
		     if (mysql_num_rows($MX_Existence) > 0) {
			   echo "<script language=\"JavaScript\">alert('The specified MX record already exists in your account!'); location.href = 'DNS.php';</script>";
			   exit();
			 }
		}
		if (!($Mail_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,MX_Pref,ZoneID) VALUES('" .
								 			 $_SESSION['Username'] . "','" . $_POST['Hostname'] . "','" . $_POST['RecType'] . "','" . 
											 $RecData . "','" . $_POST['Preference'] . "','" . $_POST['ZoneID'] . "')"))) {
			 $Result = "Could not insert the requested MX record at this time.<br><b>MySQL Said:</b><br>" . mysql_error();
		}  else {	              
		     if (!($MX_RecQuery = mysql_query("SELECT * FROM DNS_Records WHERE Hostname='" . $_POST['Hostname'] . "' AND Username='" . 
			                                  $_SESSION['Username'] . "' AND RecType='" . $_POST['RecType'] . "' AND RecData='" . 
											  $RecData . "'"))) {
			    $Result .= "Could not get newly inserted RecID for SQL query<br><b>MySQL Said:</b><br>" . mysql_error();
			 } else {
			    $PGPL->SudoLogin();
			    if (!$PGPL_BIND->RebuildZone($_POST['ZoneID'])) {
				   $Result .= "An error occurred while trying to rebuild the requested zone.";
				}
				$PGPL->SudoLogout();
			    $SQL_Handle = mysql_fetch_array($MX_RecQuery);
				if (!$Result) {
		           echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php?OnLoad=" . $SQL_Handle['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
			    }
			 }		
		}		
	 } 
	 break;
	 	 
	 case "NS":
	 if (substr($_POST['RecData'],-1) == ".") {
	  $RecData = substr_replace($_POST['RecData'],"",strlen($RP)-1);
  	 } else { $RecData = $_POST['RecData']; }
	 if (substr_count($RecData,".") >= 1) {
	   $RecData = $RecData . ".";
	 } else {
	   $RecData = $RecData;
	 }
     if (isset($_POST['ZoneID']) && $_POST['ZoneID'] != "") {
	    if (!($NS_Existence = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "'AND RecData='" . 
		                                  $RecData . "' AND RecType='" . $_POST['RecType'] . "' AND ZoneID='" . $_POST['ZoneID'] . "'"))) {
			 $Result = "Could not verify the uniqueness of the new record against the database.<br><b>MySQL Said:</b><br>" . mysql_error();
		} else {
		     if (mysql_num_rows($NS_Existence) > 0) {
			    echo "<script language=\"JavaScript\">alert('The specified NS record already exists in your account!'); location.href = 'DNS.php';</script>";
			    exit();
			 }
		}
		if (!($NS_RecQuery = mysql_query("INSERT INTO DNS_Records(Username,RecType,RecData,ZoneID) VALUES('" .
								         $_SESSION['Username'] . "','" . $_POST['RecType'] . "','" . 
										 $RecData . "','" . $_POST['ZoneID'] . "')"))) {
			 $Result = "Could not insert the requested NS record at this time.<br><b>MySQL Said:</b><br>" . mysql_error();
		} else {	              
		     if (!($NS_RecQuery = mysql_query("SELECT RecID FROM DNS_Records WHERE RecData='" . $RecData . "' AND Username='" . $_SESSION['Username'] . "' AND RecType='" . $_POST['RecType'] . "'"))) {
			     $Result .= "Could not get newly inserted RecID for SQL query<br><b>MySQL Said:</b><br>" . mysql_error();
			 } else {
			     $PGPL->SudoLogin();
			     if (!$PGPL_BIND->RebuildZone($_POST['ZoneID'])) {
				    $Result .= "An error occurred while trying to rebuild the requested zone.";
				 }
				 $PGPL->SudoLogout();
			     $SQL_Handle_NS = mysql_fetch_array($NS_RecQuery);
				 if (!$Result) {
		            echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php?OnLoad=" . $SQL_Handle_NS['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
			    }
			 }		
		}		
	 } 
	 break;	 
	 
	 default:
	  echo "The requested zone could not be updated. '<b>" . $_POST['RecType'] . "</b>' is not a supported record type.";
	  break;
   }
 } 
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<script language="javascript">
function VerifyElements(RecType) {
  
   switch (RecType) {
   
   case "A":
		// Check the hostname
		var InvalidChars = "!#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
		for (var i = 0; i < document.A_Record.Hostname.value.length; i++) {
		 if (InvalidChars.indexOf(document.A_Record.Hostname.value.charAt(i)) != -1) {
		  alert ('The specified domain name is invalid. Domain names may only contain ASCII text, numbers, and a period.');
		  return false;
		 }
		}     
		if (document.A_Record.Hostname.value.length < 1) {
		 alert ("You must enter a hostname.");
		 return false;
		}
		// Check the IP Address
		var WebIP = document.A_Record.IP_Address.value;
		 ArrIP = WebIP.split(".");
		  for (i=0; i < ArrIP.length; i++) {
			 if (ArrIP[i] > 255 || ArrIP[i] < 1) {
			   alert('Your A Record IP address is invalid. Please check the IP address and try submitting again.');
			   return false;
			 } 
		  }
		 var InvalidChars = "!@#$%^&*()[]\\\';,/{}|\":<>?`_+=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";
		 for (var i = 0; i < WebIP.length; i++) {
		  if (InvalidChars.indexOf(WebIP.charAt(i)) != -1) {
		   alert ('Your A Record IP address is invalid. Please check the IP address and try submitting again.');
		   return false;
		  }
		 }
		 document.A_Record.submit();
   break;
	
	
   case "CNAME":
        // Check the alias
		var InvalidChars = "!@#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
		for (var i = 0; i < document.CNAME_Record.Alias.value.length; i++) {
		 if (InvalidChars.indexOf(document.CNAME_Record.Alias.value.charAt(i)) != -1) {
		  alert ('The alias is invalid. Alias names may only contain ASCII text, numbers, and a period.');
		  return false;
		 }
		}     
		if (document.CNAME_Record.Alias.value.length < 1) {
		 alert ("You must enter an alias name.");
		 return false;
		}   
		// Check the hostname
		var InvalidChars = "!#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
		for (var i = 0; i < document.CNAME_Record.Hostname.value.length; i++) {
		 if (InvalidChars.indexOf(document.CNAME_Record.Hostname.value.charAt(i)) != -1) {
		  alert ('The hostname is invalid. Alias names may only contain ASCII text, numbers, and a period.');
		  return false;
		 }
		}     
		if (document.CNAME_Record.Hostname.value.length < 1) {
		 alert ("You must enter an alias name.");
		 return false;
		}   
		document.CNAME_Record.submit();
   break;
	 
	 
   case "MX":
        // Check the alias
		var InvalidChars = "!#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
		for (var i = 0; i < document.MX_Record.Hostname.value.length; i++) {
		 if (InvalidChars.indexOf(document.MX_Record.Hostname.value.charAt(i)) != -1) {
		  alert ('This hostname is invalid. Hostnames may only contain ASCII text, numbers, and a period.');
		  return false;
		 }
		}     
		if (document.MX_Record.Hostname.value.length < 1) {
		 alert ("You must enter a hostname name.");
		 return false;
		}
		// Check the RecData
		var InvalidChars = "!@#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
		for (var i = 0; i < document.MX_Record.RecData.value.length; i++) {
		 if (InvalidChars.indexOf(document.MX_Record.RecData.value.charAt(i)) != -1) {
		  alert ('This hostname is invalid. Hostnames may only contain ASCII text, numbers, and a period.');
		  return false;
		 }
		}     
		if (document.MX_Record.RecData.value.length < 1) {
		 alert ("NS_Record.RecData");
		 return false;
		}
        if (isNaN(document.MX_Record.Preference.value)) {
         alert ("The preference must only contain numeric characters.");
         return false;
        }   
		document.MX_Record.submit();
        break; 
	 
   case "NS":
	    // Check the NS record
		var InvalidChars = "!@#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
		for (var i = 0; i < document.NS_Record.RecData.value.length; i++) {
		 if (InvalidChars.indexOf(document.NS_Record.RecData.value.charAt(i)) != -1) {
		  alert ('This name server address is invalid. Name servers may only contain ASCII text, numbers, and a period.');
		  return false;
		 }
		}     
		if (document.NS_Record.RecData.value.length < 1) {
		 alert ("You must enter a name server.");
		 return false;
		}
		document.NS_Record.submit();
   break;   
   }
}
</script>
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
          <td width="100%" height="212">
		  <table class="menu" width="100%" border="0">
              <tr>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td width="18%"><?php include("CP_Navigation.php"); ?></td>
                <td colspan="2"><?php include("CenterOfAttention.php"); ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="2"><?php echo $Result; ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>
				<?php
				if (!($ZoneQuery = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $_GET['ZoneID'] . "'"))) {
				 echo "Could not execute SQL query for zone name";
				} else {
				 $ThisZone = mysql_fetch_array($ZoneQuery);
				 if ($ThisZone['Type'] == "slave") { echo "<script language=\"JavaScript\">alert('You can not add records to a slave zone. If you wish to add records to this zone, please add them to the authoritative primary server for this domain. (" . substr_replace($ThisZone['PS'],"",strlen($IPs)-1) . ")');location.href='DNS.php';</script>"; }
				}				
				 switch($_GET['RecType']) {				 
				 case "A":
				?>
				 <form name="A_Record" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Insert">                
                    <table width="100%" border="0" class="menu">
                      <tr>
                        <td colspan="2"><div align="center">
                          <p>An A-Record maps an IP address to a Fully Qualified Domain Name (FQDN). </p>
                          <?php if ($ThisA['Hostname'] == "@") { ?>
						  <p class="style2">NOTE: The '@' symbol is the character used to represent your root domain. </p>
                          <?php } ?>
						  <br>
					     </div>
						<br>
					   </td>
                      </tr>
                      <tr>
                        <td width="15%">Hostname:</td>
                        <td width="85%"><input name="Hostname" type="text" class="highlight" value="@">
                        <span class="menu">.<?php echo $ThisZone['Zone']; ?></span></td>
                      </tr>
                      <tr>
                        <td>IP Address: </td>
                        <td><input name="IP_Address" type="text" class="highlight" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>
                          <div align="left">
                            <input type="button" value="Create" onClick="JavaScript:VerifyElements('A');">
                          </div></td></tr>
                    </table>
					<input type="hidden" name="RecType" value="<?php echo $_GET['RecType']; ?>">
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">          
                </form>				
				<?php
				break;
				
				case "CNAME":
				?>
				<form name="CNAME_Record" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Insert">
				 <table width="100%"  border="0" class="menu">
                  <tr>
                    <td colspan="2"><div align="center">
                      <p>CNAME records are 'alias' records for an existing host.</p>
                      <p><span class="style2">Example:</span> An alias record of www for a hostname of web1 would allow you to type in www.yourdomain.com and get to web1.yourdomain.com. </p>
                    </div>
					<br>
					 </td>
                    </tr>
                  <tr>
                    <td width="18%">Alias: </td>
                    <td width="82%"><input name="Alias" type="text" class="highlight" value="www">
                    .<?php echo $ThisZone['Zone']; ?></td>
                  </tr>
                  <tr>
                    <td>For Host:</td>
                    <td><input type="text" name="Hostname" class="highlight"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Create" onClick="JavaScript:VerifyElements('CNAME');"></td>
                  </tr>
                </table>             
					<input type="hidden" name="RecType" value="<?php echo $_GET['RecType']; ?>">
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>"> 
                </form>				
				<?php
				break;
				
				
				case "MX":
				  if (!($ZoneRecs = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $_GET['ZoneID'] . "' AND MX_Pref!='0' ORDER BY MX_Pref"))) {
				   echo "Could not execute SQL query for MX Preference value.<br><b>MySQL Said:</b><br>" . mysql_error();
				  }
				    if (mysql_num_rows($ZoneRecs) == 1) {
					 $ThisRow = mysql_fetch_array($ZoneRecs);
					 $DisplayPref = $ThisRow['MX_Pref']+1;
					} else {					 
						while ($row = mysql_fetch_array($ZoneRecs)) {
						 $ThisPref = $row['MX_Pref'];
						 $RecordValues .= $ThisPref . ",";
						} 					 
					 $Recs = explode(",",$RecordValues);
					 $Element = count($Recs);
					 $DisplayPref = $Recs[$Element-2]+1;
				    }					
					
				?>
				<form name="MX_Record" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Insert">
				 <table width="100%"  border="0" class="menu">
                  <tr>
                    <td colspan="2"><div align="center">
                      <p>MX records are mail exchange records, which are used to let the internet know where your authoritative mail servers are located. If you have a backup mail server location, you may specify a higher preference for each additional mail server. If a failure occurs, your mail will be redirected to the next server in the list with the next highest preference level.<br>
                        </p> 
                    </p>
                      </div>
					  <br>
					  </td>
                    </tr>
                  <tr>
                    <td width="18%">Hostname:</td>
                    <td width="82%"><input name="Hostname" type="text" class="highlight" value="@">
                    .<?php echo $ThisZone['Zone']; ?></td>
                  </tr>
                  <tr>
                    <td>Mail Server: </td>
                    <td><input type="text" name="RecData" class="highlight" value="mail.gplhosting.org" onClick="JavaScript:this.value='';"></td>
                  </tr>
                  <tr>
                    <td>Preference:</td>
                    <td><input type="text" name="Preference" class="highlight" value="<?php echo $DisplayPref; ?>"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Create" onClick="JavaScript:VerifyElements('MX');"></td>
                  </tr>
                </table>             
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
					<input type="hidden" name="RecType" value="<?php echo $_GET['RecType']; ?>">
                </form>				
				<?php
				break;
				
				
				case "NS":
				?>
				<form name="NS_Record" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Insert">
				 <table width="100%"  border="0" class="menu">
                  <tr>
                    <td colspan="2"><div align="center">
                      <p>NS records let the internet know where your authoritative Name Servers are located.<br>
                        </p> 
                    </p>
                      </div>
					  <br>
					  </td>
                    </tr>
                  <tr>
                    <td width="14%">Name Server: </td>
                    <td width="86%"><input name="RecData" type="text" class="highlight" value="ns1.customnameserver.com" onClick="JavaScript:this.value='';"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Create" onClick="JavaScript:VerifyElements('NS');"></td>
                  </tr>
                </table>             
					<input type="hidden" name="RecID" value="<?php echo $_GET['RecID']; ?>">
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
					<input type="hidden" name="RecType" value="<?php echo $_GET['RecType']; ?>">
                </form>				
				<?php
				break;				
				
				default: 
				  echo "<font color=\"#FF0000\">Our DNS servers do not support " . $_GET['RecType'] . " records.</font>";
				  break;
				}
				?>
				</td>
              </tr>
            </table>
			<p>&nbsp;</p>		 </td>
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