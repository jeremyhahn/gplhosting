<?php
session_start();
require("includes/GetOuttaHere.php");
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
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
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td width="18%"><?php include("CP_Navigation.php"); ?></td>
                <td><?php include("CenterOfAttention.php"); ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><div align="center"><span class="highlight">Choose an action below to manage your File Transfer Protocol (FTP) users. </span></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><table width="80%"  border="1" align="center" bordercolor="#F9F9F9" class="menu">
                  <tr>
                    <td width="50%"><div align="center">
                        <p><a href="FTP_ViewUsers.php"><img src="images/View2.gif" alt="View Configured FTP Users" width="32" height="32" border="0"></a></p>
                        <p><a href="FTP_ViewUsers.php">View Configured Users </a></p>
                    </div></td>
                    <td width="50%"><div align="center">
                        <p><a href="FTP_NewUser.php"><img src="images/Help/FTP.gif" alt="Configure A New FTP User" width="32" height="32" border="0"></a></p>
                        <p><a href="FTP_NewUser.php">Add New FTP User </a></p>
                    </div></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td height="15">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
			<p>&nbsp;</p>
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