<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");


  if (isset($_GET['Action']) && $_GET['Action'] == "UpdatePassword") {
   require("includes/DisableDemoAccount.php");
    if (!($UpdatePass = mysql_query("UPDATE Clients Set Password='" . md5($_POST['Password']) . "' WHERE Username='" . $_SESSION['Username'] . "'"))) {
	 $Result = "The system could update your password.<br><b>MySQL Said:</b><br>" . mysql_error();
	} else {
	  echo "<script language=\"JavaScript\">alert('Your password has been successfully reset.');location.href = 'logout.php';</script>";
	}
  }
  
  if (isset($_GET['Action']) && $_GET['Action'] == "UpdateEmail") {
   require("includes/DisableDemoAccount.php");
    if (!($UpdateEmail = mysql_query("UPDATE Clients Set Email='" . $_POST['Email'] . "' WHERE Username='" . $_SESSION['Username'] . "'"))) {
	 $Result = "The system could update your email address.<br><b>MySQL Said:</b><br>" . mysql_error();
	} else {
	  echo "<script language=\"JavaScript\">alert('Your Email has been successfully reset.');location.href = 'AcctMgmt.php';</script>";
	}
  }


?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<script language="javascript">
function ValidatePassword() {
  if (document.ChangePass.Password.value != document.ChangePass.Password2.value) {
   alert('Passwords do not match!');
   return false;
  }
  if (document.ChangePass.Password.value == '') {
   alert('We do not allow blank passwords on our network!');
   return false;
  }
 document.ChangePass.submit();  
}
function VerifyEmail() {
 if (document.ChangeEmail.Email.value.length >0) {
	 i=document.ChangeEmail.Email.value.indexOf("@")
	 j=document.ChangeEmail.Email.value.indexOf(".",i)
	 k=document.ChangeEmail.Email.value.indexOf(",")
	 kk=document.ChangeEmail.Email.value.indexOf(" ")
	 jj=document.ChangeEmail.Email.value.lastIndexOf(".")+1
	 len=document.ChangeEmail.Email.value.length

 	if ((i>0) && (j>(1+1)) && (k==-1) && (kk==-1) && (len-jj >=2) && (len-jj<=3)) {
 	} else {
 		alert("Please enter a valid email address.");
		return false;
 	}
 } 
 if (document.ChangeEmail.Email2.value != document.ChangeEmail.Email.value) {
   alert('Email addresses do not match!');
   return false;
 } 
document.ChangeEmail.submit();
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
                <td colspan="3"><p align="center" class="BodyHeader">Profile Management Center</p>
                  <p>&nbsp;</p>                  <table width="400"  border="0" align="center" class="menu">
                  <tr class="menu">
                    <td>Last Login: </td>
                    <td><?php echo $_SESSION['LastLogin']; ?></td>
                    <td>From IP:</td>
                    <td><?php echo $_SESSION['LastIP']; ?></td>
                  </tr>
                  <tr>
                    <td width="22%">Project Role:</td>
                    <td width="27%"><?php echo $_SESSION['SiteRole']; ?>&nbsp;</td>
                    <td width="17%">User Since: </td>
                    <td width="34%"><?php echo $_SESSION['MemberSince']; ?>&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="4">Logged in as: <?php echo $_SESSION['Username']; ?></td>
                    </tr>
                </table>                </td>
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
                <td colspan="3"><div align="center" class="highlight"><?php if (!(isset($_GET['Action'])) || $_GET['Action'] == "") { ?>Choose a profile option below to update or modify your personal information.<?php } ?></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">
				<?php if (!(isset($_GET['Action'])) || $_GET['Action'] == "") { ?>
				 <table width="75%"  border="0" align="center" class="menu">
                    <tr>
                      <td width="46%"><div align="center">
                        <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Action=ChangeEmail"><img src="images/Profile_Email.gif" width="32" height="32" border="0"></a><br>
                          <a href="<?php echo $_SERVER['PHP_SELF']; ?>?Action=ChangeEmail">Manage Email</a> </p>
                      </div></td>
                      <td width="54%"><div align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Action=ChangePass"><img src="images/Profile_Password.GIF" width="32" height="32" border="0"></a><br>
                          <a href="<?php echo $_SERVER['PHP_SELF']; ?>?Action=ChangePass">Manage Password </a></div></td>
                    </tr>
                </table>
			  <?php } ?>				
				
				<?php if (isset($_GET['Action']) && $_GET['Action'] == "ChangeEmail") { 
				if (!($UserQuery = mysql_query("SELECT Email From Clients WHERE Username='" . $_SESSION['Username'] . "'"))) {
					 echo "Could not retrieve Email address from the database.<br><b>MySQL Said:</b><br>" . mysql_error();
					} else {
					 $ThisUser = mysql_fetch_array($UserQuery);
					}
				?>
			   <form name="ChangeEmail" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=UpdateEmail" method="post">
				<div align="center" class="highlight">
				  <p>Please type your Email address into the first textbox. You will need to confirm the address in the second textbox. When you are done, click the update button. </p>
				  <p><span class="style2">Your current registered Email address is:</span> <font color="#000000"><b><i><?php echo $ThisUser['Email']; ?></i></b></font><br>
				        </p>
				</div>
				<table width="50%"  border="0" align="center" class="menu">
                  <tr>
                    <td>Email:</td>
                    <td><input name="Email" type="text" id="Email"></td>
                  </tr>
                  <tr>
                    <td>Confirm:</td>
                    <td><input name="Email2" type="text" id="Email2"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Update" onClick="JavaScript:VerifyEmail();"></td>
                  </tr>
                  <tr>
                    <td width="31%"></td>
                    <td width="69%"></td>
                  </tr>
                </table>
				</form>
				<?php } ?>
				
				<?php if (isset($_GET['Action']) && $_GET['Action'] == "ChangePass") { ?>
			   <form name="ChangePass" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=UpdatePassword" method="post">
			     <div align="center">
			       <p><span class="highlight">Please type your new password into the first textbox. You will need to confirm the password in the second textbox. When you are done, click the reset button. </span><br>
			       </p>
			       <p>&nbsp;</p>
			     </div>
			     <table width="50%"  border="0" align="center" class="menu">
                   <tr>
                     <td>Password:</td>
                     <td><input name="Password" type="password" id="Password"></td>
                   </tr>
                   <tr>
                     <td>Confirm:</td>
                     <td><input name="Password2" type="password" id="Password2"></td>
                   </tr>
                   <tr>
                     <td>&nbsp;</td>
                     <td><input type="button" value="Update" onClick="JavaScript:ValidatePassword();"></td>
                   </tr>
                   <tr>
                     <td width="31%"></td>
                     <td width="69%"></td>
                   </tr>
                 </table>
			   </form>
				<?php } ?>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="14%">&nbsp;</td>
                <td width="35%">&nbsp;</td>
                <td width="33%">&nbsp;</td>
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