<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/GlobalConfigs.php");

if ($_GET['Action'] == "CreatePOP3Domain") {
 require("includes/XMailCTRL.php");
 $Quota = explode("\r\n",$MemberPlans);
	foreach ($Quota as $Value) {
	  $Data = explode(",",$Value);
		if ($Data[0] == $_SESSION['SiteRole']) {
		   
		   $Pop3Cap = $Data[3];
		   $MailboxSizeQuota = $Data[4];
		}
	}

 if (!($TotalDomains = mysql_query("SELECT * FROM MAIL_Domains WHERE Username='" . $_SESSION['Username'] . "' AND Type='POP3'"))) {
  echo mysql_error();
 }
 if (mysql_num_rows($TotalDomains) >= $Pop3Cap && $Pop3Cap !=0) { 
	echo "<script language=\"JavaScript\">alert('You have exceeded your POP3 domain quota of " . $Pop3Cap . ". Operation aborted.');location.href = 'MAIL.php';</script>";
	exit();
 }
 if (!($SearchQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE MailDomain='" . strtolower($_POST['MailDomain']) . "'"))) {
  echo "Could not confirm mail domain uniqueness in the database.<br><b>MySQL Said:</b><br>" . mysql_error();
  return false;
 } else { 
             if (mysql_num_rows($SearchQuery) == 0) { 		          
				  $ctrl->login();
				    if ($ctrl->domainadd(strtolower($_POST['MailDomain'])) == "1") {
					     if (!($InsertQuery = mysql_query("INSERT INTO MAIL_Domains(Username,MailDomain,Quota,Type) VALUES('" . $_SESSION['Username'] .
										   "','" . strtolower($_POST['MailDomain']) . "','" . $MailboxSizeQuota . "','POP3')"))) {
			               $Result = "Could not insert new mail domain into the database.<br><b>MySQL Said:</b><br>" . mysql_error();
		                 } else {
						    echo "<script language=\"JavaScript\">alert('" . strtolower($_POST['MailDomain']) . " was successfully created.');location.href = 'MAIL.php';</script>";
				         }
					} else {
					  echo "<script language=\"JavaScript\">alert('There was an error adding " . strtolower($_POST['MailDomain']) . ". Operation aborted.');</script>";
				    }
				  $ctrl->logout();		         
	        } else {
	          echo "<script language=\"JavaScript\">alert('This mail domain already exists in the database. Please check your configurations and try again.');location.href = 'MAIL.php';</script>";
	        }
 }
} 





if ($_GET['Action'] == "CreateMX_BackupDomain") {
 require("includes/XMailCTRL.php"); 
 $Quota = explode("\r\n",$MemberPlans);
	foreach ($Quota as $Value) {
	  $Data = explode(",",$Value);
		if ($Data[0] == $_SESSION['SiteRole']) {
		   $MX_BackupCap = $Data[6];
		}
    }
 if (!($TotalDomains = mysql_query("SELECT * FROM MAIL_Domains WHERE Username='" . $_SESSION['Username'] . "' AND Type='MX_Backup'"))) {
  echo mysql_error();
 }
 if (mysql_num_rows($TotalDomains) >= $MX_BackupCap && $MX_BackupCap !=0) { 
	echo "<script language=\"JavaScript\">alert('You have exceeded your MX backup domain quota of " . $MX_BackupCap . ". Operation aborted.');location.href = 'MAIL.php';</script>";
	exit();
 }
	 if (!($SearchQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE MailDomain='" . strtolower($_POST['MailDomain']) . "'"))) {
	  echo "Could not confirm mail domains existence in the database.<br><b>MySQL Said:</b><br>" . mysql_error();
	  return false;
	 } else { 
				 if (!(mysql_num_rows($SearchQuery) > 0)) { 		          
					  $ctrl->login();
					    $s = '"smtprelay"'."\t".'"' . strtolower($_POST['MailServerFQDN']) . '"';
						if (substr($ctrl->custdomset(strtolower($_POST['MailDomain']),$s),0,1) == "+") {
							 if (!($InsertQuery = mysql_query("INSERT INTO MAIL_Domains(Username,MailDomain,Type) VALUES('" . $_SESSION['Username'] .
											   "','" . strtolower($_POST['MailDomain']) . "','MX_Backup')"))) {
							   echo "Could not insert new mail domain into the database.<br><b>MySQL Said:</b><br>" . mysql_error();
							   return false;
							 } else {
									   if (!($SelectQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE MailDomain='" . strtolower($_POST['MailDomain']) . "' AND Username='" . $_SESSION['Username'] . "'"))) {
									     echo "Could not insert new mail domain into the database.<br><b>MySQL Said:</b><br>" . mysql_error();
										 return false;
									   } else {									
									            $ThisZone = mysql_fetch_array($SelectQuery);	  
											   if (!($InsertQuery = mysql_query("INSERT INTO MAIL_Records(Username,RedirectTo,Type,ZoneID) VALUES('" . $_SESSION['Username'] .
													   "','" . strtolower($_POST['MailServerFQDN']) . "','MX_Backup','" . $ThisZone['ZoneID'] . "')"))) {
												 echo "Could not insert new mail domain into the database.<br><b>MySQL Said:</b><br>" . mysql_error();
												 return false;
											   } else {									   									   						 		   
												 echo "<script language=\"JavaScript\">alert('" . strtolower($_POST['MailDomain']) . " was successfully created.');location.href = 'MAIL.php';</script>";
											   }
									 }		   
							 }							 
						} else {
						  echo "<script language=\"JavaScript\">alert('There was an error adding " . strtolower($_POST['MailDomain']) . ". Operation aborted.');</script>";
						}
					  $ctrl->logout();		         
				} else {
				  echo "<script language=\"JavaScript\">alert('This mail domain already exists in the database. Please check your configurations and try again.');location.href = 'MAIL.php';</script>";
				}
	 }
} 	






if ($_GET['Action'] == "CreatePort25Domain") {
 require("includes/XMailCTRL.php"); 
 $Quota = explode("\r\n",$MemberPlans);
	foreach ($Quota as $Value) {
	  $Data = explode(",",$Value);
		if ($Data[0] == $_SESSION['SiteRole']) {
		   $Port25Cap = $Data[7];
		}
    }
 if (!($TotalDomains = mysql_query("SELECT * FROM MAIL_Domains WHERE Username='" . $_SESSION['Username'] . "' AND Type='Port25'"))) {
  echo mysql_error();
 }
 if (mysql_num_rows($TotalDomains) >= $Port25Cap && $Port25Cap !=0) { 
	echo "<script language=\"JavaScript\">alert('You have exceeded your Port 25 deflection domain quota of " . $Port25Cap . ". Operation aborted.');location.href = 'MAIL.php';</script>";
	exit();
 }
	 if (!($SearchQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE MailDomain='" . strtolower($_POST['MailDomain']) . "'"))) {
	  echo "Could not confirm mail domains existence in the database.<br><b>MySQL Said:</b><br>" . mysql_error();
	  return false;
	 } else { 
				 if (!(mysql_num_rows($SearchQuery) > 0)) { 		          
					  $ctrl->login();
					    $s = '"smtprelay"'."\t".'"' . strtolower($_POST['MailServerFQDN']) . ':' . $_POST['PortNumber'] . '"';
						if (substr($ctrl->custdomset(strtolower($_POST['MailDomain']),$s),0,1) == "+") {
 				             if (!($InsertQuery = mysql_query("INSERT INTO MAIL_Domains(Username,MailDomain,Type) VALUES('" . $_SESSION['Username'] .
											   "','" . strtolower($_POST['MailDomain']) . "','Port25')"))) {
							   echo "Could not insert new mail domain into the database.<br><b>MySQL Said:</b><br>" . mysql_error();
							   return false;
							 } else {
									   if (!($SelectQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE MailDomain='" . strtolower($_POST['MailDomain']) . "' AND Username='" . $_SESSION['Username'] . "'"))) {
									     echo "Could not insert new mail domain into the database.<br><b>MySQL Said:</b><br>" . mysql_error();
										 return false;
									   } else {									
									            $ThisZone = mysql_fetch_array($SelectQuery);	  
											   if (!($InsertQuery = mysql_query("INSERT INTO MAIL_Records(Username,RedirectTo,Type,ZoneID) VALUES('" . $_SESSION['Username'] .
													   "','" . strtolower($_POST['MailServerFQDN']) . ":" . $_POST['PortNumber'] . "','Port25','" . $ThisZone['ZoneID'] . "')"))) {
												 echo "Could not insert new mail domain into the database.<br><b>MySQL Said:</b><br>" . mysql_error();
												 return false;
											   } else {									   									   						 		   
												 echo "<script language=\"JavaScript\">alert('" . strtolower($_POST['MailDomain']) . " was successfully created.');location.href = 'MAIL.php';</script>";
											   }
									 }		   
							 }							 
						} else {
						  echo "<script language=\"JavaScript\">alert('There was an error adding " . strtolower($_POST['MailDomain']) . ". Operation aborted.');</script>";
						}
					  $ctrl->logout();	
					  
					  
					  
					  	         
				} else {
				  echo "<script language=\"JavaScript\">alert('This mail domain already exists in the database. Please check your configurations and try again.');location.href = 'MAIL.php';</script>";
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
function CreateDomain(DomainType) {
 location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?DomainType='+DomainType;
}
function VerifyPOP3() {
 // Check the domain
  var InvalidChars = "!@#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
   for (var i = 0; i < document.frmPOP3.MailDomain.value.length; i++) {
  	 if (InvalidChars.indexOf(document.frmPOP3.MailDomain.value.charAt(i)) != -1) {
  	  alert ("The specified domain name is invalid. Domain names may only contain ASCII text, numbers, and a period.");
  	 return false;
   	 }
   }     
  if (document.frmPOP3.MailDomain.value.length < 1) {
    alert ("You must enter the POP3 domain name.");
    return false;
   } 
   if (!(document.frmPOP3.MailDomain.value.indexOf(".") != -1) ){
    alert ("The POP3 domain entered is not a Fully Qualified Domain Name.");
    return false;
   }
  document.frmPOP3.submit();
}
function VerifyMX() {
// Check the mail domain
  var InvalidChars = "!@#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
   for (var i = 0; i < document.frmMX.MailDomain.value.length; i++) {
  	 if (InvalidChars.indexOf(document.frmMX.MailDomain.value.charAt(i)) != -1) {
  	  alert ("The specified POP3 domain name is invalid. Domain names may only contain ASCII text, numbers, and a period.");
  	 return false;
   	 }
   }     
  if (document.frmMX.MailDomain.value.length < 1) {
    alert ("You must enter the POP3 domain name.");
    return false;
   } 
   if (!(document.frmMX.MailDomain.value.indexOf(".") != -1) ){
    alert ("The POP3 domain entered is not a Fully Qualified Domain Name (FQDN).");
    return false;
   }
   // Check the mail server FQDN
  var InvalidChars = "!@#$%^&*()+=[]\\\';,/{}|\":<>?`_+= ";
   for (var i = 0; i < document.frmMX.MailServerFQDN.value.length; i++) {
  	 if (InvalidChars.indexOf(document.frmMX.MailServerFQDN.value.charAt(i)) != -1) {
  	  alert ("The specified Mail servers Fully Qualified Domain Name FQDN is invalid. Domain names may only contain ASCII text, numbers, and a period.");
  	 return false;
   	 }
   }     
  if (document.frmMX.MailServerFQDN.value.length < 1) {
    alert ("You must enter your mail servers Fully Qualified Domain Name (FQDN).");
    return false;
   } 
   if (!(document.frmMX.MailServerFQDN.value.indexOf(".") != -1) ){
    alert ("The mail servers domain entered is not a Fully Qualified Domain Name (FQDN).");
    return false;
   }   
  document.frmMX.submit();
}
function VerifyPort25() {
// Check the mail domain
  var InvalidChars = "!@#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
   for (var i = 0; i < document.frmPort25.MailDomain.value.length; i++) {
  	 if (InvalidChars.indexOf(document.frmPort25.MailDomain.value.charAt(i)) != -1) {
  	  alert ("The specified POP3 domain name is invalid. Domain names may only contain ASCII text, numbers, and a period.");
  	 return false;
   	 }
   }     
  if (document.frmPort25.MailDomain.value.length < 1) {
    alert ("You must enter the POP3 domain name.");
    return false;
   } 
   if (!(document.frmPort25.MailDomain.value.indexOf(".") != -1) ){
    alert ("The POP3 domain entered is not a Fully Qualified Domain Name (FQDN).");
    return false;
   }
   // Check the mail server FQDN
  var InvalidChars = "!@#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
   for (var i = 0; i < document.frmPort25.MailServerFQDN.value.length; i++) {
  	 if (InvalidChars.indexOf(document.frmPort25.MailServerFQDN.value.charAt(i)) != -1) {
  	  alert ("The specified Mail servers Fully Qualified Domain Name FQDN is invalid. Domain names may only contain ASCII text, numbers, and a period.");
  	 return false;
   	 }
   }     
  if (document.frmPort25.MailServerFQDN.value.length < 1) {
    alert ("You must enter your mail servers Fully Qualified Domain Name (FQDN).");
    return false;
   } 
   if (!(document.frmPort25.MailServerFQDN.value.indexOf(".") != -1) ){
    alert ("The mail servers domain entered is not a Fully Qualified Domain Name (FQDN).");
    return false;
   }
   // Checks the port number
   if ((document.frmPort25.PortNumber.value < 1) || (document.frmPort25.PortNumber.value > 65535)) {
    alert ("The port number entered violates the internet standard of 1 - 65535 ports per TCP or UDP protocol.");
    return false;
   }
   if (isNaN(document.frmPort25.PortNumber.value)) {
    alert ("The port number must only contain numeric characters.");
    return false;
   }   
  document.frmPort25.submit();
}
</script>
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699">
<table width=780 border=0 cellpadding=0 cellspacing=0 height="383" bgcolor="#FFFFFF">
  <tr> 
    <td rowspan=2> <img src="images/index_01.gif" width=165 height=35></td>
    <td colspan=2> <img src="images/index_02.gif" width=615 height=24></td>
  </tr>
  <tr> 
    <td> <img src="images/index_03.gif" width=1 height=11></td>
    <td rowspan=2> <img src="images/index_04_logo.jpg" width=614 height=73></td>
  </tr>
  <tr> 
    <td colspan=2 height="39"> <img src="images/project_logo.gif" width=166 height=62></td>
  </tr>
  <tr> 
    <td colspan=3 background="images/links.gif"> 
     <?php include("navigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan=3 height="233"> 	 
      <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr> 
          <td height="212">
  		   <table class="menu" width="100%" border="0">
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
                <td colspan="3"><?php echo $Result; ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">
				 <div align="center" class="highlight" id="PickDomType">What type of domain would you like to create?</div>
				 <div id="divPOP3" class="highlight" align="center" style="display:none;">Type the domain name that you want to configure to accept mail into the textbox below.</div>
 			     <div id="divMX_Backup" class="highlight" align="center" style="display:none;">Type the domain which you are backing up into the first textbox. Next, place the Fully Qualified Domain Name of your mail server into the second textbox. When you are finished, click on the create button.</div>
                 <div id="divPort25" class="highlight" align="center" style="display:none;">Type the domain which you would like our mail server to collect mail for into the first textbox. Next, type the Fully Qualified Domain Name of your mail server into the second textbox. Finally, enter the port number that you would like mail relayed to into the third textbox. When you are finished, click on the create button.</div>
			   </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">
				 <?php 
		           if (!(isset($_GET['DomainType']) && $_GET['DomainType'] != "")) {
		         ?>
				<table width="45%"  border="1" bordercolor="#F9F9F9" class="menu">
                  <tr>
                    <td width="6%"><input name="DomainType" type="radio" value="POP3" id="POP3" onClick="JavaScript:CreateDomain(this.value)">  </td>
                    <td width="94%"> <label for="POP3" style="cursor:hand;">Standard POP3 Domain</label></td>
                  </tr>
                  <tr>
                    <td><input name="DomainType" type="radio" value="MX_Backup" id="MX_BACKUP" onClick="JavaScript:CreateDomain(this.value)">  </td>
                    <td> <label for="MX_BACKUP" style="cursor:hand;">MX-Backup Domain</label></td>
                  </tr>
                  <tr>
                    <td><input name="DomainType" type="radio" value="Port25" id="PORT25" onClick="JavaScript:CreateDomain(this.value)"></td>
                    <td> <label for="PORT25" style="cursor:hand;">Port 25 Redirect</label> </td>
                  </tr>
                </table>
				<?php
			     } else {
 				    switch ($_GET['DomainType']) {
					
					   case "POP3":
					    ?>
						<script language="javascript">
						  document.getElementById('PickDomType').style.display = 'none';
						  document.getElementById('divPOP3').style.display = '';
						  document.getElementById('divMX_Backup').style.display = 'none';
						  document.getElementById('divPort25').style.display = 'none';
					     </script>
						<form name="frmPOP3" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=CreatePOP3Domain" method="post" onSubmit="JavaScript:return VerifyPOP3();">
						 <table width="56%"  border="1" bordercolor="#F9F9F9" class="menu">
                           <tr>
                             <td width="36%">POP3 Domain:</td>
                             <td width="64%">
                               <input name="MailDomain" type="text" id="MailDomain">
                             </td>
                           </tr>
                           <tr>
                             <td>&nbsp;</td>
                             <td>
                               <input type="button" value="Create" onClick="JavaScript:VerifyPOP3();">
                             </td>
                           </tr>
                         </table>
				  </form>
						 <?php
					   break;
						
					   
					   case "MX_Backup":
					    ?>
 						 <script language="javascript">
						  document.getElementById('PickDomType').style.display = 'none';
						  document.getElementById('divPOP3').style.display = 'none';
						  document.getElementById('divMX_Backup').style.display = '';
						  document.getElementById('divPort25').style.display = 'none';
					     </script>
                                                <form name="frmMX" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=CreateMX_BackupDomain" method="post" onSubmit="JavaScript:return VerifyMX();">
                                                  <table width="62%"  border="1" bordercolor="#F9F9F9" class="menu">
                                                    <tr>
                                                      <td width="44%">POP3 Domain:</td>
                                                      <td width="56%">
                                                        <input name="MailDomain" type="text" id="MailDomain">
                                                     </td>
                                                    </tr>
                                                    <tr>
                                                      <td>Mail Server FQDN: </td>
                                                      <td><input name="MailServerFQDN" type="text" id="MailServerFQDN"></td>
                                                    </tr>
                                                    <tr>
                                                      <td>&nbsp;</td>
                                                      <td>
                                                        <input type="button" value="Create" onClick="JavaScript:VerifyMX();">
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </form>
                         <?php
					   break;
					  
					  
					  case "Port25":
					    ?>
					  	<script language="javascript">
						  document.getElementById('PickDomType').style.display = 'none';
						  document.getElementById('divPOP3').style.display = 'none';
						  document.getElementById('divMX_Backup').style.display = 'none';
						  document.getElementById('divPort25').style.display = '';
					     </script>
                                                 <form name="frmPort25" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=CreatePort25Domain" method="post">
                                                   <table width="59%"  border="1" bordercolor="#F9F9F9" class="menu">
                                                     <tr>
                                                       <td width="42%">POP3 Domain:</td>
                                                       <td width="58%">
                                                         <input name="MailDomain" type="text" id="MailDomain">
                                                       </td>
                                                     </tr>
                                                     <tr>
                                                       <td>Mail Server FQDN:</td>
                                                       <td><input name="MailServerFQDN" type="text" id="MailServerFQDN"></td>
                                                     </tr>
                                                     <tr>
                                                       <td>Forward To Port: </td>
                                                       <td><input name="PortNumber" type="text" id="PortNumber" size="6" maxlength="5"></td>
                                                     </tr>
                                                     <tr>
                                                       <td>&nbsp;</td>
                                                       <td>
                                                         <input type="button" value="Create" onClick="JavaScript:VerifyPort25();">
                                                       </td>
                                                     </tr>
                                                   </table>
                                                 </form>					    
                  <?php
					  break;
					}
				  }
			    ?>
			   </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="23%">&nbsp;</td>
                <td width="24%">&nbsp;</td>
                <td width="35%">&nbsp;</td>
              </tr>
            </table>
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