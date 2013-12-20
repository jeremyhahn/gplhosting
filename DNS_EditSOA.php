<?php
session_start();
require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");
require("includes/class.bind.php");

if ($_GET['Action'] == "UpdateSOA") {
	if (substr($_POST['RP'],-1) == ".") {
	  $RP = substr_replace($_POST['RP'],"",strlen($RP)-1);
	} else {
	  $RP = $_POST['RP'];
	}
    mysql_query("UPDATE DNS_Zones Set RP='" . str_replace("@",".",$RP) . ".' WHERE ZoneID='" . $_POST['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'");
	mysql_query("UPDATE DNS_Zones Set Serial='" . $_POST['Serial']+1 . "' WHERE ZoneID='" . $_POST['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'");
    mysql_query("UPDATE DNS_Zones Set Refresh='" . $_POST['Refresh'] . "' WHERE ZoneID='" . $_POST['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'");
    mysql_query("UPDATE DNS_Zones Set Retry='" . $_POST['Retry'] . "' WHERE ZoneID='" . $_POST['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'");
    mysql_query("UPDATE DNS_Zones Set Expire='" . $_POST['Expire'] . "' WHERE ZoneID='" . $_POST['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'");
    mysql_query("UPDATE DNS_Zones Set TTL='" . $_POST['TTL'] . "' WHERE ZoneID='" . $_POST['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'");
	$PGPL->SudoLogin();
	if (!$PGPL_BIND->RebuildZone($_POST['ZoneID'])) {
	   $Result .= "An error occurred while trying to rebuild the requested zone.";
	}
	$PGPL->SudoLogout();
	if (!$Result) {
	   echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php';</script>";
	}
}
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">

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
                <td colspan="3"><div align="center" class="style2">WARNING: You should not edit these values unless you know what you are doing! </div></td>
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
                <td colspan="3">
				<?php			
				      if (!($ZoneQuery = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $_GET['ZoneID'] . "'"))) {
					    $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
					  } else {					
					       if (mysql_num_rows($ZoneQuery) == 0) {
						    echo "<b><i>The SOA record for the requested zone could not be found.</i></b>";
						   }				             
							  $ThisZone = mysql_fetch_array($ZoneQuery);
					 }
				?>	
				<form name="UpdateSOA" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=UpdateSOA" method="post">
				  <table width="402" border="1" align="center" bordercolor="#F9F9F9" class="menu">
                    <tr>
                      <td width="123"><strong>Type:</strong></td>
                      <td colspan="2"><input name="Type" readonly type="text" id="Type" size="20" value="<?php echo $ThisZone['Type']; ?>"></td>
                    </tr>
                    <tr>
                      <td><strong>Primary Server: </strong></td>
                      <td colspan="2"><input name="PS" type="text" id="PS" size="25" value="<?php echo $ThisZone['PS']; ?>" readonly style="background:background-color:#CCCCCC;"></td>
                    </tr>
                    <tr>
                      <td><strong>Responsible Person:</strong></td>
                      <td colspan="2"><input name="RP" type="text" id="RP" size="25" value="<?php echo $ThisZone['RP']; ?>">
                        </td>
                    </tr>
                    <tr>
                      <td><strong>Serial:</strong></td>
                      <td width="60"><input name="Serial" type="text" id="Serial" size="10" value="<?php echo $ThisZone['Serial']; ?>" readonly></td>
                      <td width="197"><strong></strong></td>
                    </tr>
                    <tr>
                      <td><strong>Refresh:</strong></td>
                      <td colspan="2"><input name="Refresh" type="text" id="Refresh" size="10" value="<?php echo $ThisZone['Refresh']; ?>">
      Seconds</td>
                    </tr>
                    <tr>
                      <td><strong>Retry:</strong></td>
                      <td colspan="2"><input name="Retry" type="text" id="Retry" size="10" value="<?php echo $ThisZone['Retry']; ?>">
      Seconds</td>
                    </tr>
                    <tr>
                      <td><p><strong>Expire:</strong></p></td>
                      <td colspan="2"><input name="Expire" type="text" id="Expire" size="10" value="<?php echo $ThisZone['Expire']; ?>">
      Seconds</td>
                    </tr>
                    <tr>
                      <td><strong>Time To Live: </strong></td>
                      <td colspan="2"><input name="TTL" type="text" id="TTL" size="10" value="<?php echo $ThisZone['TTL']; ?>">
      Seconds</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td><input type="hidden" name="ZoneID" value="<?php echo $ThisZone['ZoneID']; ?>"></td>
                      <td colspan="2"><input type="submit" name="Submit" value="Update SOA"></td>
                    </tr>
                  </table>
				</form>
			   </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="22%">&nbsp;</td>
                <td width="25%">&nbsp;</td>
                <td width="35%">&nbsp;</td>
              </tr>
            </table>
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