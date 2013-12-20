<?php
require("includes/DB_ConnectionString.php");
require("includes/DisableDemoAccount.php");

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {
   
    if (!($SelectQuery = mysql_query("SELECT * FROM Clients WHERE Username='" . strtolower($_POST['Username']) . "' AND SecretPhrase='" . 
	                     md5($_POST['SecretPhrase']) . "'"))) {
	  $Result = "Could not execute SQL select statement.<br><b>MySQL Said:</b><br>" . mysql_error();
	} else {
	  if (mysql_num_rows($SelectQuery) > 0) {
	    mysql_query("UPDATE Clients Set Password='" . md5($_POST['Password']) . "' WHERE Username='" . strtolower($_POST['Username']) . "'");
        $Result .= "Your password has been successfully reset.";		
	  } else {
	   $Result .= "You have submitted invalid account details. Your password could not be reset.";
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
.style6 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #FF0000;
	font-style: italic;
	font-weight: bold;
}
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
          <td height="212"><p class="style2">&nbsp;</p>
            <p class="style2">Reset Your Password </p>            
            <p align="left" class="style3">To reset your password, you must enter the secret phrase that was entered when you first created your account. If by chance you forgot your password AND your secret phrase, please contact our support team for further assistance.   </p>            
            <p align="center" class="style3 style5"><?php echo "<br>" . $Result . "<br>"; ?></p>
           <form name="Signup" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		    <table width="34%"  border="1" align="center" bordercolor="#F9F9F9">
              <tr>
                <td width="41%" class="style3">Username:</td>
                <td width="59%"><input name="Username" type="text" id="Username"></td>
              </tr>
              <tr>
                <td class="style3">Password:</td>
                <td><input name="Password" type="password" id="Password"></td>
              </tr>
              <tr>
                <td class="style3">Confirm:</td>
                <td><input name="Password2" type="password" id="Password2"></td>
              </tr>
              <tr>
                <td class="style6">Secret Phrase: </td>
                <td><input name="SecretPhrase" type="password" id="SecretPhrase"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><div align="right">
				  <input type="hidden" name="Action" value="ProcessSignup">
                  <input type="submit" value="Reset Password">
                </div></td>
              </tr>
            </table>       
		  </form>     
           <p>&nbsp;</p>
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