<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");


if ($_GET['Action'] == "Process") {
   require("includes/class.apache2.php");
 
   $Website = strtolower($_GET['Website']);
if (!($ZoneInfo = mysql_query("SELECT * FROM DNS_Zones WHERE Zone='" . $Website . "'"))) {
		 $Result .= "Could not retrieve zone information from database.";
	}
	if (mysql_num_rows($ZoneInfo) > 0 && $_GET['RemoveDNS'] != 1 && $_GET['ByPass'] != 1) {
	 ?>
	  <script language="javascript">
		var DelDNS = confirm("A DNS domain name has been detected for this website. Would you like to remove these entries in DNS also?");
		 if (DelDNS == true) { 
		   location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?Action=Process&RemoveDNS=1&Website=<?php echo $Website; ?>';
		 } else {
		   location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?Action=Process&RemoveDNS=0&Website=<?php echo $Website; ?>&ByPass=1';
		 }
	  </script>
	 <?php
	 exit();
	}
    $ZoneSQL = mysql_fetch_array($ZoneInfo);

 if ($_GET['RemoveDNS'] == 1) {
    require("includes/class.bind.php");    

	if (!($DelRecQuery = mysql_query("DELETE FROM DNS_Records WHERE ZoneID='" . $ZoneSQL['ZoneID'] . "'"))) {
	 $Result .= "Could not delete records from the database.";
	}
    if (!($DelQuery = mysql_query("DELETE FROM DNS_Zones WHERE Zone='" . $_GET['Website'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	 $Result .= "Could not delete " . $_GET['Website'] . " from the database.";
	}
	  $PGPL->SudoLogin();
	  $PGPL_BIND->DeleteZone($_GET['Website']);
	  $PGPL_BIND->DeleteZoneFile($_GET['Website']);
	  $PGPL->SudoLogout();
}
// ------------------------------------------------------------------------------------------------------------------------------------> 
  $DetailsQuery = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "' AND ServerName='" . $Website . "'");
   $Details = mysql_fetch_array($DetailsQuery);  
  $DOC_ROOT = $ArrApache[0] . $_SESSION['Username'] . "/" . $Website;  
  $PGPL->SudoLogin();
  
	if (!$PGPL_Apache2->DelVhost($Website,$ArrApache[3])) {
	  $Result .= $DelVhost_ERROR;
	} else {
		if (!$PGPL_Apache2->ApacheGraceful()) {
		  $Result .= "An error occurred while trying to reload the Apache web server after deleting the virtual host container for " . $Website . ".";
		} else {
		  if (!(mysql_query("DELETE FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "' AND ServerName='" . $Website . "'"))) { $Result .= mysql_error(); }
		  echo "<script language=\"JavaScript\">location.href = 'WEB.php';</script>";
		}
	   require("includes/class.vsftpd.php");
	   if (!($FTP = mysql_query("SELECT * FROM FTP_Records WHERE Website='" . $_GET['Website'] . "'"))) {
	      $Result .= "An error occurred while attempting to perform a database query to get registered FTP users for the website " . strtolower($_GET['Website']) . ".";
	   }
	   while ($FTPuser = mysql_fetch_array($FTP)) {	 
	         $PGPL_FTP->Username = $FTPuser['Username'];
			 if (!$PGPL_FTP->DeleteUser(0)) {
			   $Result .= "An error occurred while trying to delete the user " . $PGPL_FTP->Username . ".";
			 } 
			 if (!($DelQuery = mysql_query("DELETE FROM FTP_Records WHERE Username='" . $FTPuser['Username'] . "'"))) {
			   $Result .= "An error occurred while trying to delete the user " . $PGPL_FTP->Username . " from the website " . strtolower($_GET['Website']) . ".";
			 }
			 unlink($WebalizerHome . "/" . strtolower($_GET['Website']) . ".conf");
	   }
	   if (!$PGPL_FTP->RemoveHomeDir($DOC_ROOT)) {
	     $Result .= "Could not remove website home directory.";
	   }
	   if (!$PGPL_FTP->RemoveDirs($ArrApache[1] . $_SESSION['Username'] . "/" . $Website)) {
	     $Result .= "Could not remove website log directory.";
	   }	
	   $PGPL->SudoLogout();
	}	   
}
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">

<script language="javascript">
function DelSite(Site) {
  var decision = confirm('Are you sure you want to delete the website at '+Site+'?');
   if (decision) {
     location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?Action=Process&Website='+Site;
   }
}
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
                <td></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td></td>
                <td><div align="center"><font color="#FF0000"><b><?php echo $Result; ?></b></font></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
				<div align="center">
                  <p class="highlight">Please select the website you want to delete.</p>
                  <p class="style2">WARNING: This will delete not only the website from the web server config files, but ALL of your files also! <br>
			   </p>
                </div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
				<table width="50%" border="1" align="center" bordercolor="#F9F9F9" class="menu">
                  <?php 
					  if (!($SiteListing = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "' ORDER BY ServerName"))) {
						 $Result = "Could not gather a site listing.<br><b>MySQL Said:</b><br>" . mysql_error();
					  } else {
						 $TotalSites = mysql_num_rows($SiteListing);
						 if (!$TotalSites == 0) {	
						 $Display = "Total Websites: <?php echo $TotalSites; ?>";						 		 
							while ($ThisSite = mysql_fetch_array($SiteListing)) {
					   
				  ?>
				  <tr>				
                    <td width="6%"><input name="<?php echo str_replace(".","_",$ThisSite['ServerName']); ?>" type="radio" value="<?php echo str_replace(".","_",$ThisSite['ServerName']); ?>" id="<?php echo str_replace(".","_",$ThisSite['ServerName']); ?>" onClick="JavaScript:DelSite('<?php echo $ThisSite['ServerName']; ?>')">
                    </td>
                    <td width="94%"><label for="<?php echo str_replace(".","_",$ThisSite['ServerName']); ?>" style="cursor:hand;"><?php echo $ThisSite['ServerName']; ?></label></td>
                  </tr>
                <?php 				          
				            }
						  }	else { $Display = "You do not have any websites configured at this time.";	 }
				       } 
				?><br><br><b><i><?php echo $Display; ?></i></b>
				</table>			
				</td>
              </tr>
              <tr>
                <td></td>
                <td>                
              <tr>
                <td>&nbsp;</td>
                <td>
				<p></p>
		 </table>
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