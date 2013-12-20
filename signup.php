<?php
session_start();
if (!$_SESSION['Disclaimer']) {
   echo "<script language=\"JavaScript\">alert('Viewing the disclaimer is required before you can sign up for your free account. Please take a few minutes to carefully review the disclaimer.\\r\\n\\r\\nThank you,\\r\\nThe Project GPL Hosting Team');location.href = 'disclaimer.php';</script>"; exit();
}

require("includes/DB_ConnectionString.php");

 if (isset($_GET['Action']) && $_GET['Action'] == "ProcessSignup") {
  
  // Make sure that this user doesnt already exist as a system account.
     require("includes/class.pgpl.php");
     require("includes/class.vsftpd.php");
	 if ($IS_Local = $PGPL_FTP->LocalSystemAccount(strtolower($_POST['Username']))) {
 	   echo "<script language=\"JavaScript\">alert('" . strtolower($_POST['Username']) . " is already a registered local system account. Please choose a different user name.');location.href = 'signup.php';</script>";
	   exit();
	 }
  // Make sure that this user doesnt already exist in the database for the control panel.
  if (!($DuplicateQuery = mysql_query("SELECT * FROM Clients WHERE Username='" . strtolower($_POST['Username']) . "'"))) {
    echo "Could not execute select query to validate username.<br><b>MySQL Said:</b><br>" . mysql_error();
  } else {
     if (mysql_num_rows($DuplicateQuery) > 0) {
	    echo "<script language=\"JavaScript\">alert('This user name has already been taken! Please choose a different user name.');location.href = 'signup.php';</script>"; exit();
	    $NoGood = 1;
	 }
	 if (!($DuplicateQuery2 = mysql_query("SELECT * FROM Clients WHERE Email='" . strtolower($_POST['Email']) . "'"))) {
	   echo "Could not execute select query to validate email.<br><b>MySQL Said:</b><br>" . mysql_error();
     }
	 if (mysql_num_rows($DuplicateQuery2) > 0) {
		  echo "<script language=\"JavaScript\">alert('This Email address has already been registered!');location.href = 'signup.php';</script>"; exit();
	      $NoGood = 1;
	 }
	 if (!$NoGood) {
		 $GUID = md5(mktime());
		 if (!($ActivationQuery = mysql_query("INSERT INTO Pending_Activations(Username,Password,Email,SecretPhrase,IP,UniqueID) VALUES('" . 
												strtolower($_POST['Username']) . "','" . md5($_POST['Password']) . "','" . strtolower($_POST['Email']) . "','" . 
												md5($_POST['SecretPhrase']) . "','" . $_SERVER['REMOTE_ADDR'] . "','" . $GUID . "')"))) {
			$Result = "Could not process signup request.<br><b>MySQL Said:</b><br>" . mysql_error();
		} else {
			 if (!(mail($_POST['Email'],"Account Activation!","Hello " . ucfirst($_POST['Username']) . ",\r\n\r\nThank you for joining the GPL Hosting community! Below is the link required to activate your account.\r\n\r\nhttp://www.gplhosting.org/activation.php?Action=Activate&GUID=" . 
				$GUID,"FROM: GPL Hosting Community"))) {
				 $Result = "Could not send confirmation Email. Please report this problem to our support team immediately."; 
			 } else {
				 $Result = "Sign-up complete. Please check your mailbox at <b>" . $_POST['Email'] . "</b> for an activation link!";
			 }
		} 
    }	
  } 
 }
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<style type="text/css">
.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style4 {font-size: 10px}
.style5 {color: #FF0000}
</style>
<script language="javascript">
function ValidateSignup() { 
  // Check the username
  var InvalidChars = "!@#$%^&*()+=[]\\\'.;,/{}|\":<>?`_+= ";
   for (var i = 0; i < document.Signup.Username.value.length; i++) {
  	 if (InvalidChars.indexOf(document.Signup.Username.value.charAt(i)) != -1) {
  	  alert ("The specified user name is invalid. User names may only contain ASCII text, numbers, or a period.");
  	 return false;
   	 }
   }  
   if (document.Signup.Username.value.length < 1) {
    alert ("You must enter a username name.");
    return false;
   }
    if (parseInt(document.Signup.Username.value)) {
    alert("Your user name can not start with a number. Please choose a different username.");
	return false;
   }
   // Checks the email address
	 i=document.Signup.Email.value.indexOf("@")
	 j=document.Signup.Email.value.indexOf(".",i)
	 k=document.Signup.Email.value.indexOf(",")
	 kk=document.Signup.Email.value.indexOf(" ")
	 jj=document.Signup.Email.value.lastIndexOf(".")+1
	 len=document.Signup.Email.value.length
 	  if ((i>0) && (j>(1+1)) && (k==-1) && (kk==-1) && (len-jj >=2) && (len-jj<=3)) {
 	  } else {
 		alert("If you do not enter a valid Email address, the activation Email will not reach you! Please enter a valid Email address.");
		return false;
 	  } 
   if (document.Signup.Password.value == '') {
    alert('We do not allow blank passwords on our network!');
    return false;
   }
   if (document.Signup.Password.value != document.Signup.Password2.value) {
    alert('Passwords do not match!');
    return false;
   }
   if (document.Signup.SecretPhrase.value == '') {
    alert('You must enter a value for the secret phrase!');
    return false;
   }
   if (!(document.getElementById('Agreed').checked == true)) {
    alert('You must agree to the terms and conditions before submitting!');
    return false;
   }
 document.Signup.submit();
}
function Enter() {
 if (event.keyCode==13) {
  ValidateSignup();
 }
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
          <td height="212"><p class="style2">&nbsp;</p>
            <p class="style2">Sign-Up For A FREE Account! </p>            
            <p align="left" class="style3">Please create a username and password which you will use to access your account on our network. <strong>DO NOT FORGET YOUR SECRET PHRASE</strong>, as it will be used to reset your password should you happen to forget it. </p>
            <p align="left" class="style3">Enjoy your FREE Hosting account! </p>
            <p align="center" class="style5"><?php echo $Result; ?>&nbsp;</p>
           <form name="Signup" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=ProcessSignup" method="POST">
		    <table width="34%"  border="1" align="center" bordercolor="#F9F9F9">
              <tr>
                <td width="41%" class="style3">Username:</td>
                <td width="59%"><input name="Username" type="text" id="Username" onKeyPress="JavaScript:Enter();"></td>
              </tr>
              <tr>
                <td class="style3">Email:</td>
                <td><input name="Email" type="text" id="Email" onKeyPress="JavaScript:Enter();"></td>
              </tr>
              <tr>
                <td class="style3">Password:</td>
                <td><input name="Password" type="password" id="Password" onKeyPress="JavaScript:Enter();"></td>
              </tr>
              <tr>
                <td class="style3">Confirm:</td>
                <td><input name="Password2" type="password" id="Password2" onKeyPress="JavaScript:Enter();"></td>
              </tr>
              <tr>
                <td class="style3">Secret Phrase: </td>
                <td><input name="SecretPhrase" type="text" id="SecretPhrase" onKeyPress="JavaScript:Enter();"></td>
              </tr>
              <tr>
                <td colspan="2"><input type="checkbox" name="Agreed" id="Agreed">
                  <span class="menu">
                  <label for="Agreed" style="cursor:hand;">I agree to the <a href="disclaimer.php">terms &amp; conditions</a>.</label>
                  </span></td>
                </tr>
              <tr>
                <td>&nbsp;</td>
                <td><div align="right">
				  <input type="hidden" name="Action" value="ProcessSignup">
                  <input type="button" value="Register!" onClick="JavaScript:ValidateSignup()">
                </div></td>
              </tr>
            </table>       
		  </form>     
           <p align="center"><span class="menu"><label for="Agreed" style="cursor:hand;"></label>
           </span></p>
           <p class="style3 style4"><em><strong><span class="style5">NOTE:</span> Your password is about to be stored using irreversible encryption, which means that it can not be recovered under ANY circumstances. Your secret phrase will allow you to reset your password should you lose it, however, if your secret phrase is lost or forgotten, so is your account!&nbsp; <span class="style5">THE SECRET PHRASE IS CaSe SeNsItIvE ...</span> </strong></em></p>            
           <p class="style3">&nbsp;</p>
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