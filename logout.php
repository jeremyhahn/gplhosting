<?php
session_start();
session_unset();
session_destroy();
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<script language="JavaScript">
setTimeout('location.href = "manage.php"',2000);
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
          <td height="212"><div align="center" class="BodyHeader">
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>You have been sucessfully logged out of the system. </p>
            <p class="menu style2">You will be redirected to the login page in 2 seconds.... </p>
            <p class="menu style2">&nbsp;</p>
            <p class="menu style2">&nbsp;</p>
            <p class="menu style2">&nbsp;</p>
            <p class="menu style2">&nbsp;</p>
            <p class="menu style2">&nbsp;</p>
            <p class="menu style2">&nbsp;</p>
            <p class="menu style2">&nbsp;</p>
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