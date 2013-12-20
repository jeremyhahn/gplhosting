<?php
session_start();
require("includes/GetOuttaHere.php"); 
require("includes/DB_ConnectionString.php");   
require("includes/class.pgpl.php");
require("includes/class.apache2.php");
require("includes/class.vsftpd.php");
   
$DNS_Query = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' ORDER BY Zone");
$EmailQuery = mysql_query("SELECT Email FROM Clients WHERE Username='" . $_SESSION['Username'] . "'");
$FTP_Chk = mysql_query("SELECT * FROM FTP_Records WHERE Username='" . $_SESSION['Username'] . "'");
$ArrEmail = mysql_fetch_array($EmailQuery);		

if ($_GET['Action'] == "Process") { 
   
   
     
     	    
   if ($_POST['CreateDNS'] == 1) {

		require("includes/class.bind.php");
		$Quota = explode("\r\n",$MemberPlans);			 
		foreach ($Quota as $Value) {
		  $Data = explode(",",$Value);
			if ($Data[0] == $_SESSION['SiteRole']) {
			   $NewDomainCap = $Data[1];
			  }
		 } 
		 $TotalDomains = mysql_query("SELECT Zone FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "'");
		 if (mysql_num_rows($TotalDomains) >= $NewDomainCap && $NewDomainCap !=0) { 
			echo "<script language=\"JavaScript\">alert('You have exceeded your DNS domain quota of " . $NewDomainCap . ". The website will still be created, however, you will have to make other arrangements for your DNS configurations.');</script>";
		 } else {		
			$Domain2Add = strtolower($_POST['NewDomain']);
			$ZoneType = "master";
	
			    $chkZone = mysql_query("SELECT * FROM DNS_Zones WHERE Zone='" . $Domain2Add . "'");
			    if (mysql_num_rows($chkZone) > 0) {
			      echo "<script language=\"JavaScript\">alert('" . $Domain2Add . " is already a registered domain! Please select a different zone.');history.go(-1);</script>";
			      exit();
			    }
			    mysql_query("INSERT INTO DNS_Zones(Username,Zone,Type,PS,RP,Serial,Refresh,Retry,Expire,TTL) VALUES('" . $_SESSION['Username'] . "','" . 
						  $Domain2Add . "','master','" . $PGPL_BIND->PS . "','" . $PGPL_BIND->RP . "','1','" . $PGPL_BIND->REFRESH . "','" .  $PGPL_BIND->RETRY . "','" . 
						  $PGPL_BIND->EXPIRE . "','" . $PGPL_BIND->TTL . "')");
			    if (!($ZoneID = mysql_query("SELECT * FROM DNS_Zones WHERE Zone='" . $Domain2Add . "'"))) {
				   $Result .= "<br>Could not retrieve ZoneID for new zone.<br><b>MySQL Said:</b><br>" . mysql_error();
			    } else {
				   $DB_ZoneID = mysql_fetch_array($ZoneID);
			    }
			   // Adds A-Record to the MySQL database for root domain
				if (!($A_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,ZoneID) VALUES('" .
													$_SESSION['Username'] . "','@','A','" . $ExternalWAN_IP . "','" . $DB_ZoneID['ZoneID'] . "')"))) {
				   $Result .= "<br>Could not execute SQL insert statement for A-Record.<br><b>MySQL Said:</b><br>" . mysql_error();
				}
				if (!($CNAME_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,Alias,ZoneID) VALUES('" .
													$_SESSION['Username'] . "','@','CNAME','www','" . $DB_ZoneID['ZoneID'] . "')"))) {
				   $Result .= "<br>Could not execute SQL insert statement for CNAME-Record.<br><b>MySQL Said:</b><br>" . mysql_error();
				}
				// Adds A-Record for FTP address if applicable
				if (!($FTP_Rec_Insert = mysql_query("INSERT INTO DNS_Records(Username,Hostname,RecType,RecData,ZoneID) VALUES('" .
													$_SESSION['Username'] . "','ftp','A','" . $ExternalWAN_IP . "','" . $DB_ZoneID['ZoneID'] . "')"))) {
				   $Result .= "<br>Could not execute SQL insert statement for FTP A-Record.<br><b>MySQL Said:</b><br>" . mysql_error();
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
				// Update NAMED
				if (!($SOA_RECORD = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' AND Zone='" . $Domain2Add . "'"))) {
				  $Result .= "An error occurred while attempting select query on DNS_Zones.<br><br><b>MySQL says:</b><br>" . mysql_error();
				}
				// Format The SOA Record For The MakeZoneFile() Function {DNS_functions.php}.
				$DB_SOA = mysql_fetch_array($SOA_RECORD);
				$SOA = $PGPL_BIND->FormatSOA($DB_SOA['PS'],$DB_SOA['RP'],$DB_SOA['Serial'],$DB_SOA['Refresh'],$DB_SOA['Retry'],$DB_SOA['Expire'],$DB_SOA['TTL']);
			   // Get NS Records FIRST !!!
			   if (!($NSRecords = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $DB_ZoneID['ZoneID'] . "' AND RecType='NS'"))) {
				 $Result .= "An error occurred while attempting select query on DNS_Records.<br><br><b>MySQL says:</b><br>" . mysql_error();
			   }
			   if (!($ZoneRecords = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $DB_ZoneID['ZoneID'] . "' AND RecType!='NS' ORDER BY RecType"))) {
				 $Result .= "An error occurred while attempting select query on DNS_Records.<br><br><b>MySQL says:</b><br>" . mysql_error();
			   } 					
			   // Format this zones resource NS records.
			   while ($DB_NS = mysql_fetch_array($NSRecords)) {
				 $This_NS = $PGPL_BIND->Format_RR($DB_NS['Hostname'],$DB_NS['RecType'],$DB_NS['Alias'],$DB_NS['RecData'],$DB_NS['MX_Pref']);
				 $RRs_2_Write .= $This_NS;							 
			   }
			   // Format this zones resource records (RR) which were retrieved from the database.
			   while ($DB_RR = mysql_fetch_array($ZoneRecords)) {
				 $This_RR = $PGPL_BIND->Format_RR($DB_RR['Hostname'],$DB_RR['RecType'],$DB_RR['Alias'],$DB_RR['RecData'],$DB_RR['MX_Pref']);
				 $RRs_2_Write .= $This_RR;							 
			   }
			   $ZoneFile = $SOA . $RRs_2_Write . "\n";								
			   // Add the zone to the named.conf file.
			   $PGPL->SudoLogin();
			   if (!$PGPL_BIND->WriteMasterZone($Domain2Add)) {
				 $Result .= "An error occurred while attmepting to write new master zone file. Operation aborted.";
			   }							
			   // Write the new zone file to the data directory.								
			   if (!$PGPL_BIND->WriteZoneFile($ZoneFile,$Domain2Add)) {
				  $Result .= "An error occurred while trying to update the BIND zone file. Operation aborted."; 
			   }							
			   // Reload the DNS server so that the new zone will be loaded.   
			   $PGPL_BIND->Reload();
			   $PGPL->SudoLogout();						
		 }		
	  } 
	  ## ------------------------------------------------------------------------------------------------------------------------------------>
	  $ArrApache = explode(",",$Apache2);
	  $NewDomain = strtolower($_POST['NewDomain']);
	  $Plan = explode("\r\n",$MemberPlans);			 
			    foreach ($Plan as $Value) {
				  $Data = explode(",",$Value);
 				    if ($Data[0] == $_SESSION['SiteRole']) {
					   $WebsiteCap = $Data[8];
					   $DiskUsageCap = $Data[10];
					   $BandwidthCap = $Data[11];
					  }
		         }
	 if (!($TotalSites = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "'"))) {
	  echo mysql_error();
	 }
	 if (mysql_num_rows($TotalSites) >= $WebsiteCap && $WebsiteCap !=0) { 
		echo "<script language=\"JavaScript\">alert('You have exceeded your website quota of " . $WebsiteCap . ". Operation aborted.');location.href = 'WEB.php';</script>";
		exit();
	 }
     $DOC_ROOT = $ArrApache[0] . $_SESSION['Username'] . "/" . $NewDomain;
	 $LOG_FILE = $ArrApache[1] . $_SESSION['Username'] . "/" . $NewDomain . "/access_log";	 
     if (!($DirChk = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "' AND ServerName='" . $NewDomain . "'"))) {
        $Result .= "Could not execute SQL query to verify uniqueness of new website name.<br>";
     } else {
        if (mysql_num_rows($DirChk) == 0) {	
	       if (mysql_num_rows($FTP_Chk) == 0) { mysql_query("INSERT INTO FTP_Records(Username,Password,Owner,DaRoot,Unique_ID) VALUES('" . $_SESSION['Username'] . "','" . md5($_POST['Password']) . "','" . $_SESSION['Username'] . "','1','" . md5(mktime()) . "')"); }
		      if (!($Insert = mysql_query("INSERT INTO HTTP_Records(Username,DocumentRoot,NameVhost,ServerName,ServerAlias,ServerAdmin,Logfile,DiskQuota,BandwidthQuota) VALUES('" . 
			                              $_SESSION['Username'] . "','" . $DOC_ROOT . "','" . $PGPL_Apache2->NameVhost . "','" . 
										  $NewDomain . "','www." . $NewDomain . "','" . $_POST['AdminEmail'] . "','" . $LOG_FILE . "','" . 
										  $DiskUsageCap . "','" . $BandwidthCap . "')"))) {
			     $Result .= "Could not create database records for the new website <i>$NewDomain</i>.<br><b>MySQL Said:</b><br>" . mysql_error() . "<br>";
		      }
			  $PGPL->SudoLogin();
			  // Create account holder home directory
			  $PGPL_FTP->HomeDir = $ArrApache[0] . $_SESSION['Username'];
			  if (!is_dir($PGPL_FTP->HomeDir)) {
				 if (!$PGPL_FTP->CreateDirectory("777")) {
					$Result .= "An error occurred while attempting to create the account holder home directory located at  " . $PGPL_FTP->HomeDir . ".";;
				 }
			  }
			  // Create the directory for the new website
			  $PGPL_FTP->HomeDir = $ArrApache[0] . $_SESSION['Username'] . "/" . $NewDomain;
			  if (!is_dir($PGPL_FTP->HomeDir)) {
				 if (!$PGPL_FTP->CreateDirectory("777")) {
					$Result .= "An error occurred while attempting to create your new website directory located at " . $PGPL_FTP->HomeDir . ".";;
				 }
			  }
		      // Create a directory where statistics output is generated to
			  $PGPL_FTP->HomeDir = $ArrApache[0] . $_SESSION['Username'] . "/" . $NewDomain . "/Statistics";
			  if (!is_dir($PGPL_FTP->HomeDir)) {
		         if (!$PGPL_FTP->CreateDirectory("776")) {
			        $Result .= "An error occurred while attempting to create the statistics directory located at " . $PGPL_FTP->HomeDir . ".";
		         }
			  }
		      // Create log home directory
			  $PGPL_FTP->HomeDir = $ArrApache[1] . $_SESSION['Username'];
			  if (!is_dir($PGPL_FTP->HomeDir)) {
		         if (!$PGPL_FTP->CreateDirectory("774")) {
			        $Result .= "An error occurred while attempting to create the account holders log directory located at " . $PGPL_FTP->HomeDir;                                          
		         }
			  }
			  // Create log directory for new website
			  $PGPL_FTP->HomeDir = $ArrApache[1] . $_SESSION['Username'] . "/" . $NewDomain;
			  if (!is_dir($PGPL_FTP->HomeDir)) {
		         if (!$PGPL_FTP->CreateDirectory("774")) {
			        $Result .= "An error occurred while attempting to create a log directory located at " . $PGPL_FTP->HomeDir . ".";
		         }
			  }
			  // Copy custom html to new website if preferences allow
		      if ($ArrApache[7] == 1) {
			     $ThisPath = getcwd();
			     $PGPL_FTP->HomeDir = $ThisPath . "/Custom/html";
			     $PGPL_FTP->CopyContents($ArrApache[0] . $_SESSION['Username'] . "/" . $NewDomain);
		      }
			  // Create the account holder
		      if (mysql_num_rows($FTP_Chk) == 0) {
			     $PGPL_FTP->Username = $_SESSION['Username'];
			     $PGPL_FTP->Password = $_POST['Password'];
			     $PGPL_FTP->HomeDir = $ArrApache[0] . $_SESSION['Username'];
			     if (!$PGPL_FTP->CreateUser(1)) {
				    $Result .= "An error occurred while attempting to create a new FTP account holder login for " . $PGPL_FTP->Username . ".";
			     }
			  }
		      $Vhost = $PGPL_Apache2->FormatVhost($NewDomain,$ArrApache[3],$NewDomain,"www.".$NewDomain,$DOC_ROOT,$_POST['AdminEmail'],str_replace("\r\n","\n\t",$ApacheCustDirectives),$ArrApache[1] . $_SESSION['Username'] . "/" . $NewDomain . "/");
		      /*
		       START WEBALIZER
		      */
		      $LogFile = "LogFile\t" . $ArrApache[1] . $_SESSION['Username'] . "/" . $NewDomain . "/access_log";
		      $OutputDir = "OutputDir\t" . $ArrApache[0] . $_SESSION['Username'] . "/" . $NewDomain . "/Statistics";
		      $HistoryName = "HistoryName\t" . $ArrApache[0] . $_SESSION['Username'] . "/" . $NewDomain . "/Statistics/webalizer.history";
		      $HostName = "HostName\t" . $NewDomain;
		      $DNS_Cache = "DNSCache\t" . $ArrApache[0] . $_SESSION['Username'] . "/" . $NewDomain . "/Statistics/dns_cache.db";
		      $Directives = $LogFile . "\n" . $OutputDir . "\n" . $HistoryName . "\n" . $DNS_Cache . "\n" . $HostName . "\n"; 
		      $ConfFile = $WebalizerHome . "/" . $NewDomain . ".conf";
			  $Row = explode("\r\n",$WebalizerConf);			 
				foreach ($Row as $Value) {
				  $Data = explode(",",$Value);
				  if (!($Data[2])) {
					$ThisEntry = $Data[0] . "\t" . $Data[1] . "\n";
				  } else {
					$ThisEntry = $Data[0] . "\t" . $Data[1] . "\t" . $Data[2] . "\n";
				  }
				  $Directives .= $ThisEntry;
				}				
					if (!$NewConfig = fopen($ConfFile, 'w')) {
						 $Result .= "Could not open $Conf file.";						 
					}
					if (!fwrite($NewConfig, $Directives . "\n")) {
						$Result .= "Could not write to $ConfFile";						
					}
					fclose($NewConfig); 
			   /*
				  END WEBALIZER
			   */
			   if (!$PGPL_Apache2->AddVhost($NewDomain,$Vhost)) {
				  $Result .= "An error occurred while trying to create the new virtual host website. Opeation aborted.";
			   } else {
				  if (!$PGPL_Apache2->ApacheGraceful()) {
				     $Result .= "An error occurred while trying to load your new website into the Apache web server configuration file.";
				  } else {
				     if (!$Result) { echo "<script language=\"JavaScript\">location.href = 'WEB.php';</script>"; }
				  }
			   }			  
	      } else {
		    $Result .= "This website already exists in your account.";
	   } 
     }   
} 
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">

