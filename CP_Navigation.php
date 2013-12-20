<div style="position:absolute; left:13px; top:140px;">
<table width="132" border="1" align="left" class="menu" style="background-color:#F9F9F9;">
  <tr>
	<td bordercolor="#F9F9F9"><img alt="Copyright &copy; 2004 Jeremy Hahn" src="images/ControlPanel.gif" width="16" height="16"></td>
	<td class="menu" colspan="2" bordercolor="#F9F9F9">CONTROL PANEL</td>
  </tr>
  <tr>
	<td bordercolor="#F9F9F9"></td>
	<td bordercolor="#F9F9F9"></td>
	<td bordercolor="#F9F9F9"></td>
  </tr>
  <?php if ($_SESSION['SiteRole'] == "Site Admin") { ?>
  <tr>
    <td bordercolor="#F9F9F9"></td>
    <td bordercolor="#F9F9F9"><a href="Admin/Admin.php"><img alt="Control Panel Administration" border="0" src="images/Administration.gif" width="16" height="16" style="cursor:hand;"></a></td>
    <td class="menu" bordercolor="#F9F9F9"><a href="Admin/Admin.php">Administration</a></td>
  </tr>
  <?php } ?>
  <tr>
	<td bordercolor="#F9F9F9"></td>
	<td bordercolor="#F9F9F9"><a href="AcctMgmt.php"><img alt="Personal Profile Management" border="0" src="images/profile.gif" width="16" height="20" style="cursor:hand;"></a></td>
	<td class="menu" bordercolor="#F9F9F9"><a href="AcctMgmt.php">Profile</a></td>
  </tr>
  <tr>
	<td width="16" bordercolor="#F9F9F9"></td>
	<td width="23" bordercolor="#F9F9F9"><a href="DNS.php"><img alt="Domain Name Service (DNS) Management" border="0" src="images/dns.gif" width="16" height="16" style="cursor:hand;"></a></td>
	<td class="menu" width="71" bordercolor="#F9F9F9"><a href="DNS.php">DNS Hosting </a></td>
  </tr>
  <tr>
	<td bordercolor="#F9F9F9"></td>
	<td bordercolor="#F9F9F9"><a href="MAIL.php"><img alt="Mail Management" border="0" src="images/mail.gif" width="16" height="16" style="cursor:hand;"></a></td>
	<td class="menu" bordercolor="#F9F9F9"><a href="MAIL.php">Mail Hosting </a></td>
  </tr>
  <tr>
    <td bordercolor="#F9F9F9"></td>
    <td bordercolor="#F9F9F9"><a href="WEB.php"><img src="images/webhost.gif" alt="Web Hosting Management" width="16" height="16" border="0" style="cursor:hand;"></a></td>
    <td class="menu" bordercolor="#F9F9F9"><a href="WEB.php">Web Hosting</a> </td>
  </tr>
  <tr>
    <td bordercolor="#F9F9F9"></td>
    <td bordercolor="#F9F9F9"><a href="FTP.php"><img src="images/FTP.gif" alt="File Transfer Protocol (FTP) Management" width="16" height="16" border="0" style="cursor:hand;"></a></td>
    <td class="menu" bordercolor="#F9F9F9"><a href="FTP.php">FTP Hosting</a> </td>
  </tr>
  <tr>
    <td bordercolor="#F9F9F9"></td>
    <td bordercolor="#F9F9F9"><a href="HELP.php"><img alt="Help Me!" border="0" src="images/Help.gif" width="16" height="16" style="cursor:hand;"></a></td>
    <td class="menu" bordercolor="#F9F9F9"><a href="HELP.php">Help!</a></td>
  </tr>
  <tr>
	<td bordercolor="#F9F9F9"></td>
	<td bordercolor="#F9F9F9"><a href="https://www.paypal.com/xclick/business=paypal%40pc-technics.com&item_name=Project+GPL+Hosting+Donations&item_number=GPL+Hosting+Donations&no_shipping=1&return=http%3A//www.gplhosting.org/paypal_return.php%3FConfirm%3D1%26Purchase%3DDonation&cancel_return=http%3A//www.gplhosting.org&no_note=1&tax=0&currency_code=USD" target="_blank"><img alt="Please Support Project GPL Hosting!" src="images/Donations.gif" width="16" height="16" border="0"></a></td>
	<td class="menu" bordercolor="#F9F9F9"><a href="https://www.paypal.com/xclick/business=paypal%40pc-technics.com&item_name=Project+GPL+Hosting+Donations&item_number=GPL+Hosting+Donations&no_shipping=1&return=http%3A//www.gplhosting.org/paypal_return.php%3FConfirm%3D1%26Purchase%3DDonation&cancel_return=http%3A//www.gplhosting.org&no_note=1&tax=0&currency_code=USD" target="_blank">Donations</a></td>
  </tr>
  <tr>
    <td bordercolor="#F9F9F9"></td>
	<td bordercolor="#F9F9F9"></td>
	<td class="menu" bordercolor="#F9F9F9"><a href="logout.php"><strong><i>Logout</i></strong></a></td>
  </tr>
</table>
</div>