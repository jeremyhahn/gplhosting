<?php
session_start();
require("../includes/AdminSecurity.php");
require("../includes/DB_ConnectionString.php");
require("../includes/AdminSecurity.php");

if (isset($_GET['Action']) && $_GET['Action'] == "SendMail") {

  if (!($SelectQuery = mysql_query("SELECT * FROM Clients"))) {
    echo mysql_error();
  } else {
    $Count = mysql_num_rows($SelectQuery);
	  while ($list = mysql_fetch_array($SelectQuery)) {	  
	     if (!(mail($list['Email'],$_POST['Subject'],"Hello " . ucfirst($list['Username']) . ",\r\n\r\n" . $_POST['Body'] . "\r\n\r\n\r\n\r\nThis Email is being sent from an unattended mailbox. Please do not reply to this message.\r\n" . $_POST['Signature'],"FROM:newsletter@gplhosting.org"))) {
		    echo "Could not send an email to: <b>" . $list['Email']; 
		  }
		  $Result .= "Successfully delivered an email to " . $list['Username'] . " at address " . $list['Email'] . "\r\n";
	  }
	  echo "<script language=\"JavaScript\">alert('Successfully sent " . $Count . " emails.');</script>";
  }
  
}
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="../style.css">
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699">
<table width=780 border=0 cellpadding=0 cellspacing=0 height="383" bgcolor="#FFFFFF">
  <tr> 
    <td rowspan=2> <img src="../images/index_01.gif" width=165 height=35></td>
    <td colspan=2> <img src="../images/index_02.gif" width=615 height=24></td>
  </tr>
  <tr> 
    <td> <img src="../images/index_03.gif" width=1 height=11></td>
    <td rowspan=2> <img src="../images/index_04_logo.jpg" width=614 height=73></td>
  </tr>
  <tr> 
    <td colspan=2 height="39"> <img src="../images/project_logo.gif" width=166 height=62></td>
  </tr>
  <tr> 
    <td colspan=3 background="../images/links.gif"> 
     <?php include("../navigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan=3 height="233"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr> 
          <td height="212"><div align="center" class="BodyHeader">
            <p>&nbsp;</p>
            <p>
              <?php 
			     if (isset($_GET['Action']) && $_GET['Action'] == "SendMail") {
			   ?>
              Mailing Results</p>
            <p><textarea name="textarea" cols="80" rows="20"><?php echo $Result; ?></textarea> 
		      <?php
			  }
			?></p>
            <p><strong>GPL-Hosting Subscriber Mailing List </strong></p>
            <p>&nbsp;</p>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=SendMail" method="post">
            <table width="390" border="0" class="menu">
              <tr>
                <td width="78">From:</td>
                <td colspan="2">&lt;newsletter@gplhosting.org&gt;</td>
              </tr>
              <tr>
                <td>To:</td>
                <td colspan="2">&lt;All Users@gplhosting.org&gt; </td>
              </tr>
              <tr>
                <td>Subject:</td>
                <td colspan="2"><input name="Subject" type="text" id="Subject" size="60"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td>Body:</td>
                <td colspan="2"><textarea name="Body" cols="50" rows="10" id="Body"></textarea></td>
              </tr>
              <tr>
                <td>Signature:                  </td>
                <td colspan="2"><textarea name="Signature" cols="50" id="Signature">- GPL Hosting</textarea></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="175">&nbsp;</td>
                <td width="123"><input type="submit" name="Submit" value="Send"></td>
              </tr>
            </table>
			<p>&nbsp;</p>
			</form>
           </div></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td colspan=3 height="14"> 
      <div align="center"> 
        <table width="100%" border="0" cellspacing="0" cellpadding="0" height="35" align="center">
          <tr> 
            <td background="../images/index_08.gif" height="35"> 
              <?php include("../footer.html"); ?>
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
</BODY>
</HTML>