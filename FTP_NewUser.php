<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");


$WebQuery = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "' ORDER BY ServerName");
$FTP_UserQuery = mysql_query("SELECT * FROM FTP_Records WHERE Owner='" . $_SESSION['Username'] . "'");

  if ($_POST['Username'] != "" && $_GET['Action'] == "Process") {
    require("includes/class.vsftpd.php");
    if ($IS_Local = $PGPL_FTP->LocalSystemAccount($_POST['Username'])) {
	       echo "<script language=\"JavaScript\">alert('" . $_POST['Username'] . " is already a registered local system account. Please choose another user name.');location.href = 'FTP_NewUser.php';</script>";
	       exit();
	}
	$Apache = explode(",",$Apache2);
	// Get quotas for this users membership
    $Quota = explode("\r\n",$MemberPlans);			 
	foreach ($Quota as $Value) {
	  $Data = explode(",",$Value);
		if ($Data[0] == $_SESSION['SiteRole']) {
		   $UserCap = $Data[9];
		  }
	}
	// Add total number of FTP users so far for this website
	$TotalUsers = mysql_query("SELECT Username FROM FTP_Records WHERE Owner='" . $_SESSION['Username'] . "' AND Website='" . $_POST['Website'] . "'");
	if (mysql_num_rows($TotalUsers) >= $UserCap && $UserCap !=0) { 
	   echo "<script language=\"JavaScript\">alert('You have exceeded your FTP user quota of " . $UserCap . " users per website. Operation aborted.');location.href = 'FTP.php';</script>";
	   exit(); 
	}
	// Make sure this user doesnt already exist
    $UsrChk = mysql_query("SELECT * FROM FTP_Records WHERE Username='" . strtolower($_POST['Username']) . "'");
	if (mysql_num_rows($UsrChk) > 0) { echo "<script language=\"JavaScript\">alert('" . strtolower($_POST['Username']) . " is already a registered FTP user name! Please select a different username.');location.href = 'FTP.php';</script>"; return false; } 
    // Root Level Access To FTP
	if ($_POST['Website'] == "Root") { 
	   $PGPL->SudoLogin();
	   if (!($ThisQuery = mysql_query("INSERT INTO FTP_Records(Username,Password,Owner,Website,DaRoot,Unique_ID) VALUES('" . strtolower($_POST['Username']) . "','" .
					                   md5($_POST['Password']) . "','" . $_SESSION['Username'] . "','Root','1','" . md5(mktime()) . "')"))) {
		  $Result .= "Could not add the new FTP user to the system.<br><b>MySQL Said:</b><br>" . mysql_error(); 
	   }
	   // Create user
	   $PGPL_FTP->Username = strtolower($_POST['Username']);
	   $PGPL_FTP->Password = $_POST['Password'];
	   $PGPL_FTP->HomeDir = $Apache[0] . $_SESSION['Username'];
	   if (!$PGPL_FTP->CreateUser(2)) {
		  $Result .= "An error occurred while attempting to create the new FTP user " . strtolower($_POST['Username']) . ".";
	   } else {
		  $PGPL->SudoLogout();
		  if (!$Result) { echo "<script language=\"JavaScript\">location.href = 'FTP_ViewUsers.php';</script>"; }
	   }
    } else {
	   // Ordinary user
	   if (!($Insert = mysql_query("INSERT INTO FTP_Records(Username,Password,Owner,Website,Unique_ID) VALUES('" . strtolower($_POST['Username']) . "','" . md5($_POST['Password']) . "','" . $_SESSION['Username'] . "','" . $_POST['Website'] . "','" . md5(mktime()) . "')"))) {
		  $Result = "Could not create the new FTP user account.<br><b>MySQL Said:</b><br>" . mysql_error();
	   }
	   $PGPL->SudoLogin();
	   $PGPL_FTP->Username = strtolower($_POST['Username']);
	   $PGPL_FTP->Password = $_POST['Password'];
	   $PGPL_FTP->HomeDir = $Apache[0] . $_SESSION['Username'] . "/" . $_POST['Website'];
	   if (!$PGPL_FTP->CreateUser(2)) {
		  $Result .= "An error occurred while attempting to create the new FTP user " . strtolower($_POST['Username']) . ".";
		  $PGPL->SudoLogout();
	   } else {
		  $PGPL->SudoLogout();
		  if (!$Result) { echo "<script language=\"JavaScript\">location.href = 'FTP_ViewUsers.php';</script>"; }
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
  var InvalidChars = "!@#$%^&*()+=[]\\\'.;,/{}|\":<>?`+=_- ";
   for (var i = 0; i < document.NewUser.Username.value.length; i++) {
  	 if (InvalidChars.indexOf(document.NewUser.Username.value.charAt(i)) != -1) {
  	  alert ("The specified user name is invalid. User names may only contain ASCII text, or numbers.");
  	 return false;
   	 }
   }
   if (document.NewUser.Username.value.length < 1) {
    alert ("You must enter a username name.");
    return false;
   }
   if (parseInt(document.NewUser.Username.value)) {
    alert("Your user name can not start with a number!");
	return false;
   }
   if (document.NewUser.Username.value.toUpperCase() == "ROOT") {
    alert ("HaCkEr! Change that user name NOW!");
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
   if (document.NewUser.Website.value == "Root") {
     var decision = confirm("You are about to give "+document.NewUser.Username.value+" access to your root directory structure, which means that they will have access to ANY and ALL websites under your account! Are you sure you want to do this?");
       if (decision == false) { return false; }
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
                <td colspan="3"><div id="Header" align="center" class="highlight">Please enter the username / password pair for the new FTP user, and select the website that you want this user to be able to access. </div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3"><div align="center"><?php echo $Result; ?></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">
				<?php if (mysql_num_rows($WebQuery) > 0 || mysql_num_rows($FTP_UserQuery) > 0) { ?>				
			  <form name="NewUser" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Process" method="post">
				<table width="192" border="0" align="center" class="menu">
                  <tr>
                    <td width="68">Username:</td>
                    <td width="114"><input name="Username" type="text" id="Username"></td>
                    <script language="javascript">document.NewUser.Username.focus()</script>
				  </tr>
                  <tr>
                    <td>Password:</td>
                    <td><input name="Password" type="password" id="Password"></td>
                  </tr>
                  <tr>
                    <td>Confirm:</td>
                    <td><input name="Password2" type="password" id="Password2"></td>
                  </tr>
                  <tr>
                    <td>Website:</td>
                    <td>
					 <select name="Website">					 
					 <?php
					   while ($ThisAccount = mysql_fetch_array($WebQuery)) {
					     echo "<option value=\"" . $ThisAccount['ServerName'] . "\">" . $ThisAccount['ServerName'] . "</option>";
					   }
					 ?>
					 <option value="Root">*** Root Level Access ***</option>
                     </select>
					</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" value="Create" onclick="JavaScript:ValidateElements();"></td>
                  </tr>
                </table>
			  </form>	
			  <?php } else { 
			  ?>
			   <script language="javascript">
			    document.getElementById('Header').innerHTML = '<div align=\"center\"><b><i><font color=\"#000000\">You do not have any FTP users configured at this time.<br>You must create a new website before any FTP users can become active.</font></i></b></div>';
			   </script>
			  <?php } ?>
			    </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="22%">&nbsp;</td>
                <td width="25%">&nbsp;</td>
                <td width="35%">&nbsp;</td>
              </tr>
            </table>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p></td>
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