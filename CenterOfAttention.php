<p align="center" class="BodyHeader">Account Management Center</p>
<p></p>
<table width="400"  border="0" align="center" class="menu">
  <tr class="menu">
	<td>Last Login: </td>
	<td><?php echo $_SESSION['LastLogin']; ?></td>
	<td>From IP:</td>
	<td><?php echo $_SESSION['LastIP']; ?></td>
  </tr>
  <tr>
	<td width="22%">Project Role:</td>
	<td width="27%"><?php echo $_SESSION['SiteRole']; ?>&nbsp;</td>
	<td width="17%">User Since: </td>
	<td width="34%"><?php echo $_SESSION['MemberSince']; ?>&nbsp;</td>
  </tr>
  <tr>
	<td colspan="4">Logged in as: <?php echo $_SESSION['Username']; ?></td>
	</tr>
</table>                