<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
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
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><div align="center" class="highlight">Choose the service you would like to manage from the control panel.</div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><div align="center"></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><table width="90%"  border="0" align="center" class="menu">
                  <tr>
                    <td colspan="3"><div align="center" class="BodyHeader">
                      <div align="left"></div>
                    </div></td>
                  </tr>
                  <tr>
                    <td colspan="3">You are currently logged in as a <?php echo $_SESSION['SiteRole']; ?>, under the identity <?php echo $_SESSION['Username']; ?>.</td>
                    </tr>
                  <tr>
                    <td colspan="3"><?php
					                   if (!($_SESSION['LastLogin'] == "")) {
					                 ?>
									 Your last login was on <?php echo $_SESSION['LastLogin']; ?> 
									 from IP address <?php echo $_SESSION['LastIP']; ?>. 
									<?php
									 } else {
									 ?>
									  This is your first visit, Welcome!									 
									<?php } ?>
					 </td>
                    </tr>
                  <tr>
                    <td colspan="3">You have been a member since <?php if ($_SESSION['MemberSince'] == "") { echo date("m-d-Y"); } else { echo $_SESSION['MemberSince']; } ?>.</td>
                    </tr>
                  <?php
				    if (!($DonationAmount = mysql_query("SELECT * FROM PaymentSummary WHERE Username='" . $_SESSION['Username'] . "'"))) {
					  $Result = "Could not query the database for donation total.<br><b>MySQL Said:</b><br>" . mysql_error();
					} else {
					     while ($Payments = mysql_fetch_array($DonationAmount)) {
					      $ThisAmount = $Payments['PaidAmount'];
						  $TotalPaid = ($TotalPaid + $ThisAmount);
					    }						
					}
				  ?>
				  <tr>
                    <td colspan="3">You have made a total of $<?php echo money_format("%i", $TotalPaid); ?> in donations since you have signed up.</td>
                  </tr>
                  <tr>
				 
                    <td colspan="3">&nbsp;</td>
                    </tr>
                  <tr>
                    <td width="65%">&nbsp;</td>
                    <td width="10%">&nbsp;</td>
                    <td width="25%">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
			<p align="center" class="menu">&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>		 </td>
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