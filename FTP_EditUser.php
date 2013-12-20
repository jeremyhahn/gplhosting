<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");

if (!($UserQuery = mysql_query("SELECT * FROM FTP_Records WHERE Username='" . $_GET['Username'] . "' AND Owner='" . $_SESSION['Username'] . "'"))) {
  $Result = "Could not retrieve user information from the database.<br><b>MySQL Said:</b><br>" . mysql_error();
}
$ThisAccount = mysql_fetch_array($UserQuery);

  if ($_GET['Action'] == "Update") {
     require("includes/class.vsftpd.php");
     $PGPL->SudoLogin();
	 $PGPL_FTP->Username = $_POST['Username'];
	 $PGPL_FTP->Password = $_POST['Password'];
	 if (!$PGPL_FTP->ChangePasswd()) {
	     $Result = "An error occurred attempting to update your password.";
	     $PGPL->SudoLogout();
	 } else {
	     $PGPL->SudoLogout();
		 if (!($SQL_ERROR = mysql_query("UPDATE FTP_Records Set Password='" . md5($_POST['Password']) . "' WHERE Username='" . $_POST['Username'] . "'"))) {
		    $Result = "Could not update database password.<br><b>MySQL Said:</b><br>" . mysql_error();
		 } else {
		   if (!$Result) { echo "<script language=\"JavaScript\">location.href = 'FTP_ViewUsers.php';</script>"; }
	     }
	 }		
  }   
  if ($_GET['Action'] == "DeleteUser") {
  
     if (strtolower($_GET['Username']) == $_SESSION['Username']) {
	 // Delete Account Holder
	 require("includes/class.apache2.php");
	 require("includes/class.vsftpd.php");
	    $Webs = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "'");
		$FTPs = mysql_query("SELECT * FROM FTP_Records WHERE Owner='" . $_SESSION['Username'] . "'");
		$TotalWebs = mysql_num_rows($Webs);
		// No websites to remove from the system		
		if (mysql_num_rows($Webs) == 0) {                
			 $PGPL->SudoLogin();
			 // Remove any 'ROOT LEVEL' FTP users
			 while ($Master = mysql_fetch_array($FTPs)) {
			         if ($Master['Username'] != $_SESSION['Username']) {
						 mysql_query("DELETE FROM FTP_Records WHERE Username='" . $Master['Username'] . "'");
						 $PGPL_FTP->Username = $Master['Username'];
						 if (!$PGPL_FTP->DeleteUser(0)) {
							$Result .= "An error occurred while attempting to delete the local system account for " . $Master['Username'] . ".";
						 }
					}
			 }	 
			 // Remove the account holder    
			 $PGPL_FTP->Username = $_SESSION['Username'];
			 $PGPL_FTP->RemoveHomeDir($ArrApache[0] . $_SESSION['Username']);
			 $PGPL_FTP->RemoveDirs($ArrApache[1] . $_SESSION['Username']);
			 if (!$PGPL_FTP->DeleteUser(2)) {
			    $Result .= "An error occurred while attempting to delete the account holders local system user and group accounts.";
			 }
			 mysql_query("DELETE FROM FTP_Records WHERE Username='" . $_SESSION['Username'] . "'");
			 if (!$Result) {
			    $PGPL->SudoLogout();
			    echo "<script language=\"JavaScript\">location.href = 'FTP_ViewUsers.php';</script>"; 
			 }			 
		}
		// Remove all 'standard' ftp user accounts
	    while ($ThisWeb = mysql_fetch_array($Webs)) { 
			  if (!($ThisWeb['ServerName'] == "Root")) { 		   	    
				 if (!$PGPL_Apache2->DelVhost($ThisWeb['ServerName'],$ArrApache[3])) {
				    $Result .= "An error occurred while trying to remove the website virtual host container for " . $ThisWeb['ServerName'] . ".";
				 }
				 if (!($MySQL_ERROR = mysql_query("DELETE FROM HTTP_Records WHERE ServerName='" . $ThisWeb['ServerName'] . "'"))) {
				    $Result .= "Could not execute delete query.<br><b>MySQL Said:</b><br>" . mysql_error();
				 }			   
				 $FTPusers = mysql_query("SELECT * FROM FTP_Records WHERE Website='" . $ThisWeb['ServerName'] . "'");
				 while ($ArrFTP = mysql_fetch_array($FTPusers)) {
					   mysql_query("DELETE FROM FTP_Records WHERE Username='" . $ArrFTP['Username'] . "'");				  
					   $PGPL_FTP->Username = $ArrFTP['Username'];
					   if (!$PGPL_FTP->DeleteUser(0)) {
						   $Result .= "Could not delete the FTP account for user " . $ArrFTP['Username'] . ".";
					   }	 
				 }
				 /*
				  FOR WEBALIZER 
				 */  
				  unlink($WebalizerHome . "/" . $ThisWeb['ServerName'] . ".conf");
				  /*
				  END WEBALIZER 
				 */ 	
			  }
	    }
		// Remove 'ROOT LEVEL' FTP users
	    if (!($DeleteRootLevel = mysql_query("SELECT * FROM FTP_Records WHERE Owner='" . $_SESSION['Username'] . "' AND Username!='" . $_SESSION['Username'] . "'"))) {
		   $Result .= "Could not query the database for root level users.<br><b>MySQL Said:</b><br>" . mysql_error();
	    } else {
		   while ($Master = mysql_fetch_array($DeleteRootLevel)) {
			     mysql_query("DELETE FROM FTP_Records WHERE Username='" . $Master['Username'] . "'");
			     $PGPL_FTP->Username = $Master['Username'];
			     if (!$PGPL_FTP->DeleteUser(0)) {
				    $Result .= "An error occurred while attempting to delete the local system account for " . $Master['Username'] . ".";
			     }
		  }
	    }
		// Remove the account holder from the local system account
		$PGPL_FTP->Username = $_SESSION['Username'];
		$PGPL_FTP->HomeDir = $ArrApache[0] . $_SESSION['Username'];
		if (!$PGPL_FTP->DeleteUser(2)) {
		   $Result .= "An error occurred while attempting to delete the local system user & group account for " . $_SESSION['Username'] . ".";
	    }
		// Remove the account from MySQL
		mysql_query("DELETE FROM FTP_Records WHERE Owner='" . $_SESSION['Username'] . "' AND Username='" . $_SESSION['Username'] . "'");
		// Remove the account holders home directory
		if (is_dir($ArrApache[0] . $_SESSION['Username'])) {
		   if (!$PGPL_FTP->RemoveHomeDir($ArrApache[0] . $_SESSION['Username'])) {
			 $Result .= "Could not delete the directory located at " . $ArrApache[0] . $_SESSION['Username'];
		   }
		}
		// Remove account holders log directory		
		if (is_dir($ArrApache[1] . $_SESSION['Username'])) {
		   if (!$PGPL_FTP->RemoveDirs($ArrApache[1] . $_SESSION['Username'])) {
			 $Result .= "Could not delete the directory located at " . $ArrApache[1] . $_SESSION['Username'];
		   }
		}
		// If no errors, return account holder to FTP users page
		if (!$Result) {
		  $PGPL->SudoLogout();
		  echo "<script language=\"JavaScript\">location.href = 'FTP_ViewUsers.php';</script>";
		}		 			   
	 }	else {
	     // Delete standard FTP user
		 require("includes/class.vsftpd.php");
		 $PGPL->SudoLogin(); 
		 $PGPL_FTP->Username = strtolower($_GET['Username']);
		 if (!$PGPL_FTP->DeleteUser(0)) {
			$Result .= "An error occurred while attempting to remove the local system account for user " . strtolower($_GET['Username']) . ".";
			$PGPL->SudoLogout();
		 } else { 
		    mysql_query("DELETE FROM FTP_Records WHERE Username='" . strtolower($_GET['Username']) . "'");
			$PGPL->SudoLogout();
			echo "<script language=\"JavaScript\">location.href = 'FTP_ViewUsers.php';</script>"; 
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
// Check the username
  var InvalidChars = "!@#$%^&*()+=[]\\\'.;,/{}|\":<>?`+= ";
   for (var i = 0; i < document.NewUser.Username.value.length; i++) {
  	 if (InvalidChars.indexOf(document.NewUser.Username.value.charAt(i)) != -1) {
  	  alert ("The specified user name is invalid. User names may only contain ASCII text, numbers, or an underscore.");
  	 return false;
   	 }
   }
   if (document.NewUser.Username.value.length < 1) {
    alert ("You must enter a username name.");
    return false;
   }
   if (document.NewUser.Password.value == '') {
    alert('We do not allow blank passwords on our network!');
    return false;
   }
   if (document.NewUser.Password.value != document.NewUser.Password2.value) {
    alert('Passwords do not match!');
    return false;
   }
 document.NewUser.submit();
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
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3"><div align="center" class="highlight">Please enter the new password for <b><i><?php echo $_GET['Username']; ?></i></b>.</b></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3"><div align="center"><?php echo $Result; ?></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">
			  <form name="NewUser" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Update" method="post">
				<table width="192" border="0" align="center" class="menu">
                  <tr>
                    <td width="68">Username:</td>
                    <td width="114"><input name="Username" readonly type="text" id="Username" value="<?php echo $_GET['Username']; ?>"></td>
                  </tr>
                  <tr>
                    <td>Password:</td>
                    <td><input name="Password" type="password" id="Password"></td>
                  </tr>
                  <tr>
                    <td>Confirm:</td>
                    <td><input name="Password2" type="password" id="Password2"></td>
                  </tr>
				  <?php if (strlen($ThisAccount['Website']) > 1) { ?>
                  <tr>
                    <td>Website:</td>
                    <td><input type="text" readonly name="Website" value="<?php echo $ThisAccount['Website']; ?>"></td>
                  </tr>
				  <?php } ?>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Update" onclick="JavaScript:ValidateElements();"></td>
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