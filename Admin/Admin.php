<?php
session_start();
require("../includes/AdminSecurity.php");
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="../style.css">
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699">
<table width="780" border="0" cellpadding="0" cellspacing="0" height="383" bgcolor="#FFFFFF">
<?php include("../SubHeader.html"); ?>
  <tr> 
    <td colspan=3 background="../images/links.gif"> 
     <?php include("../navigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan="3" height="233"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr>
		<td height="212"><table class="menu" width="100%" border="0">
			  <tr>
			    <td width="18%" rowspan="11"><?php include("../SubCP_Navigation.php"); ?></td><td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
                <td><div align="center" class="BodyHeader">Administration/Configuration Management </div></td>
			  </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td><?php switch ($_GET['Desktop']) {
				            case "1":
					 ?>
					 <table width="85%"  border="0" align="center" class="menu">
					  <tr>
						<td width="52%"><div align="center">
							<p><a href="AdminModAccounts.php"><img src="../images/Users.gif" width="31" height="32" border="0"></a></p>
							<p><a href="AdminModAccounts.php" class="SetColor">View/Modify Accounts</a></p>
						</div></td>
						<td width="48%"><div align="center">
						  <p><a href="AdminMemberships.php"><img src="../images/MembershipPlans.gif" alt="Configure Membership Plans" width="48" height="48" border="0"></a></p>
						  <p class="SetColor"><a href="AdminMemberships.php" class="SetColor">Configure Membership Plans </a></p>
						</div></td>
					  </tr>
					</table>
					 <?php break;
					        case "2":
					 ?>
					 <table width="85%"  border="0" align="center" class="menu">
					  <tr>
						<td><div align="center">
							<p><a href="AdminSite.php"><img src="../images/SiteConfig.gif" alt="Configure Site Specific Settings" width="31" height="32" border="0"></a></p>
							<p class="SetColor"><a href="AdminSite.php" class="SetColor">Configure Site Settings </a></p>
						</div></td>
						<td><div align="center">
						  <p><a href="AdminBIND.php"><img src="../images/BIND.gif" alt="Configure BIND DNS Server Settings" width="122" height="30" border="0"></a></p>
						  <p><a href="AdminBIND.php" class="SetColor">Configure BIND DNS Settings </a></p>
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
						<td width="52%"><div align="center">
						  <p><a href="AdminApache.php"><img src="../images/Apache.gif" alt="Configure Apache Web Server Settings" width="125" height="40" border="0"></a></p>
						  <p><a href="AdminApache.php" class="SetColor">Configure Apache Settings</a></p>
						</div></td>
						<td width="48%"><div align="center">
						  <p><a href="AdminXmail.php"><img src="../images/Xmail.gif" alt="Configure Xmail Server Settings" width="125" height="50" border="0"></a></p>
						  <p><a href="AdminXmail.php" class="SetColor">Configure Xmail Server Settings</a> </p>
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
						  <p><a href="AdminWebalizer.php"><img src="../images/webalizer.gif" alt="Configure Webalizer Statistics Settings" width="88" height="31" border="0"></a></p>
						  <p><a href="AdminWebalizer.php" class="SetColor">Configure Webalizer Settings</a> </p>
						</div></td>
						<td><div align="center">
						  <p>&nbsp;</p>
						  </div></td>
					  </tr>
					</table>
					 <?php break;
					        default:
					 ?>
					 <table width="85%"  border="0" align="center" class="menu">
					  <tr>
						<td width="52%"><div align="center">
							<p><a href="Admin.php?Desktop=2"><img src="../images/Configuration.gif" alt="Configure Control Panel Settings" width="28" height="32" border="0"></a></p>
							<p><a href="Admin.php?Desktop=2" class="SetColor">Control Panel Configurations</a></p>
						</div></td>
						<td width="48%"><div align="center">
						  <p><a href="Admin.php?Desktop=1"><img src="../images/Users.gif" alt="Configure Membership Plans" width="31" height="32" border="0"></a></p>
						  <p><a href="Admin.php?Desktop=1" class="SetColor">Membership Administration</a> </p>
						</div></td>
					  </tr>
					</table>
					 <?php break;
					      }
				     ?>
						          
				</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
			<p>&nbsp;</p>
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