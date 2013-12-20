<?php
session_start();
require("includes/GlobalConfigs.php");

if (isset($_SESSION['Username']) && $_SESSION['Username'] != "" && $_SESSION['LOGGED_IN'] === true) {
  echo "<script language=\"JavaScript\">location.href = 'loggedin.php';</script>";
}

if (isset($_GET['Action']) && $_GET['Action'] == "ProcessLogin") {

   require("includes/DB_ConnectionString.php");   
   if (!($SelectQuery = mysql_query("SELECT * FROM Clients WHERE Username='" . $_POST['Username'] . "' AND Password='" . $_POST['Password'] . "'"))) {
       echo "<script language=\"JavaScript\">alert('You could not be logged in using the credentials supplied. Please try again.');</script>";
   } else {
      $ThisClient = mysql_fetch_array($SelectQuery);
	  
	    if ($rows = mysql_num_rows($SelectQuery) > 0) {
		
	      $_SESSION['Username'] = strtolower($_POST['Username']);
		   $ARR_LastLogin = explode("-",$ThisClient['LastLogin']);
		  $_SESSION['LastLogin'] = $ARR_LastLogin[1] . "-" . $ARR_LastLogin[2] . "-" . $ARR_LastLogin[0];
		  $_SESSION['SiteRole'] = $ThisClient['Plan'];
 		   $ARR_Created = explode("-",$ThisClient['Created']);
		  $_SESSION['MemberSince'] = $ARR_Created[1] . "-" . $ARR_Created[2] . "-" . $ARR_Created[0];
		  $_SESSION['LastIP'] = $ThisClient['LastLoginIP'];
		  $_SESSION['LOGGED_IN'] = true;
		  
		    if (!($UpdateQuery = mysql_query("UPDATE Clients Set LastLogin='" . date("Y-m-d") . "' WHERE Username='" . $_POST['Username'] . "'"))) {
			  echo "<script language=\"JavaScript\">alert('Could not execute SQL statement for update query.');</script>";
			} else {
			  if (!($UpdateQuery2 = mysql_query("UPDATE Clients Set LastLoginIP='" . $_SERVER['REMOTE_ADDR'] . "' WHERE Username='" . $_POST['Username'] . "'"))) {
			    echo "<script language=\"JavaScript\">alert('Could not execute SQL statement for update query.');</script>";
			  } else {
			    echo "<script language=\"JavaScript\">location.href = 'http://" . $_SERVER['SERVER_NAME'] . "/loggedin.php';</script>";
			  }
			}		
		} else {
		  echo "<script language=\"JavaScript\">alert('You could not be logged in using the credentials supplied. Please try again.');</script>";
        }
   }  
}
if ($UseSSL == 1) {
  $Protocol = "https://";
} else {
  $Protocol = "http://";
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
.style6 {color: #0000FF}
</style>
<script language="JavaScript" src="includes/MD5.js"></script>
<script language="javascript">
function ValidateLogin() {
 // Check for user input in both fields
 if (document.login.Username.value == '') {
   alert('You must enter a username.');
   return false;
 }
 if (document.login.Password.value == '') {
  alert(document.login.Password.value);
  return false;
 }
 document.login.Password.value = hex_md5(document.login.Password.value);
document.login.submit();
}
function Enter() {
 if (event.keyCode==13) {
  ValidateLogin();
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
            <p class="style2">Login To Your  FREE Account! </p>            
            <p align="left" class="style3">Please enter your username or email address followed by your password. If you would like to login using a demo account, simply click the login button now.</p>
            <p align="left" class="style3">&nbsp;</p>
            <form name="login" action="<?php echo $Protocol . $_SERVER['SERVER_NAME'] . "/manage.php"; ?>?Action=ProcessLogin" method="POST">
		    <table width="34%"  border="1" align="center" bordercolor="#F9F9F9">
              <tr>
                <td width="41%" class="style3">Username:</td>
                <td width="59%"><input name="Username" type="text" id="Username" value="demo" onFocus="JavaScript:this.value = '';" onKeyPress="JavaScript:Enter();"></td>
              </tr>
              <tr>
                <td class="style3">Password:</td>
                <td><input name="Password" type="password" id="Password" value="demo_007" onFocus="JavaScript:this.value = '';" onKeyPress="JavaScript:Enter();"></td>
              </tr>
              <tr>
                <td class="style1"><div align="center"><a href="ResetPassword.php">Forgot Password</a></div></td>
                <td><div align="right">
				  <input type="hidden" name="Action" value="ProcessLogin">
                  <input type="button" value="Login!" onClick="JavaScript:ValidateLogin()">
                </div></td>
              </tr>
            </table>       
		  </form>     
            <table width="254" border="0" align="center">
              <tr>
                <td width="248"><p align="center" class="style2"><em><marquee scrolldelay="200" behavior="alternate"><a href="https://www.paypal.com/xclick/business=paypal%40pc-technics.com&item_name=Project+GPL+Hosting+Donations&item_number=GPL+Hosting+Donations&no_shipping=1&return=http%3A//www.gplhosting.org/paypal_return.php%3FConfirm%3D1%26Purchase%3DDonation&cancel_return=http%3A//www.gplhosting.org&no_note=1&tax=0&currency_code=USD" target="_blank" class="style3">Donate To Project GPL Hosting!</a>
                </marquee></em></p></td>
              </tr>
            </table>            
            <p align="center" class="style2">&nbsp;</p>

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