<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");
require("includes/class.bind.php");

  $DomainList = explode(",",$ThirdLevelDomains);
  $NumDomains = count($DomainList);


## ------------------------------------------------------------------------------------------------------------------------------------>

   if ($_GET['Action'] == "CreateZone") {
   	 
	 $PGPL->SudoLogin();
	 $Quota = explode("\r\n",$MemberPlans);			 
	 foreach ($Quota as $Value) {
	  $Data = explode(",",$Value);
		  if ($Data[0] == $_SESSION['SiteRole']) {
		     $NewDomainCap = $Data[1];
		  }
	 } 
	 $TotalDomains = mysql_query("SELECT Zone FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "'");
	 if (mysql_num_rows($TotalDomains) >= $NewDomainCap && $NewDomainCap !=0) { 
	    echo "<script language=\"JavaScript\">alert('You have exceeded your DNS domain quota of " . $NewDomainCap . ". Operation aborted.');location.href = 'DNS.php';</script>";
	    exit(); 
	 }	 
	 if ($_POST['IS_Master'] == "master") {
	  $ZoneType = "master";
	 } else {
	  $ZoneType = "slave";
	 }		   
	 // Decides if the visitor is trying to create a third level domain or their own top level domain.
	 if (isset($_POST['ThirdLevelDomain']) && $_POST['ThirdLevelDomain'] != "") {
	   $Domain2Add = strtolower($_POST['Domain']) . "." . strtolower($_POST['ThirdLevelDomain']);
	 } else {
	   $Domain2Add = strtolower($_POST['Domain']);
	 }    		     
	 // Check to make sure that this zone does not already exist!
	 // CREATE ZONE
	 $chkZone = mysql_query("SELECT * FROM DNS_Zones WHERE Zone='" . $Domain2Add . "'");
	 if (mysql_num_rows($chkZone) > 0) {
	    echo "<script language=\"JavaScript\">alert('" . $Domain2Add . " is already a registered domain! Please select a different zone.');history.go(-1);</script>";
	 } else {
	    // If this is a slave zone, lets process it now and get out of here!
 	    if ($ZoneType == "slave") {
		    if (substr($_POST['PrimaryIP'],-1) == ",") {
				$IPs = substr_replace($ThisSlave['PS'],"",strlen($IPs)-1);
			} else {
			    $IPs = $_POST['PrimaryIP'];
			}  
		    if (!($SlaveInsert = mysql_query("INSERT INTO DNS_Zones(Username,Zone,Type,PS) VALUES('" . $_SESSION['Username'] . "','" . 
			                                 $Domain2Add . "','slave','" . $_POST['PrimaryIP'] . ",')"))) {
			    $Result .= "Could not insert new slave zone into the database.<br><b>MySQL Said:</b><br>" . mysql_error();   
			} else {
			    if (!$PGPL_BIND->WriteSlaveZone($Domain2Add,$IPs . ",")) {
				   $Result .= "An error occurred while attempting to write the new slave zone to disk. Operation aborted.";
				} else {
			      $PGPL_BIND->Reload();
				  $PGPL->SudoLogout();
				  if (!$Result) {
				     echo "<script language=\"JavaScript\">alert('" . $Domain2Add . " was successfully created.');location.href = 'DNS.php';</script>"; exit();
				  }
			    }
			}		   
		}
		// Adds new domain to MySQL database.
		if (!($WebInsert = mysql_query("INSERT INTO DNS_Zones(Username,Zone,Type,PS,RP,Serial,Refresh,Retry,Expire,TTL) VALUES('" .
									   $_SESSION['Username'] . "','" . $Domain2Add . "','" . $ZoneType . "','" . $PGPL_BIND->PS . "','" . 
									   $PGPL_BIND->RP . "','0','" . $PGPL_BIND->REFRESH . "','" . $PGPL_BIND->RETRY . "','" . 
									   $PGPL_BIND->EXPIRE . "','" . $PGPL_BIND->TTL . "')"))) {
			  $Result = "Could not execute SQL insert statement for new zone.<br><b>MySQL Said:</b><br>" . mysql_error();
	   } 
	   if (!($ZoneID = mysql_query("SELECT * FROM DNS_Zones WHERE Zone='" . $Domain2Add . "'"))) {
		 $Result .= "<br>Could not retrieve ZoneID for new zone.<br><b>MySQL Said:</b><br>" . mysql_error();
	   } else {
		 $DB_ZoneID = mysql_fetch_array($ZoneID);
	   }
	   // Adds A-Record to the MySQL database for root domain
	   if (isset($_POST['WebIP']) && $_POST['WebIP'] != "") {
		   if (!($A_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,ZoneID) VALUES('" .
											 $_SESSION['Username'] . "','@','A','" . $_POST['WebIP'] . "','" . $DB_ZoneID['ZoneID'] . "')"))) {
				 $Result .= "<br>Could not execute SQL insert statement for A-Record.<br><b>MySQL Said:</b><br>" . mysql_error();
		   }
		   if (!($CNAME_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,Alias,ZoneID) VALUES('" .
												 $_SESSION['Username'] . "','@','CNAME','www','" . $DB_ZoneID['ZoneID'] . "')"))) {
				 $Result .= "<br>Could not execute SQL insert statement for CNAME-Record.<br><b>MySQL Said:</b><br>" . mysql_error();
		   }			  
	   }
	   // Adds A-Record for FTP address if applicable
	   if (isset($_POST['FTPip']) && $_POST['FTPip'] != "") {
		   if (!($FTP_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,ZoneID) VALUES('" .
											   $_SESSION['Username'] . "','ftp','A','" . $_POST['FTPip'] . "','" . $DB_ZoneID['ZoneID'] . "')"))) {
				 $Result .= "<br>Could not execute SQL insert statement for FTP A-Record.<br><b>MySQL Said:</b><br>" . mysql_error();
		   }
	   }
	   // Adds Mail records if applicable
	   if (isset($_POST['MailIP']) && $_POST['MailIP'] != "") {
		  if (!($Mail_A_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,ZoneID) VALUES('" .
												 $_SESSION['Username'] . "','mail','A','" . $_POST['MailIP'] . "','" . $DB_ZoneID['ZoneID'] . "')"))) {
				$Result .= "<br>Could not execute SQL insert statement for mail A-Record.<br><b>MySQL Said:</b><br>" . mysql_error();
		  }
		  if (!($Mail_MX_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,MX_Pref,ZoneID) VALUES('" .
												  $_SESSION['Username'] . "','@','MX','mail." . $Domain2Add . ".','1','" . $DB_ZoneID['ZoneID'] . "')"))) {
				$Result .= "<br>Could not execute SQL insert statement for MX-Record.<br><b>MySQL Said:</b><br>" . mysql_error();
		  }
	   }
	   // Adds NS records
	   if (!($NS1_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,ZoneID) VALUES('" .
										   $_SESSION['Username'] . "','ns1','NS','" . $PGPL_BIND->NS1 . "','" . $DB_ZoneID['ZoneID'] . "')"))) {
			 $Result .= "<br>Could not execute SQL insert statement for NS1 Record.<br><b>MySQL Said:</b><br>" . mysql_error();
	   }
	   if (!($NS2_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,ZoneID) VALUES('" .
										   $_SESSION['Username'] . "','ns2','NS','" . $PGPL_BIND->NS2 . "','" . $DB_ZoneID['ZoneID'] . "')"))) {
			 $Result .= "<br>Could not execute SQL insert statement for NS2 Record.<br><b>MySQL Said:</b><br>" . mysql_error();
	   }			
	   // Call Class Function To Load The New Zone Into The DNS Server
	   if (!$PGPL_BIND->WriteMasterZone($Domain2Add,$NAMED_CONF)) { 
	       $Result .= "Could not write new master zone file. Operation aborted.";
	   } 
	   if (!$PGPL_BIND->RebuildZone($DB_ZoneID['ZoneID'])) {				
		  $Result = "Could not rebuild new master zone file. Operation aborted.";
	   }							
	   $PGPL->SudoLogout();
	   if (!$Result) {
		   echo "<script language=\"JavaScript\">alert('" . $Domain2Add . " was successfully created.');location.href = 'DNS_ViewTree.php';</script>";
	   }
  }
}
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<script language="javascript">
function VerifySlave() {
  // Check the domain
  var InvalidChars = "!@#$%^&*()+=[]\\\';,/{}|\":<>?`_+= ";
   for (var i = 0; i < document.Slave.Domain.value.length; i++) {
  	 if (InvalidChars.indexOf(document.Slave.Domain.value.charAt(i)) != -1) {
  	  alert ("The specified domain name is invalid. Domain names may only contain ASCII text, numbers, and a period.");
  	 return false;
   	 }
   }  
   if (document.Slave.Domain.value.length < 1) {
    alert ("You must enter a domain name.");
    return false;
   }
   if (!(document.Slave.Domain.value.indexOf(".") != -1) ){
    alert ("The domain entered is not a Fully Qualified Domain Name.");
    return false;
   }
  var PrimaryIP = document.Slave.PrimaryIP.value;
  if (PrimaryIP) {
    ArrIP = PrimaryIP.split(".");
	  for (i=0; i < ArrIP.length; i++) {
	     if (ArrIP[i] > 255) {
	       alert('Your primary name server address is invalid. Please check the IP address and try submitting again.');
		   return false;
	     } 
   	  }
  var InvalidChars = "!@#$%^&*()+=-[]\\\';/{}|\":<>?`_-+=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";
   for (var i = 0; i < PrimaryIP.length; i++) {
  	 if (InvalidChars.indexOf(PrimaryIP.charAt(i)) != -1) {
  	  alert ("Your name server address is invalid. Please check the IP address and try submitting again.");
  	  return false;
    }
   }
  }
   document.Slave.submit();
}
function VerifyElements() {
  // Check the domain
  var InvalidChars = "!@#$%^&*()+=[]\\\';,/{}|\":<>?`_+= ";
   for (var i = 0; i < document.ZoneInfo.Domain.value.length; i++) {
  	 if (InvalidChars.indexOf(document.ZoneInfo.Domain.value.charAt(i)) != -1) {
  	  alert ("The specified domain name is invalid. Domain names may only contain ASCII text, numbers, and a period.");
  	 return false;
   	 }
   }  
   if (document.ZoneInfo.Domain.value.length < 1) {
    alert ("You must enter a domain name.");
    return false;
   }
  
  // Check the IP Address of the web server address
  var WebIP = document.ZoneInfo.WebIP.value;
  if (WebIP) {
    ArrIP = WebIP.split(".");
	  for (i=0; i < ArrIP.length; i++) {
	     if (ArrIP[i] > 255) {
	       alert('Your web server address is invalid. Please check the IP address and try submitting again.');
		   return false;
	     } 
   	  }
  var InvalidChars = "!@#$%^&*()+=-[]\\\';,/{}|\":<>?`_-+=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";
   for (var i = 0; i < WebIP.length; i++) {
  	 if (InvalidChars.indexOf(WebIP.charAt(i)) != -1) {
  	  alert ("Your web server address is invalid. Please check the IP address and try submitting again.");
  	  return false;
    }
   }
  }
  // Check the IP Address of the FTP server address
  var FTPip = document.ZoneInfo.FTPip.value;
  if (FTPip) {
    ArrIP = FTPip.split(".");
	  for (i=0; i < ArrIP.length; i++) {
	     if (ArrIP[i] > 255) {
	       alert('Your web server address is invalid. Please check the IP address and try submitting again.');
		   return false;
	     }   
   	 }
   var InvalidChars = "!@#$%^&*()+=-[]\\\';,/{}|\":<>?`_-+=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";
     for (var i = 0; i < FTPip.length; i++) {
  	  if (InvalidChars.indexOf(FTPip.charAt(i)) != -1) {
  	    alert ("Your web server address is invalid. Please check the IP address and try submitting again.");
  	    return false; 
	  }
    }
  }
  // Check the IP Address of the mail server address
  var MailIP = document.ZoneInfo.MailIP.value;
  if (MailIP) {
    ArrIP = MailIP.split(".");
	  for (i=0; i < ArrIP.length; i++) {
	     if (ArrIP[i] > 255) {
	       alert('Your web server address is invalid. Please check the IP address and try submitting again.');
		   return false;
	     }   
   	 }
	 var InvalidChars = "!@#$%^&*()+=-[]\\\';,/{}|\":<>?`_-+=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";
      for (var i = 0; i < MailIP.length; i++) {
  	   if (InvalidChars.indexOf(MailIP.charAt(i)) != -1) {
  	    alert ("Your web server address is invalid. Please check the IP address and try submitting again.");
  	   return false;
	   }
	 }
  }
 document.ZoneInfo.submit();  
}
</script>
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699" onLoad="JavaScript:ZoneInfo.Domain.focus()">
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
          <td height="212"><table class="menu" width="100%" border="0">
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td width="18%"><?php include("CP_Navigation.php"); ?></td>
                <td colspan="3"><?php include("CenterOfAttention.php"); ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3"><table width="90%"  border="0" align="center" class="menu">
                  <tr><td class="highlight"><div id="Directions">To create a new domain on our DNS servers, you must first enter the domain name you wish to add, followed by the IP address of your web, FTP, and mail server. If you are not running a particuliar server (mail for instance), simply remove the IP from the textbox, and the associated records will be omitted when the domain is built.</div></td>
                  </tr>
                </table>                
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><p>&nbsp;</p>
                </td>
                <td colspan="2"><font color="#FF0000"><b><?php echo $Result; ?></b></font></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="22%">&nbsp;</td>
                <td colspan="2">
			   <div id="Primary">
				<form name="ZoneInfo" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=CreateZone" method="POST">
				<table width="100%"  border="0">
                  <tr class="menu">
                    <td width="21%">Domain Name: </td>
                    <td width="79%"><input name="Domain" type="text" class="highlight" id="Domain">
                 <?php 
				  if ($_GET['DomainType'] == "ThirdLevelDomain") { 
				    echo ".&nbsp;";
				    echo "<select name=\"ThirdLevelDomain\">\r\n";
				       for ($i=0; $i < count($DomainList); $i++) {
                         echo "\t\t\t<option value=\"" . $DomainList[$i] . "\">" . $DomainList[$i] . "</option>\r\n";
                       } 
					echo "\t\t\t</select>\r\n";
				  }
				 ?>
                    </td>
                  </tr>
                  <tr class="menu">
                    <td>Web Server IP: </td>
                    <td><input name="WebIP" type="text" class="highlight" id="WebIP" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>"></td>
                  </tr>
                  <tr class="menu">
                    <td>FTP Server IP: </td>
                    <td><input name="FTPip" type="text" class="highlight" id="FTPip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>"></td>
                  </tr>
                  <tr class="menu">
                    <td>Mail Server IP: </td>
                    <td><input name="MailIP" type="text" class="highlight" id="MailIP" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>"></td>
                  </tr>
                  <tr class="menu">
                    <td>Master Zone:</td>
                    <td><input name="IS_Master" type="checkbox" id="IS_Master" value="master" checked onClick="JavaScript:document.getElementById('Secondary').style.display = '';document.getElementById('Primary').style.display = 'none';  document.getElementById('IS_Slave').checked = true;document.getElementById('Directions').innerHTML = 'For slave zones, you must type the name of the zone, followed the IP address of the primary DNS server that is authoritative for this zone. If you have more than one primary DNS server, you may list them by seperating each IP with a comma (24.217.52.1,24.213.62.6). When you are finished, click create to initiate the first zone transfer.';"></td>
                  </tr>
                  <tr class="menu">
                    <td>&nbsp;</td>
                    <td><input type="button" value="Create" onClick="JavaScript:VerifyElements();"></td>
                  </tr>
                </table>
			   </form>
			  </div>
			  <div id="Secondary" style="display:none;">
			  <form name="Slave" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=CreateZone" method="POST">
			  <table width="100%"  border="0">
                  <tr class="menu">
                    <td width="25%">Domain Name</td>
                    <td width="75%"><input name="Domain" type="text" class="highlight" id="Domain"></td>
                  </tr>
                  <tr class="menu">
                    <td>Primary Server(s)</td>
                    <td><input name="PrimaryIP" type="text" class="highlight" id="PrimaryIP" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>"></td>
                  </tr>
                  <tr class="menu">
                    <td>Slave Zone:</td>
                    <td><input name="IS_Slave" type="checkbox" id="IS_Slave" value="slave" checked onClick="JavaScript:document.getElementById('Primary').style.display = '';document.getElementById('Secondary').style.display = 'none';document.getElementById('IS_Master').checked = true;document.getElementById('Directions').innerHTML = 'To create a new domain on our DNS servers, you must first enter the domain name you wish to add, followed by the IP address of your web, FTP, and mail server. If you are not running a particuliar server (mail for instance), simply remove the IP from the textbox, and the associated records will be omitted when the domain is built.';"></td>
                  </tr>
                  <tr class="menu">
                    <td>&nbsp;</td>
                    <td><input type="button" value="Create" onClick="JavaScript:VerifySlave();"></td>
                  </tr>
                </table>
			   </form>
			  </div>
			  </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td width="12%">&nbsp;</td>
                <td width="48%">&nbsp;</td>
              </tr>
            </table>
		    <p>&nbsp;</p>
            <p>&nbsp;</p>
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