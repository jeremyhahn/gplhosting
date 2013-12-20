<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");
require("includes/class.bind.php");


 if (isset($_GET['Action']) && $_GET['Action'] == "Update") {   
	 
     switch ($_POST['RecType']) {
	 
	 case "A":
	  if (!($Existence = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND Hostname='" . 
		        $_POST['Hostname'] . "' AND RecType='" . $_POST['RecType'] . "'AND RecData='" . $_POST['IP_Address'] . "' AND ZoneID='" . $_POST['ZoneID'] . "'"))) {
		$Result = "Could not verify the uniqueness of the new record against the database.<br><b>MySQL Said:</b><br>" . mysql_error();
	  } else {
		if (mysql_num_rows($Existence) > 0) {
		   $SQL = mysql_fetch_array($Existence);
		   echo "<script language=\"JavaScript\">alert('This A-Record already exists in your account!'); location.href = 'DNS_ViewTree.php?OnLoad=" . $SQL['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
		   exit();
		}
	  }
      if (!($UpdateHost = mysql_query("UPDATE DNS_Records Set Hostname='" . $_POST['Hostname'] . "' WHERE RecID='" . $_POST['RecID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	    $Result = "Could not update the requested A record at this time. Please contact our support team to notify us of this internal error.";
	  } else {
		 if (!($UpdateIP = mysql_query("UPDATE DNS_Records Set RecData='" . $_POST['IP_Address'] . "' WHERE RecID='" . $_POST['RecID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
		   $Result = "Could not update A record at this time. Please contact our support team to notify us of this issue.";
		 } else {
		   $PGPL->SudoLogin();
		   if (!$PGPL_BIND->RebuildZone($_POST['ZoneID'])) {
			  $Result .= "An error occurred while trying to rebuild the requested zone.";
		   }
		   $PGPL->SudoLogout();
		   if (!$Result) {
			  echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php?OnLoad=" . $_POST['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
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
	 if (!($CNAME_Existence = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND Hostname='" . 
		        $Hostname . "' AND Alias='" . $_POST['Alias'] . "' AND ZoneID='" . $_POST['ZoneID'] . "'"))) {
	   $Result = "Could not verify the uniqueness of the new record against the database.<br><b>MySQL Said:</b><br>" . mysql_error();
	 } else {
	   if (mysql_num_rows($CNAME_Existence) > 0) {
		  $SQL = mysql_fetch_array($CNAME_Existence);
		  echo "<script language=\"JavaScript\">alert('This specified alias name already exists in your account!'); location.href = 'DNS_ViewTree.php?OnLoad=" . $SQL['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
		  exit();
	   }
	 }
     if (!($UpdateAlias = mysql_query("UPDATE DNS_Records Set Alias='" . $_POST['Alias'] . "' WHERE RecID='" . $_POST['RecID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	   $Result = "Could not update the requested CNAME record at this time. Please contact our support team to notify us of this internal error.";
	 } else {	              
 	   if (!($UpdateHost = mysql_query("UPDATE DNS_Records Set Hostname='" . $Hostname . "' WHERE RecID='" . $_POST['RecID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
		 $Result = "Could not update requested CNAME record at this time. Please contact our support team to notify us of this issue.";
	   } else {
		 $PGPL->SudoLogin();
		 if (!$PGPL_BIND->RebuildZone($_POST['ZoneID'])) {
			$Result .= "An error occurred while trying to rebuild the requested zone.";
		 }
		 $PGPL->SudoLogout();
		 if (!$Result) {
		    echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php?OnLoad=" . $_POST['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
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
     if (!($MX_Existence = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND Hostname='" . 
		        $_POST['Hostname'] . "' AND RecData='" . $_POST['RecData'] . ".' AND ZoneID='" . $_POST['ZoneID'] . "'"))) {
	   $Result = "Could not verify the uniqueness of the new record against the database.<br><b>MySQL Said:</b><br>" . mysql_error();
	 } else {
	   if (mysql_num_rows($MX_Existence) > 0) {
	      $SQL = mysql_fetch_array($MX_Existence);
		  echo "<script language=\"JavaScript\">alert('The specified MX record already exists in your account!'); location.href = 'DNS_ViewTree.php?OnLoad=" . $SQL['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
		  exit();
	   }
	 }
     if (!($UpdateMXhost = mysql_query("UPDATE DNS_Records Set Hostname='" . $_POST['Hostname'] . "' WHERE RecID='" . $_POST['RecID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	   $Result = "Could not update the requested CNAME record at this time. Please contact our support team to notify us of this internal error.";
	 } else {	              
	   if (!($UpdateMXdata = mysql_query("UPDATE DNS_Records Set RecData='" . $RecData . "' WHERE RecID='" . $_POST['RecID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
		 $Result = "Could not update requested CNAME record at this time. Please contact our support team to notify us of this issue.";
	   } else {				 
	     if (!($UpdateMXpref = mysql_query("UPDATE DNS_Records Set MX_Pref='" . $_POST['Preference'] . "' WHERE RecID='" . $_POST['RecID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
		   $Result = "Could not update requested CNAME record at this time. Please contact our support team to notify us of this issue.";
		 } else {				
	       $PGPL->SudoLogin();
		   if (!$PGPL_BIND->RebuildZone($_POST['ZoneID'])) {
		      $Result .= "An error occurred while trying to rebuild the requested zone.";
		  }
	      $PGPL->SudoLogin();
		  if (!$Result) {
		     echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php?OnLoad=" . $_POST['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
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
	if (!($NS_Existence = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "'AND RecData='" . 
		                              $_POST['RecData'] . "' AND RecType='" . $_POST['RecType'] . "' AND ZoneID='" . $_POST['ZoneID'] . "'"))) {
 	   $Result = "Could not verify the uniqueness of the new record against the database.<br><b>MySQL Said:</b><br>" . mysql_error();
	} else {
	  if (mysql_num_rows($NS_Existence) > 0) {
		 $SQL = mysql_fetch_array($NS_Existence);
		 echo "<script language=\"JavaScript\">alert('The specified NS record already exists in your account!'); location.href = 'DNS_ViewTree.php?OnLoad=" . $SQL['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
		 exit();
	  }
    }
    if (!($UpdateAlias = mysql_query("UPDATE DNS_Records Set RecData='" . $RecData . "' WHERE RecID='" . $_POST['RecID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	   $Result = "Could not update the requested CNAME record at this time. Please contact our support team to notify us of this internal error.";
	} else {	
	  $PGPL->SudoLogin();            
	  if (!$PGPL_BIND->RebuildZone($_POST['ZoneID'])) {
		 $Result .= "An error occurred while trying to rebuild the requested zone.";
	  }
	  $PGPL->SudoLogout();
	  if (!$Result) {
         echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php?OnLoad=" . $_POST['RecID'] . "," . $_POST['ZoneID'] . "';</script>";
 	  }
	}				 
	break;	  
	  
   case "slave":
   if (substr($_POST['PrimaryIP'],-1) == ",") {
	  $IPs = substr_replace($_POST['PrimaryIP'],"",strlen($IPs)-1);
   } else {
	  $IPs = $_POST['PrimaryIP'];
   }
   if (!($SlaveQuery = mysql_query("SELECT * FROM DNS_Zones WHERE ZoneID='" . $_POST['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	  $Result .= "Could not delete records from the database.";
   } else {
     $ZoneSQL = mysql_fetch_array($SlaveQuery);		   
     mysql_query("UPDATE DNS_Zones Set PS='" . $IPs . "' WHERE ZoneID='" . $_POST['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'");		   
     $PGPL->SudoLogin();
     $PGPL_BIND->DeleteZone($_POST['Domain'],$ZoneSQL['PS']);
     $PGPL_BIND->DeleteZoneFile($_POST['Domain']);
     if (!$PGPL_BIND->WriteSlaveZone($_POST['Domain'],$IPs . ",")) {
	    $Result .= "An error occurred while trying to rebuild the requested zone.";
     }
     $PGPL->SudoLogout();			
     if (!$Result) {
	    echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php';</script>"; exit();
     }			
   }
   break;	   
	 
   default:
   echo "How'd you do that?";
   exit();
   break;
 } 
} 
	 
  if (isset($_GET['Action']) && $_GET['Action'] == "Delete") {  
	  // Delete a slave zone
	  if (isset($_GET['Zone'])) {
	      if (!($DeleteQuery = mysql_query("DELETE FROM DNS_Zones WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
			 $Result = "Could not execute delete query.<br><b>MySQL Said:</b><br>" . mysql_error();
		  } else {
			 $PGPL->SudoLogin(); 	  
			 $PGPL_BIND->DeleteZone($_GET['Zone']);
			 $PGPL_BIND->DeleteZoneFile($_GET['Zone']);
			 $PGPL->SudoLogout(); 
			 if (!$Result) {
				echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php';</script>";
			 }
		  }	  
	  }
	  if (!($DeleteQuery = mysql_query("DELETE FROM DNS_Records WHERE RecID='" . $_GET['RecID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	     $Result = "Could not execute delete query.<br><b>MySQL Said:</b><br>" . mysql_error();
	  } else {
	     $PGPL->SudoLogin(); 	  
	     if (!$PGPL_BIND->RebuildZone($_GET['ZoneID'])) {
		    $Result .= "An error occurred while trying to rebuild the requested zone.";
	     } 
	     $PGPL->SudoLogout(); 
	     if (!$Result) {
		    echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php';</script>";
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
function DeleteDomain(Zone,ZoneID) {
 location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?Action=Delete&Zone='+Zone+'&ZoneID='+ZoneID;
}
function DeleteRec(RecID,ZoneID) {
 location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?Action=Delete&RecID='+RecID+'&ZoneID='+ZoneID;
}
function VerifyElements(RecType) {
  
   switch (RecType) {
   
   case "A":
		// Check the hostname
		var InvalidChars = "!#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
		for (var i = 0; i < document.A_Record.Hostname.value.length; i++) {
		 if (InvalidChars.indexOf(document.A_Record.Hostname.value.charAt(i)) != -1) {
		  alert ('The specified hostname is invalid. Hostnames may only contain ASCII text, numbers, and a period (.), unless specifying the root (@) or parent domain.');
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
		  alert ('The alias is invalid. Alias names may only contain ASCII text, numbers, and a period.');
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
        // Check the hostname
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
   
   case "SlaveZone":
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
          <td width="100%" height="212"><table class="menu" width="100%" border="0">
              <tr>
                <td width="18%"><?php include("CP_Navigation.php"); ?></td>
                <td colspan="3"><p>&nbsp;</p>
                <?php include("CenterOfAttention.php"); ?></td>
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
                <td width="2%">&nbsp;</td>
                <td width="77%">
				<?php
				if (!($ZoneQuery = mysql_query("SELECT Zone FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $_GET['ZoneID'] . "'"))) {
				 echo "Could not execute SQL query for zone name";
				} else {
				 $ThisZone = mysql_fetch_array($ZoneQuery);
				}
				
				
				 switch($_GET['RecType']) {
				 
				 case "A":
				 if (!($A_Rec_Query = mysql_query("SELECT * FROM DNS_Records WHERE RecID='" . $_GET['RecID'] . "' AND RecType='" . $_GET['RecType'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
				  echo "SQL query failed for A-Record.<br><b>MySQL Said</b><br>" . mysql_error();
				 } else { $ThisA = mysql_fetch_array($A_Rec_Query); }
				?>
				<form name="A_Record" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update">                
                    <table width="100%"  border="0" class="menu">
                      <tr>
                        <td colspan="2"><div align="center">
                          <p class="highlight">An A-Record maps an IP address to a Fully Qualified Domain Name (FQDN). </p>
 
						  <p class="style2">NOTE: The '@' symbol is the character used to represent your root domain '<?php echo $ThisZone['Zone']; ?>'. </p>

						  <br>
					    </div></td>
                      </tr>
                      <tr>
                        <td width="15%">Hostname:</td>
                        <td width="85%"><input name="Hostname" type="text" class="highlight" value="<?php echo $ThisA['Hostname']; ?>">.<?php echo $ThisZone['Zone']; ?></td>
                      </tr>
                      <tr>
                        <td>IP Address: </td>
                        <td><input name="IP_Address" type="text" class="highlight" value="<?php echo $ThisA['RecData']; ?>"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>
                          <div align="left">
                            <input type="button" value="Update" onClick="JavaScript:VerifyElements('A');">
                            <input name="Delete" type="button" value="Delete" onClick="JavaScript:DeleteRec('<?php echo $ThisA['RecID']; ?>','<?php echo $ThisA['ZoneID'] ?>');">
                          </div></td></tr>
                    </table>
					<input type="hidden" name="RecID" value="<?php echo $_GET['RecID']; ?>">
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">    
					<input type="hidden" name="RecType" value="<?php echo $_GET['RecType']; ?>">          
                </form>				
				<?php
				break;
				
				case "CNAME":
				if (!($CNAME_Rec_Query = mysql_query("SELECT * FROM DNS_Records WHERE RecID='" . $_GET['RecID'] . "' AND RecType='" . $_GET['RecType'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
				  echo "SQL query failed for A-Record.<br><b>MySQL Said</b><br>" . mysql_error();
				 } else { $ThisCNAME = mysql_fetch_array($CNAME_Rec_Query); }
				?>
				<form name="CNAME_Record" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update">
				 <table width="100%"  border="0" class="menu">
                  <tr>
                    <td colspan="2"><div align="center">
                      <p class="highlight">CNAME records are 'alias' records for an existing host.</p>
                      <p><span class="style2">Example:</span> <span class="highlight">An alias record of www for a hostname of web1 would allow you to type in www.yourdomain.com and get to web1.yourdomain.com. </span></p>
                    </div></td>
                    </tr>
                  <tr>
                    <td width="18%">Alias: </td>
                    <td width="82%"><input name="Alias" type="text" class="highlight" value="<?php echo $ThisCNAME['Alias']; ?>"></td>
                  </tr>
                  <tr>
                    <td>For Hostname:</td>
                    <td><input type="text" name="Hostname" class="highlight" value="<?php echo $ThisCNAME['Hostname']; ?>"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Update" onClick="JavaScript:VerifyElements('CNAME');">
                      <input name="Delete" type="button" value="Delete" onClick="JavaScript:DeleteRec('<?php echo $ThisCNAME['RecID']; ?>','<?php echo $ThisCNAME['ZoneID'] ?>');"></td>
                  </tr>
                </table>             
					<input type="hidden" name="RecID" value="<?php echo $_GET['RecID']; ?>">
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
					<input type="hidden" name="RecType" value="<?php echo $_GET['RecType']; ?>">
                </form>				
				<?php
				break;
				
				
				case "MX":
				if (!($MX_Rec_Query = mysql_query("SELECT * FROM DNS_Records WHERE RecID='" . $_GET['RecID'] . "' AND RecType='" . $_GET['RecType'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
				  echo "SQL query failed for A-Record.<br><b>MySQL Said</b><br>" . mysql_error();
				 } else { $ThisMX = mysql_fetch_array($MX_Rec_Query); }
				?>
				<form name="MX_Record" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update">
				 <table width="100%"  border="0" class="menu">
                  <tr>
                    <td colspan="2"><div align="center">
                      <p><span class="highlight">MX records are mail exchange records, which are used to let the internet know where your authoritative mail servers are located. If you have a backup mail server location, you may specify a higher preference for each additional mail server. If a failure occurs, your mail will be redirected to the next server in the list with the next highest preference level.</span><br>
                        </p> 
                    </p>
                      </div></td>
                    </tr>
                  <tr>
                    <td width="18%">Hostname:</td>
                    <td width="82%"><input name="Hostname" type="text" class="highlight" value="<?php echo $ThisMX['Hostname']; ?>">.<?php echo $ThisZone['Zone']; ?></td>
                  </tr>
                  <tr>
                    <td>Mail Server: </td>
                    <td><input type="text" name="RecData" class="highlight" value="<?php echo $ThisMX['RecData']; ?>"></td>
                  </tr>
                  <tr>
                    <td>Preference:</td>
                    <td><input type="text" name="Preference" class="highlight" value="<?php echo $ThisMX['MX_Pref']; ?>"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Update" onClick="JavaScript:VerifyElements('MX');">
                      <input name="Delete" type="button" value="Delete" onClick="JavaScript:DeleteRec('<?php echo $ThisMX['RecID']; ?>','<?php echo $ThisMX['ZoneID'] ?>');"></td>
                  </tr>
                </table>             
					<input type="hidden" name="RecID" value="<?php echo $_GET['RecID']; ?>">
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
					<input type="hidden" name="RecType" value="<?php echo $_GET['RecType']; ?>">
                </form>				
				<?php
				break;
				
				
				case "NS":
				if (!($NS_Rec_Query = mysql_query("SELECT * FROM DNS_Records WHERE RecID='" . $_GET['RecID'] . "' AND RecType='" . $_GET['RecType'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
				  echo "SQL query failed for A-Record.<br><b>MySQL Said</b><br>" . mysql_error();
				 } else { $ThisNS = mysql_fetch_array($NS_Rec_Query); }
				?>
				<form name="NS_Record" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update">
				 <table width="100%"  border="0" class="menu">
                  <tr>
                    <td colspan="2"><div align="center">
                      <p><span class="highlight">NS records let the internet know where your authoritative Name Servers are located.</span><br>
                        </p> 
                    </p>
                      </div></td>
                    </tr>
                  <tr>
                    <td width="14%">Name Server: </td>
                    <td width="86%"><input name="RecData" type="text" class="highlight" value="<?php echo $ThisNS['RecData']; ?>"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Update" onClick="JavaScript:VerifyElements('NS');">
                      <input name="Delete" type="button" value="Delete" onClick="JavaScript:DeleteRec('<?php echo $ThisNS['RecID']; ?>','<?php echo $ThisNS['ZoneID'] ?>');"></td>
                  </tr>
                </table>             
					<input type="hidden" name="RecID" value="<?php echo $_GET['RecID']; ?>">
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
					<input type="hidden" name="RecType" value="<?php echo $_GET['RecType']; ?>">
                </form>				
				<?php
				break;
				
				
				case "slave":
				if (!($SlaveQuery = mysql_query("SELECT * FROM DNS_Zones WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Type='" . $_GET['RecType'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
				  echo "SQL query failed for A-Record.<br><b>MySQL Said</b><br>" . mysql_error();
				 } else { 
				   $ThisSlave = mysql_fetch_array($SlaveQuery); 
				   if (substr($ThisSlave['PS'],-1) == ",") {
					  $IPs = substr_replace($ThisSlave['PS'],"",strlen($IPs)-1);
					} else {
					  $IPs = $_POST['PrimaryIP'];
					}  
				 }
				?>
			  <div id="UpdateView">
				<form name="Slave" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update">
				 <table width="100%"  border="0" class="menu">
                  <tr>
                    <td colspan="3"><div align="center">
                      <p>The only editable information here is your primary server IP list. Please don't forget to sperate each value with a comma. (64.234.25.62<span class="style2">,</span>24.234.232.34)<br>
                        </p>
                      </p>
                      </div></td>
                    </tr>                  
				  <tr>
                    <td>&nbsp;</td>
                    <td width="22%">Zone</td>
                    <td width="69%"><input name="Zone" type="text" class="highlight" id="SlaveZone" value="<?php echo $ThisSlave['Zone']; ?>"> 
                      </td>
                  </tr>
                  <tr>
                    <td width="9%">&nbsp;</td>
                    <td>Primary Server IPs </td>
                    <td><input name="PrimaryIP" type="text" class="highlight" id="PrimaryIP" value="<?php echo $IPs; ?>"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Update" onClick="JavaScript:VerifyElements('SlaveZone');"> <input type="button" value="Delete" name="Delete" onClick="JavaScript:DeleteDomain('<?php echo $ThisSlave['Zone']; ?>','<?php echo $ThisSlave['ZoneID']; ?>')"></td>
                  </tr>
                </table>             
					<input type="hidden" name="Domain" value="<?php echo $ThisSlave['Zone']; ?>">
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
					<input type="hidden" name="RecType" value="<?php echo $_GET['RecType']; ?>">
                </form>			
			   </div>
			   <div id="DataView" style="display:none;">
			   <?php
			        $ArrBIND = explode(",",$BIND);
					
					// chmod($ArrBIND[1] . $ThisSlave['Zone'] . ".zone", 0744);
			        $Zonefile = fopen($ArrBIND[1] . $ThisSlave['Zone'] . ".zone","r");
			  ?>
			    <table border="0" align="center" width="100%">
				<tr>
					<td width="100%">
			   <?php 
			         while ($Data = fgets($Zonefile)) {
			           echo str_replace("\n","<br>",$Data); 
					 }
					 fclose($Zonefile);
			   ?></td>
			    </tr>
				<tr>
				  <input value="<~~ Back" type="button" onClick="JavaScript:document.getElementById('UpdateView').style.display = '';document.getElementById('DataView').style.display = 'none';">
				</tr>
				</table>
			   </div>	
				<?php
				break;
				
				default: 
				  ?>
				  <div align="center"><font color="#FF0000"><b><?php echo $Result; ?></b></font></div>
				  <?php
				  break;
				}
				?>
				</td>
                <td width="3%">&nbsp;</td>
              </tr>
            </table>
			<p>&nbsp;</p>
            <p>&nbsp;</p>
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