<?php if (mysql_num_rows($FTP_Chk) == 0) { ?>
<script language="javascript">
function ValidateElements() {
  // Check the domain
  if (document.getElementById('CreateDNS').value == 1) {
	  var InvalidChars = "!@#$%^&*()+=[]\\\';,/{}|\":<>?`_+= ";
	   for (var i = 0; i < document.NewWebsite.NewDomain.value.length; i++) {
		 if (InvalidChars.indexOf(document.NewWebsite.NewDomain.value.charAt(i)) != -1) {
		  alert ("The specified domain name is invalid. Domain names may only contain ASCII text, numbers, and a period.");
		 return false;
		 }
	   }  
	   if (!(document.NewWebsite.NewDomain.value.indexOf(".") != -1) ){
		alert ("The domain entered is not a Fully Qualified Domain Name.");
		return false;
	   }
	   if (document.NewWebsite.NewDomain.value.length < 1) {
		alert ("You must enter a domain name.");
		return false;
	   }
   }
   // Checks the email address
   i=document.NewWebsite.AdminEmail.value.indexOf("@")
   j=document.NewWebsite.AdminEmail.value.indexOf(".",i)
   k=document.NewWebsite.AdminEmail.value.indexOf(",")
   kk=document.NewWebsite.AdminEmail.value.indexOf(" ")
   jj=document.NewWebsite.AdminEmail.value.lastIndexOf(".")+1
   len=document.NewWebsite.AdminEmail.value.length
   if ((i>0) && (j>(1+1)) && (k==-1) && (kk==-1) && (len-jj >=2) && (len-jj<=3)) {
   } else {
 	alert("Please enter a valid Email address. This Email address will be used by the web server in the event of an error.");
	return false;
   }
   // Validate Password
   if (document.NewWebsite.Password.value == '') {
    alert('We do not allow blank passwords on our network!');
    return false;
   }
   if (document.NewWebsite.Password.value != document.NewWebsite.Password2.value) {
    alert('Passwords do not match!');
    return false;
   }    
 document.NewWebsite.submit();
}
</script>
<?php } else { ?>
<script language="javascript">
function ValidateElements() {
  // Check the domain
 if (document.getElementById('CreateDNS').value == 1) {
	  var InvalidChars = "!@#$%^&*()+=[]\\\';,/{}|\":<>?`_+= ";
	   for (var i = 0; i < document.NewWebsite.NewDomain.value.length; i++) {
		 if (InvalidChars.indexOf(document.NewWebsite.NewDomain.value.charAt(i)) != -1) {
		  alert ("The specified domain name is invalid. Domain names may only contain ASCII text, numbers, and a period.");
		 return false;
		 }
	   }  
	   if (!(document.NewWebsite.NewDomain.value.indexOf(".") != -1) ){
		alert ("The domain entered is not a Fully Qualified Domain Name.");
		return false;
	   }
	   if (document.NewWebsite.NewDomain.value.length < 1) {
		alert ("You must enter a domain name.");
		return false;
	   }
   }
   // Checks the email address
   i=document.NewWebsite.AdminEmail.value.indexOf("@")
   j=document.NewWebsite.AdminEmail.value.indexOf(".",i)
   k=document.NewWebsite.AdminEmail.value.indexOf(",")
   kk=document.NewWebsite.AdminEmail.value.indexOf(" ")
   jj=document.NewWebsite.AdminEmail.value.lastIndexOf(".")+1
   len=document.NewWebsite.AdminEmail.value.length
   if ((i>0) && (j>(1+1)) && (k==-1) && (kk==-1) && (len-jj >=2) && (len-jj<=3)) {
   } else {
 	alert("Please enter a valid Email address. This Email address will be used by the web server in the event of an error.");
	return false;
   }    
 document.NewWebsite.submit();
}
</script>
<?php } ?>
<script language="javascript">
function ToggleDiv(Value) {
  if (Value == "Other") { 
    document.getElementById('DomainSelection').innerHTML = '';
	document.getElementById('DomainEntry').style.display = '';
	document.getElementById('NewEntry').value = 1;
    var BuildDNS = confirm("Would you like to have a DNS domain name automatically configured for this website on our DNS servers?");
	    if (BuildDNS == true) {
		  document.getElementById('CreateDNS').value = 1;
		} else { return false; }
  }
}
function BuildDNS() {
  var BuildDNS = confirm("Would you like to have a DNS domain name automatically configured for this website on our DNS servers?");
	    if (BuildDNS == true) {
		  document.getElementById('CreateDNS').value = 1;
		} else { return false; }
}
</script>
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
                <td colspan="3"><div align="center"><font color="#FF0000"><b><?php echo $Result; ?></b></font></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3"><div align="center" class="highlight">
                  <table width="90%"  border="0" class="menu">
                    <tr>
                      <td class="highlight">Please type the domain name (yourdomain.com) that internet users will use to get to your website into the first textbox. The admin e-mail, will allow your visitors a way of contacting you in case of a server error (such as an infamous 404 not found error). By default, the drop-down box lists all of your configured DNS A-Records for your convienece. If you want to add a new domain, simply choose 'other', and tell the system to automatically configure the domain. </td>
                    </tr>
                  </table>
                </div>                  
                  </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="22%">&nbsp;</td>
                <td width="37%">
				<form name="NewWebsite" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Process" method="post">
				<input type="hidden" id="CreateDNS" name="CreateDNS" value="0">
				 <table width="100%" border="1" bordercolor="#F9F9F9" class="menu">
                  <tr>
                    <td>Your Domain:</td>
					<?php if (mysql_num_rows($DNS_Query) == 0) { ?>
                      <td> 
					     <input name="NewDomain" type="text" id="NewDomain" size="25">
						 <script language="javascript">BuildDNS()</script>
					  </td>
					<?php } else { ?>
					  <td>
					    <div id="DomainEntry" style="display:none;"><input type="hidden" id="NewEntry" name="NewEntry" value="0"><input name="NewDomain" type="text" id="NewDomain" size="25"></div>
						<div id="DomainSelection">
						  <select name="NewDomain" onChange="ToggleDiv(this.value)">
						   <?php 
						         while ($ThisZone = mysql_fetch_array($DNS_Query)) {
								    $DNS_Rec_Query = mysql_query("SELECT * FROM DNS_Records WHERE ZoneID='" . $ThisZone['ZoneID'] . "' AND RecType='A' ORDER BY Hostname");
								    while ($ThisRec = mysql_fetch_array($DNS_Rec_Query)) {
									   $FQDN = $ThisRec['Hostname'] . "." . $ThisZone['Zone'];									  
									   $FQDN = str_replace("@","",$FQDN);
									   if (!($ThisRec['Hostname'] == "ftp" || $ThisRec['Hostname'] == "mail")) {
									       if ($FQDN{0} == ".") {
										       $FQDN = substr_replace($FQDN,"",0,1);											
									           echo "<option value=\"" . $FQDN . "\">" . $FQDN . "</option>";
										   } else {
										       echo "<option value=\"" . $FQDN . "\">" . $FQDN . "</option>";
										   }									 
									   }
									}
								 }
						   ?>
						    <option value="Other">Other ...</option>
						  </select>
						</div>
					<?php } ?>				  
				   </td>
                  </tr>
                  <tr>
                    <td>Admin Email:</td>
                    <td><font class="menu"><input name="AdminEmail" type="text" id="AdminEmail" value="<?php echo $ArrEmail['Email']; ?>" size="25">
                    </font></td>
                  </tr>				  
                  <tr>
                    <td>FTP Login: </td>
                    <td><strong><?php echo $_SESSION['Username']; ?></strong></td>
                  </tr>
				    <?php if (mysql_num_rows($FTP_Chk) == 0) { ?>
                  <tr>
                    <td>FTP Password: </td>
                    <td><input name="Password" type="password" id="Password" size="25"></td>
                  </tr>
                  <tr>
                    <td width="43%">Confirm: </td>
                    <td><input name="Password2" type="password" id="Password2" size="25"></td>
                  </tr>
					<?php } ?>
                  <tr>
                      <td>&nbsp;</td>
                      <td width="57%"><input type="button" value="Create" onClick="JavaScript:ValidateElements();"></td>
                  </tr>
                </table>
			   </form>
				</td>
                <td width="23%">&nbsp;</td>
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