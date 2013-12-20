<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");


if (!($SelectQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
 echo "Could not execute SQL select query for mail domain.<br><b>MySQL Said:</b><br>" . mysql_error();
 return false;
} else {
  $ThisDomain = mysql_fetch_array($SelectQuery);
}  
	  
	  
	  
  // Creates the user account on the mail server, and adds their account to the database.
  if (isset($_GET['Action']) && $_GET['Action'] == "CreateNewMailbox") {
   require("includes/XMailCTRL.php");
   $Quota = explode("\r\n",$MemberPlans);
	foreach ($Quota as $Value) {
	  $Data = explode(",",$Value);
		if ($Data[0] == $_SESSION['SiteRole']) {		   
		   $MailboxSizeQuota = $Data[4];
		   $MailboxQuota = $Data[5];
		}
    }
	 if (!($MailZoneID = mysql_query("SELECT ZoneID FROM MAIL_Domains WHERE Username='" . $_SESSION['Username'] . "' AND MailDomain='" . $_POST['MailDomain'] . "'"))) {
	  echo mysql_error();
	 } else {
       $MailID = mysql_fetch_array($MailZoneID);
	 }
	 if (!($TotalMailboxes = mysql_query("SELECT * FROM MAIL_Records WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $MailID['ZoneID'] . "'"))) {
	  echo mysql_error();
	 }
	 if (mysql_num_rows($TotalMailboxes) >= $MailboxQuota && $MailboxQuota !=0) { 
		echo "<script language=\"JavaScript\">alert('You have exceeded the maximum number of mailboxes you can have for " . $_POST['MailDomain'] . ", which is " . $MailboxQuota  . ". Operation aborted.');location.href = 'MAIL.php';</script>";
		exit();
	 }
   // Check to make sure that this zone does not already exist
     $ZoneQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE MailDomain='" . $_POST['MailDomain'] . "'");
	 $ThisDomain = mysql_fetch_array($ZoneQuery);
	   if (!($chkQuery = mysql_query("SELECT * FROM MAIL_Records WHERE Mailbox='" . $_POST['Mailbox'] . "' AND Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $ThisDomain['ZoneID'] . "'"))) {
		 echo "Could not execute SQL select query for mail domain.<br><b>MySQL Said:</b><br>" . mysql_error();
		 return false;
		} else {
				 if (mysql_num_rows($chkQuery) > 0) {
				   echo "<script language=\"JavaScript\">alert('" . ucfirst($_POST['Mailbox']) . " is already configured as a mailbox for " . $_POST['MailDomain'] . ". Please check your current configurations and try again.');location.href = 'MAIL.php';</script>";
				 } else {		
					 // Creates the user on the mail server
					 $ctrl->login();
							 if (!($ctrl->useradd($_POST['MailDomain'], $_POST['Mailbox'], $_POST['Password'], "U"))) {				  
									echo "<script language=\"JavaScript\">alert('There was an error adding " . $_POST['MailDomain'] . ". Operation aborted.');</script>";
							 } else {							           
									 // Make sure that the quota doesnt exceed the allowable quota for the domain.
									  if (str_replace(",","",$_POST['Quota']) * 1024 > $MailboxSizeQuota && $MailboxSizeQuota !=0) {
									    $OverQuota = 1;
									    echo "<script language=\"JavaScript\">alert('You entered a quota amount of " . $_POST['Quota'] . " MB, which exceeds your domain limit of " . $MailboxSizeQuota / 1024 . " MB per mailbox. A default value of " . $MailboxSizeQuota / 1024 . " MB will be used to create the new mailbox.');</script>"; 
									    $MBquota = $MailboxSizeQuota;
									  }	else { $MBquota = str_replace(",","",$_POST['Quota']) * 1024; }
							          // Creates the account in the database only if the user was created on the mail server
									  if (!($InsertQuery = mysql_query("INSERT INTO MAIL_Records(Username,Password,Mailbox,Quota,RedirectTo,Type,ZoneID) VALUES('" .
														  $_SESSION['Username'] . "','" . base64_encode($_POST['Password']) . "','" . $_POST['Mailbox'] . "','" . 
														  $MBquota . "','" . $_POST['RedirectTo'] . "','POP3','" . $_POST['ZoneID'] . "')"))) {
										echo "Could not execute SQL insert query for new mailbox.<br><b>MySQL Said:</b><br>" . mysql_error();
									   } else {																			
										// Update Quota
										 $s = '"MaxMBsize"'."\t".'"' . $MBquota . '"';										
										 if (!($ctrl->uservarsset($_POST['MailDomain'], $_POST['Mailbox'], $s))) {
										   echo "<script language=\"JavaScript\">alert('The quota for mailbox " . $_POST['Mailbox'] . " could not be updated.');</script>";
										 } else {
											 if (!($UpdateQuery3 = mysql_query("Update MAIL_Records Set Quota='" . $MBquota . "' WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $_POST['ZoneID'] . "' AND Mailbox='" . $_POST['Mailbox'] . "'"))) {
											   $Failed = true;
											   echo "Could not execute SQL update statement to update mailbox quota.<br><b>MySQL Said:</b><br>" . mysql_error();
											 }
										}
										
										
										echo "<script language=\"JavaScript\">alert('" . ucfirst($_POST['Mailbox']) . " was successfully created.');location.href = 'MAIL.php';</script>";
									   }							 
							   }
				     $ctrl->logout();		
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
function ValidateElements() {
   // Check the passwords
   if (document.getElementById('Password').value != document.getElementById('Password2').value) {
    alert('Passwords do not match!');
	return false;
   }
   if (document.getElementById('Password').value == "" || document.getElementById('Password2').value == "") {
    alert('Password can not be blank.');
	return false;
   }  
   // Checks the mailbox name
   if (document.NewMailbox.Mailbox.value.length == 0) {
    alert('Mailbox name can not be blank.');
	return false;
   }
   var InvalidChars = "!@#$%^&*()[]\\\';,/{}|\":<>?`_+= ";
   for (var i = 0; i < document.NewMailbox.Mailbox.value.length; i++) {
  	 if (InvalidChars.indexOf(document.NewMailbox.Mailbox.value.charAt(i)) != -1) {
  	  alert ("Your mailbox name contains special characters which are not allowed. Please remove them and try again.");
  	 return false;
   	 }
   }
   // Checks Quota
   if (isNaN(document.NewMailbox.Quota.value)) {
    alert('The quota which you entered is not a valid size limit. Please check the quota limit and try again.');
   return false;
   }
 document.NewMailbox.submit();
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
                <td colspan="3"><div align="center">
                  <table width="90%"  border="0" class="menu">
                    <tr>
                      <td><span class="highlight">Type the mailbox name into the first textbox, followed by your password. You must confirm your password before the settings will be applied. You may set the allowable mailbox quota size if desired, as long as the overall domain quota level is not exceeded. When you are finished, you may click the 'create' button to complete your mailbox setup.</span></td>
                    </tr>
                  </table>
                  <p class="highlight">&nbsp;</p>
                </div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">
			   <form name="NewMailbox" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=CreateNewMailbox" method="POST">	
				<table width="100%"  border="0" class="menu">
                  <tr>
                    <td width="21%">Mailbox Name: </td>
                    <td width="79%"><input name="Mailbox" type="text" id="Mailbox" value="you" onClick="JavaScript:this.value='';">
                      @ <?php echo $ThisDomain['MailDomain']; ?></td>
                  </tr>
                  <tr>
                    <td>Password:</td>
                    <td><input name="Password" type="password" id="Password"></td>
                  </tr>
                  <tr>
                    <td>Confirm Password: </td>
                    <td><input name="Password2" type="password" id="Password2"></td>
                  </tr>
                  <tr>
                    <td>Quota</td>
                    <td><input name="Quota" type="text" id="Quota" value="2" size="4" maxlength="2"> 
                    MB</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Create" onClick="JavaScript:ValidateElements();"></td>
					<input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
					<input type="hidden" name="MailDomain" value="<?php echo $ThisDomain['MailDomain']; ?>">
                  </tr>
                </table>
				</form>
			   </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="22%">&nbsp;</td>
                <td width="25%">&nbsp;</td>
                <td width="35%">&nbsp;</td>
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