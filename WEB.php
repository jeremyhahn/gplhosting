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
			    <td><div align="center" class="highlight">Choose an action below to manage your websites.</div></td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td></td></tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;				
			    </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><table width="80%"  border="1" align="center" bordercolor="#F9F9F9" class="menu">
                  <tr>
                    <td width="50%"><div align="center">
                        <p><a href="WEB_ViewSites.php"><img src="images/View2.gif" alt="View Configured Websites" width="32" height="32" border="0"></a></p>
                        <p><a href="WEB_ViewSites.php">View Configured Websites </a></p>
                    </div></td>
                    <td width="50%"><div align="center">
                        <p><a href="WEB_NewSite.php"><img src="images/globe.gif" alt="Configure A New Website" width="32" height="32" border="0"></a></p>
                        <p><a href="WEB_NewSite.php">Configure New Website</a></p>
                    </div></td>
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
                    <td><div align="center">
                        <p><a href="WEB_DelSite.php"><img src="images/BigRecycle.gif" alt="Delete A Website" width="32" height="32" border="0"></a></p>
                        <p><a href="WEB_DelSite.php">Delete A Website </a></p>
                    </div></td>
                    <td><div align="center">
                        <p><a href="WEB_Statistics.php"><img src="images/Statistics.gif" alt="View Website Statistics" width="32" height="32" border="0"></a></p>
                        <p><a href="WEB_Statistics.php">Website Statistics </a></p>
                    </div></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td></td>				
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>  
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