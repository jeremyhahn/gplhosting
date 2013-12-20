<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");


  if (isset($_GET['MailboxID'])) {
	  if (!($query = mysql_query("SELECT * FROM MAIL_Records WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "' AND MailboxID='" . $_GET['MailboxID'] . "' ORDER BY Mailbox"))) {
	   $Result = "Could not execute SQL select statement to get domain info.<br><b>MySQL Said:</b><br>" . mysql_error();
	  }
  } else {
	  if (!($query = mysql_query("SELECT * FROM MAIL_Records WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "' ORDER BY Mailbox"))) {
		   $Result = "Could not execute SQL select statement to get domain info.<br><b>MySQL Said:</b><br>" . mysql_error();
		  }
  }

     if (isset($_GET['Action']) && $_GET['Action'] == "UpdateMailbox") {
	  $ThisRecord = mysql_fetch_array($query);
	  require("includes/XMailCTRL.php");
	  $ctrl->login();
	  
	    // Gets the domain name needed for the XMAIL Class		
		if (!($DomQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE ZoneID='" . $_POST['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
         $Result = "Could not execute SQL select statement to get domain info.<br><b>MySQL Said:</b><br>" . mysql_error();
        } else {
		 $DB_Rec = mysql_fetch_array($DomQuery);
		}	 		 
		 
		// Change password
		if (!(isset($_POST['ChangePass']))) {
	       if (!($ctrl->userpasswd($DB_Rec['MailDomain'], $_POST['Mailbox'], $_POST['Password']))) {
			  echo "<script language=\"JavaScript\">alert('The password for mailbox " . $_POST['Mailbox'] . " could not be reset.');location.href = 'MAIL.php';</script>";		      
		   } else { 
			   if (!($UpdateQuery2 = mysql_query("Update MAIL_Records Set Password='" . base64_encode($_POST['Password']) . "' WHERE Username='" . $_SESSION['Username'] . "' AND MailboxID='" . $_POST['MailboxID'] . "'"))) {
				 $Failed = true;
				 echo "Could not execute SQL update statement to update mailbox password.<br><b>MySQL Said:</b><br>" . mysql_error();
			   }
		   }
		}		
		
		// Make sure that the quota doesnt exceed the allowable quota for the domain.
		 if (str_replace(",","",$_POST['Quota']) * 1024 > $DB_Rec['Quota']) {
		  $OverQuota = 1;
		  echo "<script language=\"JavaScript\">alert('You have entered a quota amount which exceeds your domain limit of " . $DB_Rec['Quota'] / 1024 . " MB. Please correct this mistake before trying to submit again.');location.href = '" . $_SERVER['PHP_SELF'] . "?MailboxID=" . $_POST['MailboxID'] . "&ZoneID=" . $_POST['ZoneID'] . "';</script>";
		 }
		
		// Update Quota
		if (!$OverQuota) {
		 $s = '"MaxMBsize"'."\t".'"' . str_replace(",","",$_POST['Quota']) * 1024 . '"';
		 if (!($ctrl->uservarsset($DB_Rec['MailDomain'], $_POST['Mailbox'], $s))) {
		   echo "<script language=\"JavaScript\">alert('The quota for mailbox " . $_POST['Mailbox'] . " could not be updated.');//location.href = 'MAIL.php';</script>";
		 } else {
			 if (!($UpdateQuery3 = mysql_query("Update MAIL_Records Set Quota='" . str_replace(",","",$_POST['Quota']) * 1024 . "' WHERE Username='" . $_SESSION['Username'] . "' AND MailboxID='" . $_POST['MailboxID'] . "'"))) {
			   $Failed = true;
			   echo "Could not execute SQL update statement to update mailbox quota.<br><b>MySQL Said:</b><br>" . mysql_error();
			 }
		 }
		}
		 
		 // Update Redirect
		 if (!($_POST['RedirectTo'] == $ThisRecord['RedirectTo'])) {
		  if (strlen($_POST['RedirectTo']) > 2) {
		   $vars = '"mailbox"'."\n".'"redirect"'."\t".'"' . $_POST['RedirectTo'] . '"';
		  } else {
		   $vars = ".";
		  }
			 $ctrl->usersetmproc($DB_Rec['MailDomain'],$_POST['Mailbox'],$vars);
				 if (!($UpdateQuery4 = mysql_query("Update MAIL_Records Set RedirectTo='" . $_POST['RedirectTo'] . "' WHERE Username='" . $_SESSION['Username'] . "' AND MailboxID='" . $_POST['MailboxID'] . "'"))) {
				   $Failed = true;
				   echo "Could not execute SQL update statement to update mailbox destination.<br><b>MySQL Said:</b><br>" . mysql_error();
				 } 	
		}
		 $ctrl->logout();	
		 echo "<script language=\"JavaScript\">alert('Mailbox for " . $_POST['Mailbox'] . " was successfully updated.');location.href = 'MAIL.php';</script>";
	}		 
		 	 
	 
  
  
  
  if (isset($_GET['Action']) && $_GET['Action'] == "DeleteMailbox") {
     if (!($ThisSelectQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $_GET['ZoneID'] . "'"))) {
	   echo "Could not execute SQL select query for ZoneID.<br><b>MySQL Said:</b><br>" . mysql_error();
	 } else {
	   $DomainInfo = mysql_fetch_array($ThisSelectQuery);
	 }
	     require("includes/XMailCTRL.php");
		 $ctrl->login();
   
          if (!($ctrl->userdel($DomainInfo['MailDomain'],$_GET['Mailbox']))) {
		    echo "<script language=\"JavaScript\">alert('The mailbox \'" . $_GET['Mailbox'] . "\' could not be deleted.');location.href = 'MAIL.php';</script>";
		  } else {			
			 if (!($DeleteQuery = mysql_query("DELETE FROM MAIL_Records WHERE Username='" . $_SESSION['Username'] . "' AND MailboxID='" . $_GET['MailboxID'] . "'"))) {
			  echo "Could not execute SQL delete query.<br><b>MySQL Said:</b><br>" . mysql_error();
			 } else {
			  echo "<script language=\"JavaScript\">alert('The mailbox \'" . $_GET['Mailbox'] . "\' was  successfully deleted.');location.href = 'MAIL.php';</script>";
			 }
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
function UpdateMailbox(MailboxID,ZoneID) {
 location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?MailboxID='+MailboxID+'&ZoneID='+ZoneID;
}
function DeleteRecord(MailboxID,Mailbox,ZoneID) {
 var decision = confirm("Are you sure you want to delete this mailbox?");
  if (decision == true) {
    location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?Action=DeleteMailbox&MailboxID='+MailboxID+'&Mailbox='+Mailbox+'&ZoneID='+ZoneID;
  }
}
function ValidateElements() {
   // Check the passwords
   if (document.getElementById('Password').value != document.getElementById('Password2').value) {
    alert('Passwords do not match!');
	return false;
   }
   if (!(document.getElementById('ChangePass').checked == true)) {
	   if (document.getElementById('Password').value == "" || document.getElementById('Password2').value == "") {
		alert('Password can not be blank.');
		return false;
	   }  
   }
   // Checks the mailbox name
   if (document.MailboxInfo.Mailbox.value.length == 0) {
    alert('Mailbox name can not be blank.');
	return false;
   }
   var InvalidChars = "!@#$%^&*()[]\\\';,./{}|\":<>?`_+=";
   for (var i = 0; i < document.MailboxInfo.Mailbox.value.length; i++) {
  	 if (InvalidChars.indexOf(document.MailboxInfo.Mailbox.value.charAt(i)) != -1) {
  	  alert ("Your mailbox name contains special characters which are not allowed. Please remove them and try again.");
  	 return false;
   	 }
   }
   // Checks Quota
   if (isNaN(document.MailboxInfo.Quota.value)) {
    alert('The quota which you entered is not a valid size limit. Please check the quota limit and try again.');
   return false;
   }
 document.MailboxInfo.submit();
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
          <td height="212">
  		   <table class="menu" width="100%" border="0">
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              <tr>
                <td width="18%"><?php include("CP_Navigation.php"); ?></td>
                <td colspan="3"><?php include("CenterOfAttention.php"); ?></td>
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
				 <div align="center" class="highlight" id="PickMailboxType">Which mailbox would you like to edit?</div>
				 <div id="divMailbox" style="display:none;" align="center">
				  <p class="highlight">Type the mailbox name into the first textbox, followed by your password. You must confirm your password before the settings will be applied. If you wish to redirect this mailbox to another inbox, you may do so by typing the full email address to the inbox you want the message forwarded to, into the textbox labeled 'Redirect To'. You may also change the allowable mailbox quota size if desired. When you are finished, you may click the 'create' button to complete your mailbox setup. </p>
                  <p><span class="style2">NOTE:</span> <span class="highlight">You may not exceed the allowable quota which has been set for your domain.</span></p>
                 </div>
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
				   if (isset($_GET['Action']) && $_GET['Action'] == "EditMailbox") {
				 ?>
				 <table border="0" class="menu">
                  <tr>
				   <td></td>
				   <td><?php if (mysql_num_rows($query) > 0) { ?>
				     <span class="highlight">Active Mailboxes</span>				     <?php } ?></td>
				  </tr>
				<?php
			       if (mysql_num_rows($query) == 0) {
					echo "<tr><td><b><i>No mailboxes configured for this domain at this time.</i></b></td></tr>";
				   }
					  while ($row = mysql_fetch_array($query)) {
					   echo "<tr><td><input type=\"radio\" id=\"Mailbox_" . $row['MailboxID'] . "\" name=\"MailboxID\" value=\"" . $row['MailboxID'] . "\" onClick=\"JavaScript:UpdateMailbox(this.value,'" . $row['ZoneID'] . "');\" style=\"cursor:hand;\"></td><td><label for=\"Mailbox_" . $row['MailboxID'] . "\" style=\"cursor:hand;\">" . $row['Mailbox'] . "</label></td></tr>";
					  }
					?>
                 </table>
				 <?php
				   } else {
				   $ThisRecord = mysql_fetch_array($query);
				  ?>
                   <form action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=UpdateMailbox&ZoneID=<?php echo $ThisRecord['ZoneID']; ?>&MailboxID=<?php echo $ThisRecord['MailboxID']; ?>" name="MailboxInfo" method="post">
				    <table width="100%"  border="0" class="menu">
                      <tr>
                        <td width="24%">Mailbox Name: </td>
                        <td width="76%"><input name="Mailbox" style="color:#999999;" readonly type="text" id="Mailbox" value="<?php echo $ThisRecord['Mailbox']; ?>">
                       </td>
                      </tr>
                      <tr>
                        <td>Password:</td>
                        <td><input name="Password" type="password" id="Password" value="<?php echo $ThisRecord['Password']; ?>"></td>
                      </tr>
                      <tr>
                        <td>Confirm Password: </td>
                        <td><input name="Password2" type="password" id="Password2" value="<?php echo $ThisRecord['Password']; ?>"></td>
                      </tr>
                      <tr>
                        <td>Use Current Password:</td>
                        <td><input name="ChangePass" type="checkbox" id="ChangePass" value="NO" checked onClick="JavaScript:document.MailboxInfo.Password.value = '';document.MailboxInfo.Password2.value = '';"></td>
                      </tr>
                      <tr>
                        <td>Quota</td>
                        <td><input name="Quota" type="text" id="Quota" value="<?php echo $ThisRecord['Quota'] / 1024; ?>" size="4" maxlength="4">
                         MB</td>
                      </tr>
                      <tr>
                        <td>Redirect To: </td>
                        <td><input name="RedirectTo" type="text" id="RedirectTo" value="<?php echo $ThisRecord['RedirectTo']; ?>"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td><input type="button" value="Update" onClick="JavaScript:ValidateElements();">
                        <input type="button" value="Delete" onClick="JavaScript:DeleteRecord('<?php echo $ThisRecord['MailboxID']; ?>','<?php echo $ThisRecord['Mailbox']; ?>','<?php echo $_GET['ZoneID']; ?>');"></td>
                        <input type="hidden" name="ZoneID" value="<?php echo $_GET['ZoneID']; ?>">
						<input type="hidden" name="MailboxID" value="<?php echo $ThisRecord['MailboxID']; ?>">
                      </tr>
                    </table>
				  </form>
				    <p>                         
				      <script language="javascript">
						  document.getElementById('PickMailboxType').style.display = 'none';
						  document.getElementById('divMailbox').style.display = '';
					     </script>
			          </p>
				    <?php
					
					
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