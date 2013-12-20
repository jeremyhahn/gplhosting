<?php
session_start();
require("../includes/AdminSecurity.php");
require("../includes/DB_ConnectionString.php");

if (isset($_GET['Action']) && $_GET['Action'] == "SwitchUser") {
  $_SESSION['Username'] = $_GET['User'];
  echo "<script language=\"JavaScript\">alert('You are now logged in as " . $_SESSION['Username'] . ", with administrative privileges.');location.href = '../loggedin.php';</script>";
}
if (empty($_GET['ThisPage'])) {
$ThisPage=0;
} else {
$ThisPage = $_GET['ThisPage'];
}			
function FixDate($Date) {
  $ArrDate = explode("-",$Date);
  return $ArrDate[1] . "-" . $ArrDate[2] . "-" . $ArrDate[0];
}
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="../style.css">
<script language="javascript">
function LoginAsUser(Username) {
 location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?Action=SwitchUser&User='+Username;
}
</script>
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699">
<table width="780" border="0" cellpadding="0" cellspacing="0" height="383" bgcolor="#FFFFFF">
<?php include("../SubHeader.html"); ?>
  <tr> 
    <td colspan=3 background="../images/links.gif"> 
     <?php include("../SubNavigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan="3" height="233"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr>
		<td height="212"><table class="menu" width="100%" border="0">
			  <tr>
			    <td width="18%" rowspan="12"><?php include("../SubCP_Navigation.php"); ?></td><td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
                <td><div align="center" class="BodyHeader">Member Administration</div></td>
			  </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="highlight">&nbsp;</td>
              </tr>
              <tr>
                <td><span class="highlight">From here, you can log into another members account for troubleshooting, send emails, modify membership plans, and verify the members home server. Hover over each link to view more details.</span></td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>
				<form name="Search" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Search" method="post">
				<table width="51%"  border="0" align="center" class="CustomTable">
                  <tr>
                    <td width="23%">GREP:</td>
                    <td width="77%"><input class="menu" name="Criteria" type="text" id="Criteria" value="(Searh Criteria)" onClick="JavaScript:this.value = '';"> 
                    by 
                      <select name="Field" class="menu" id="Field">
                        <option value="Username" selected>Username</option>
                        <option value="Email">Email</option>
                        <option value="Plan">Plan</option>
                        <option value="HomeServer">Home Server</option>
                        <option value="Created">Created</option>
                        <option value="LastLogin">Last Login</option>
                      </select></td>
                  </tr>
                  <tr>
                    <td><label style="cursor:hand;" for="FindSimilar">Find Simliar</label></td>
                    <td><input class="menu" name="FindSimilar" type="checkbox" id="FindSimilar" value="1" checked></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input class="menu" type="submit" name="Submit" value="Search"></td>
                  </tr>
                </table>
				</form>
                </td>
              </tr>
              <tr>
                <td><p>
                  <?php
				 $limit=100; 						
				        if (isset($_GET['Action']) && $_GET['Action'] == "Search") {
						    if (isset($_POST['FindSimilar']) && $_POST['FindSimilar'] == 1) {
						      $query = "SELECT * FROM Clients WHERE " . $_POST['Field'] . " LIKE '%" . $_POST['Criteria'] . "%' ORDER BY " . $_POST['Field'];
						    } else {
							  $query = "SELECT * FROM Clients WHERE " . $_POST['Field'] . "='" . $_POST['Criteria'] . "' ORDER BY " . $_POST['Field'];
							}
						
						} else {
						  $query = "SELECT * FROM Clients ORDER BY Username";
						}
						
												
						 $execQuery = mysql_query($query);
						 $numrows = mysql_num_rows($execQuery);														  			
						// get results
						  $query .= " limit " . $ThisPage . "00,$limit";
						  $result = mysql_query($query) or die("Couldn't execute query<br><br>Query was: $query<br>Number of rows found were $numrows");
						  
				  $count = $ThisPage . "00" + 1;				 
				  if ($numrows == 0) {
	                echo "<p><b>No records found.</b></p>";
	              } else {
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
				</p>
				<table width="98%"  border="0" align="center" class="CustomTable">
                  <tr class="highlight" bgcolor="#F9F9F9">
                    <td>Username</td>
                    <td>Email</td>
                    <td>Plan</td>
                    <td>Home Server </td>
                    <td>Created</td>
                    <td>Last Login </td>
                    </tr>                  
				  <?php 
  					   
					     while ($ThisClient = mysql_fetch_array($result)) {
				  ?>
				  
				  <tr class="TableRow">
				    <td width="14%"><a class="SetColor" title="Login As '<?php echo $ThisClient['Username']; ?>'" href="JavaScript:LoginAsUser('<?php echo $ThisClient['Username']; ?>')"><?php echo $ThisClient['Username']; ?></a></td>
                    <td width="29%"><a class="SetColor" title="Send An Email To <?php echo ucfirst($ThisClient['Username']); ?>" href="mailto:<?php echo $ThisClient['Email']; ?>"><?php echo $ThisClient['Email']; ?></a></td>
                    <td width="15%"><a class="SetColor" title="Edit Memebrship Plan" href="AdminMemberships.php?Action=Update&Plan=<?php echo $ThisClient['Plan']; ?>"><?php echo $ThisClient['Plan']; ?></a></td>
                    <td width="18%"><a class="SetColor" title="Visit Home Server" href="http://<?php echo $ThisClient['HomeServer']; ?>" target="_blank"><?php echo $ThisClient['HomeServer']; ?></a></td>
                    <td width="12%"><?php echo FixDate($ThisClient['Created']); ?></td>
                    <td width="12%"><?php echo FixDate($ThisClient['LastLogin']); ?></td>
                   </tr>
				   <tr class="TableRow2"></tr>
				  <?php 
					   }
				  ?>				                  
                </table>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
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