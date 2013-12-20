<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");
require("includes/class.bind.php");

  if (isset($_POST['Domain']) && $_POST['Domain'] != "") {
    $PGPL->SudoLogin();
    if (!($ZoneInfo = mysql_query("SELECT * FROM DNS_Zones WHERE Zone='" . $_POST['Domain'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	 $Result .= "Could not retrieve zone information from database.";
	} else {
	 $ZoneSQL = mysql_fetch_array($ZoneInfo);
	}
	if ($ZoneSQL['Type'] == "slave") {
	  if (!($DelRecQuery = mysql_query("DELETE FROM DNS_Zones WHERE ZoneID='" . $ZoneSQL['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	     $Result .= "Could not delete records from the database.";
	  }
	  if (!$PGPL_BIND->DeleteZone($_POST['Domain'])) {
	     $Result .= "There was an error removing " . $_POST['Domain'] . ". The operation has been aborted.";
	  }
	  if (!$PGPL_BIND->DeleteZoneFile($_POST['Domain'])) {
	     $Result .= "There was an error removing the zone file for " . $_POST['Domain'] . ". The requested operation has been aborted.";
	  }
	  $PGPL_BIND->Reload();
	  $PGPL->SudoLogout();
	  if (!$Result) { echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php';</script>"; exit(); }
	}	
    if (!$PGPL_BIND->DeleteZone($_POST['Domain'])) {
	   $Result .= "There was an error removing " . $_POST['Domain'] . ". The operation has been aborted.";
	} else {
		if (!($DelQuery = mysql_query("DELETE FROM DNS_Zones WHERE Zone='" . $_POST['Domain'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
		  $Result .= "Could not delete " . $_POST['Domain'] . " from the database.";
		}
		if (!($DelRecQuery = mysql_query("DELETE FROM DNS_Records WHERE ZoneID='" . $ZoneSQL['ZoneID'] . "'"))) {
		  $Result .= "Could not delete resource records from the database for " . $_POST['Domain'] . ".";
		}  
	    if (!$PGPL_BIND->DeleteZoneFile($_POST['Domain'])) {
		  $Result .= "There was an error removing the zone file for " . $_POST['Domain'] . ". The requested operation has been aborted.";
		}
		$PGPL_BIND->Reload();
		$PGPL->SudoLogout();
	    if (!$Result) { echo "<script language=\"JavaScript\">location.href = 'DNS_ViewTree.php';</script>"; }
    }
  }
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">

<script language="javascript">
function DelDomain(Domain) {
var decision = confirm("Are you sure you want to delete "+Domain+"?");
 if (decision == true) {
  document.write("<form name=\"frmDelDomain\" action=\"DNS_DelDomain.php\" method=\"POST\">");
  document.write("<input type=\"hidden\" name=\"Domain\" value="+Domain+">");
  document.write("</form>");
  document.frmDelDomain.submit(); 
 }
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
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td width="18%"><?php include("CP_Navigation.php"); ?></td>
                <td><?php include("CenterOfAttention.php"); ?>                </td>
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
                <td><div align="center" class="highlight">Please select a zone to delete.</div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
				  <?php if (isset($Result)) { echo $Result . "<br>"; } ?>
				 <table border="0" align="center" class="menu">
				<?php			
				      if (!($ZoneQuery = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' ORDER BY Zone"))) {
					    $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
					  } else {					
					       if (mysql_num_rows($ZoneQuery) == 0) {
						    echo "<tr><td colspan=\"3\" width=\"100%\"><b><i>You do not have any DNS zones configured at this time.</i></b></td></tr>";
						   }				             
							  while($ThisZone = mysql_fetch_array($ZoneQuery)) {
							   echo "<tr><td><input type=\"radio\" id=\"this1\" name=\"DelDomain\" onClick=\"JavaScript:DelDomain('" . $ThisZone['Zone'] . "');\" style=\"cursor:hand;\"></td><td><label for=\"" . $ThisZone['Zone'] . "\"><a href=\"JavaScript:DelDomain('" . $ThisZone['Zone'] . "');\">" . $ThisZone['Zone'] . "</a></label></td></tr>\r\n";
							  }
					  }
				?>				
                  </table>
				</td>
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