<?php
session_start();
require("includes/DB_ConnectionString.php");

if ($_GET['Action'] == "ClearLog") {
  mysql_query("DELETE FROM DynDNS_Stats WHERE Username='" . $_SESSION['Username'] . "'");
  echo "<script language=\"JavaScript\">alert('Statistic logs have been cleared.')</script>";
}
if (!($_GET['Username'] == "")) {
 $_SESSION['Username'] = $_GET['Username'];
}
$limit=100; 
$query = "SELECT * FROM DynDNS_Stats WHERE Username='" . $_SESSION['Username'] . "'";
$execQuery = mysql_query($query);
$numrows = mysql_num_rows($execQuery);
if (empty($_GET['ThisPage'])) {
$ThisPage=0;
} else {
$ThisPage = $_GET['ThisPage'];
}  
$query .= " limit " . $ThisPage . "00,$limit";
$result = mysql_query($query) or die("Couldn't execute query<br><br>Query was: $query<br>Number of rows found were $numrows");
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
                <td width="18%"><?php include("CP_Navigation.php"); ?></td>
                <td colspan="3"><p align="center" class="BodyHeader">&nbsp;</p>
                  <p align="center" class="BodyHeader">Dynamic DNS Activity Log </p>
                  <p>&nbsp;</p>
				  <table width="400"  border="0" align="center" class="menu">
                  <tr>
                    <td width="100%"><p>This activity log shows detailed statistics of all of your dynamic DNS traffic for your archiving, historical, or troubleshooting needs. </p>
                      <p>&nbsp;</p></td>
                    </tr>
                </table>                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td width="70%"></td>
                <td width="10%"><div align="right"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Action=ClearLog">Clear Log</a> </div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">
				  <?php
				 $count = $ThisPage . "00" + 1;
				 
				    echo "<table width=\"100%\" border=\"1\" class=\"menu\" style=\"filter:progid:DXImageTransform.Microsoft.Gradient(endColorstr='#C7C7DA', startColorstr='#FFFFFF', gradientType='0');border-style:groove;\">";
					echo "<tr style=\"color:#666699;font-weight:bold;\"><td>Last Update</td><td>From IP Address</td><td>System Response</td><td>Domain</td>";
				     while ($row = mysql_fetch_array($result)) {
					   echo "<tr><td>" . $row['Date'] . "</td><td>" . $row['FromIP'] . "</td><td>" . $row['Response'] . "</td><td>" . $row['FQDN'] . "</td></tr>";
					 }
					echo "</table>";
				
				
				
				 if ($numrows == 0) {
	              echo "<h4>Results</h4>";
	              echo "<p>Your Dynamic DNS statistic log currently contains <b>0</b> records.</p>";
	             } else {
				          echo "<h4>Results</h4>";
				                 // Links to other results
								  if ($ThisPage>=1) { 
									 // bypass PREV link if ThisPage is 1
									  print "&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?ThisPage=" . ($ThisPage-1) . "\">&lt;&lt; Prev 100</a> &nbsp; ";
								  }
                                      $pagesLeft=intval($numrows/$limit);


										  if ($numrows%$limit) {
										  // There are a few more results left over
										  $pagesLeft++;
										  }
										  
										  for ($pageLinks=1; $pageLinks < $pagesLeft+1; $pageLinks++) {
										    echo "Page <a href=\"" . $_SERVER['PHP_SELF'] . "?ThisPage=" . ($pageLinks-1) . "\">" . $pageLinks . "</a> &nbsp; ";
										  } 

												// check to see if last page
												  if (!((($ThisPage . "00"+$limit)/$limit)==$pagesLeft) && $pagesLeft!=1) {												  
												   // not last page so give NEXT link
												   $newPage=$ThisPage+1;
												   echo "&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?ThisPage=$newPage\">Next 100 &gt;&gt;</a>";
												  }
						$a = $ThisPage . "00" + ($limit);
						  if ($a > $numrows) { $a = $numrows ; }
						$b = $ThisPage . "00" + 1 ;						
						  echo "<p>Showing records <i><b>$b</b></i> to <i><b>$a</b></i> of <b>$numrows</b></p>";				
				    }
				?>
			   </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="2%">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
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