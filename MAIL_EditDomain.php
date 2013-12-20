<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");


  if (!($query = mysql_query("SELECT * FROM MAIL_Domains WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
   $Result = "Could not execute SQL select statement to get domain info.<br><b>MySQL Said:</b><br>" . mysql_error();
  } else {
   $DomainInfo = mysql_fetch_array($query);
   
      if (!($Rec_Query = mysql_query("SELECT * FROM MAIL_Records WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	   $Result = "Could not execute SQL select statement to get mail domain database records.<br><b>MySQL Said:</b><br>" . mysql_error();
	  } else {
	    $Recs = mysql_fetch_array($Rec_Query);
	  }
  }




    if (isset($_GET['Action']) && $_GET['Action'] == "UpdatePort25Domain") {
       require("includes/XMailCTRL.php");
	   
	    $ctrl->login();
           if (!($UpdateMX = mysql_query("UPDATE MAIL_Records Set RedirectTo='" . $_POST['MailServerFQDN'] . ":" . $_POST['PortNumber'] . "' WHERE ZoneID='" . $_POST['ZoneID'] . "'"))) {
		    $Result = "Could not execute SQL update statement for port 25 deflection domain.<br><b>MySQL Said:</b><br>" . mysql_error();
		   } else {
		        $s = '"smtprelay"'."\t".'"' . $_POST['MailServerFQDN'] . ':' . $_POST['PortNumber'] . '"';
		        echo $ctrl->custdomset($_POST['MailDomain'],$s);
			    echo "<script language=\"JavaScript\">alert('" . $_POST['MailDomain'] . " was successfully updated.');location.href = 'MAIL.php';</script>";
		  } 
	    $ctrl->logout();		
    } 
  
  
  
  
  
  
  if (isset($_GET['Action']) && $_GET['Action'] == "UpdateMX_BackupDomain") {
       require("includes/XMailCTRL.php");
	   
	    $ctrl->login();
           if (!($UpdateMX = mysql_query("UPDATE MAIL_Records Set RedirectTo='" . $_POST['MailServerFQDN'] . "' WHERE ZoneID='" . $_POST['ZoneID'] . "'"))) {
		    $Result = "Could not execute SQL update statement for port 25 deflection domain.<br><b>MySQL Said:</b><br>" . mysql_error();
		   } else {
		        $s = '"smtprelay"'."\t".'"' . $_POST['MailServerFQDN'] . '"';
		        echo $ctrl->custdomset($_POST['MailDomain'],$s);
			    echo "<script language=\"JavaScript\">alert('" . $_POST['MailDomain'] . " was successfully updated.');location.href = 'MAIL.php';</script>";
		  } 
	    $ctrl->logout();		
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
function VerifyMX() {
// Check the mail domain
  var InvalidChars = "!@#$%^&*()+=[]\\\';,/{}|\":<>?`_+= ";
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
  var InvalidChars = "!@#$%^&*()+=[]\\\';,/{}|\":<>?`_+= ";
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
  var InvalidChars = "!@#$%^&*()+=[]\\\';,/{}|\":<>?`_+= ";
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
                <td colspan="3">
				<?php echo $Result; ?></td>
                 
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">
				 <table width="90%"  border="0" align="center" class="menu">
                  <tr>
                   <td>
				    <div class="highlight" id="PickDomType" align="center">What type of domain would you like to create?</div>
				    <div class="highlight" id="divPOP3" align="center" style="display:none;">Type the domain name that you want to configure to accept mail into the textbox below.</div>
 			        <div class="highlight" id="divMX_Backup" align="center" style="display:none;">Type the domain which you are backing up into the first textbox. Next, place the Fully Qualified Domain Name of your mail server into the second textbox. When you are finished, click on the create button.</div>
                    <div class="highlight" id="divPort25" align="center" style="display:none;">Type the domain which you would like our mail server to collect mail for into the first textbox. Next, type the Fully Qualified Domain Name of your mail server into the second textbox. Finally, enter the port number that you would like mail relayed to into the third textbox. When you are finished, click on the create button.</div>
			     </tr>
                </table>
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
			    
				   switch ($DomainInfo['Type']) {					
					   case "POP3":
					    ?>
                         <script language="javascript">
						  document.getElementById('PickDomType').style.display = 'none';
						  document.getElementById('divPOP3').style.display = '';
						  document.getElementById('divMX_Backup').style.display = 'none';
						  document.getElementById('divPort25').style.display = 'none';
					     </script>
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=UpdatePOP3Domain" method="post">
						 <table width="56%"  border="1" bordercolor="#F9F9F9" class="menu">
                           <tr>
                             <td width="36%">POP3 Domain:</td>
                             <td width="64%">
                               <input name="MailDomain" type="text" id="MailDomain" value="<?php echo $DomainInfo['MailDomain']; ?>">
                            </td>
                           </tr>
                           <tr>
                             <td>&nbsp;                             </td>
                             <td>
                               <input type="submit" name="Submit" value="Update">
							   <input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
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
                                                <form name="frmMX" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=UpdateMX_BackupDomain" method="post">
                                                  <table width="62%"  border="1" bordercolor="#F9F9F9" class="menu">
                                                    <tr>
                                                      <td width="44%">POP3 Domain:</td>
                                                      <td width="56%">
                                                        <input name="MailDomain" style="color:#999999;" readonly type="text" id="MailDomain" value="<?php echo $DomainInfo['MailDomain']; ?>">
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td>Mail Server FQDN: </td>
                                                      <td><input name="MailServerFQDN" type="text" id="MailServerFQDN" value="<?php echo $Recs['RedirectTo']; ?>"></td>
                                                    </tr>
                                                    <tr>
                                                      <td>&nbsp;</td>
                                                      <td>
                                                        <input type="submit" name="Submit" value="Update">
														<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </form>
                         <?php
					   break;
					  
					  
					  case "Port25":
					     $ReDirArray = explode(":",$Recs['RedirectTo']);
			              $Domain = $ReDirArray[0];
					      $Port = $ReDirArray[1];				
					    ?>
					  	<script language="javascript">
						  document.getElementById('PickDomType').style.display = 'none';
						  document.getElementById('divPOP3').style.display = 'none';
						  document.getElementById('divMX_Backup').style.display = 'none';
						  document.getElementById('divPort25').style.display = '';
					     </script>
                                                 <form name="frmPort25" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=UpdatePort25Domain" method="post">
                                                   <table width="59%"  border="1" bordercolor="#F9F9F9" class="menu">
                                                     <tr>
                                                       <td width="42%">POP3 Domain:</td>
                                                       <td width="58%">
                                                         <input name="MailDomain" style="color:#999999;" readonly type="text" id="MailDomain" value="<?php echo $DomainInfo['MailDomain']; ?>">
                                                       </td>
                                                     </tr>
                                                     <tr>
                                                       <td>Mail Server FQDN:</td>
                                                       <td><input name="MailServerFQDN" type="text" id="MailServerFQDN" value="<?php echo $Domain; ?>""></td>
                                                     </tr>
                                                     <tr>
                                                       <td>Forward To Port: </td>
                                                       <td><input name="PortNumber" type="text" id="PortNumber" value="<?php echo $Port; ?>" size="6" maxlength="5"></td>
                                                     </tr>
                                                     <tr>
                                                       <td>&nbsp;</td>
                                                       <td>
                                                         <input type="submit" name="Submit" value="Update">
														 <input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
                                                      </td>
                                                     </tr>
                                                   </table>
                                                 </form>					    
                  <?php
					  break;
					}
			    ?>
			   </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="21%">&nbsp;</td>
                <td width="26%">&nbsp;</td>
                <td width="35%">&nbsp;</td>
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