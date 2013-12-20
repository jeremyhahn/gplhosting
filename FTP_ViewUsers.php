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

<script language="javascript">
function Highlight(el) {
 el.style.background = '#CCFFFF'
}
function Visited(el) {
 el.style.background = '#FFFFFF'
}
function EditPassword(Username,Owner) {
  location.href = 'FTP_EditUser.php?Action=EditPassword&Username='+Username;
}
function DeleteUser(Username,Owner,Website) {
	if (Username == Owner) {
	  var DelUser = confirm("You are about to delete the account holders login! This will delete EVERY website and FTP user that exists.\r\nAre you sure you want to perform this operation?");
	  if (DelUser == true) {
		 location.href = 'FTP_EditUser.php?Action=DeleteUser&Username='+Username;
	  }
	} else {
	   var decision = confirm("Are you sure you want to delete "+Username+" from website "+Website+"?");
		 if (decision == true) {
			location.href = 'FTP_EditUser.php?Action=DeleteUser&Username='+Username;
		 }
	}
}
function ToggleDiv(DivID) {
  if (document.getElementById(DivID).style.display == 'none') {
   document.getElementById(DivID).style.display = '';
  } else {
   document.getElementById(DivID).style.display = 'none';
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
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">
				<?php 			
				     if (!($MASTER_FTP_User_Query = mysql_query("SELECT * FROM FTP_Records WHERE Owner='" . $_SESSION['Username'] . "' AND DaRoot='1' Order By Username"))) {
					   echo "There was an error retreiving the master FTP records from the database.<br><b>MySQL Said:</b><br>" . mysql_error();
					   exit();
					 }
					 $MasterUserCount = mysql_num_rows($MASTER_FTP_User_Query);
					 if ($MasterUserCount) {
				?>         <b><i>Root Level  FTP Account(s)</i></b>
						   <table width="99%" align="center" class="menu">
							  <tr class="highlight" bgcolor="#FFFFCC">
								<td width="30%">Username</td>
								<td colspan="2">Password</td>
								<td width="19%">Action</td>
							  </tr>
                              <?php 
								 while ($MasterUser = mysql_fetch_array($MASTER_FTP_User_Query)) {
								  echo "<tr onmouseover=\"JavaScript:Highlight(this)\" onmouseout=\"JavaScript:Visited(this)\"><td><b>" . $MasterUser['Username'] . "</b></td><td>" . $MasterUser['Password'] . "</td><td></td><td><a href=\"ftp://" . $MasterUser['Username'] .":@" . $_SERVER['SERVER_NAME'] . "\" target=\"_blank\"><img src=\"images/FTP.gif\" alt=\"Log into the root level FTP folder.\" border=\"0\" style=\"cursor:hand;\"></a> &nbsp; &nbsp; <img src=\"images/keys.gif\" alt=\"Edit " . ucfirst($MasterUser['Username']) . "'s password\" border=\"0\" onClick=\"JavaScript:EditPassword('" . $MasterUser['Username'] . "')\" style=\"cursor:hand;\"> &nbsp; &nbsp;&nbsp;<img src=\"images/delete.gif\" alt=\"Delete " . ucfirst($MasterUser['Username']) . "'s user account\" border=\"0\" onClick=\"JavaScript:DeleteUser('" . $MasterUser['Username'] . "','" . $MasterUser['Owner'] . "','/WEB_ROOT')\" style=\"cursor:hand;\"></td></tr>\r\n"; 
							     }					
							  ?>            
				  </table>
							  <br>					                
				<?php } else { if (mysql_num_rows($MASTER_FTP_User_Query) == 0) { $Display = "<div align=\"center\"><b><i>You do not have any FTP users configured at this time.<br>You must create a new website before any FTP users can become active.</i></b></div>"; } }		  
					 if (!($Weblist = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "' ORDER BY ServerName"))) {
					   echo "There was an error retreiving the web site records from the database.<br><b>MySQL Said:</b><br>" . mysql_error();
					   exit();
					 }
					 $WebCount = mysql_num_rows($Weblist);
					 if ($WebCount) {
					      while ($ThisSite = mysql_fetch_array($Weblist)) {
						     $UsrCount = mysql_query("SELECT * FROM FTP_Records WHERE Website='" . $ThisSite['ServerName'] . "' Order By Username");
						     if (mysql_num_rows($UsrCount) > 0) {
				?>
				<br><br>
			
				<table width="99%" border="0" align="center" class="menu">
                  <tr style="background-image:url(images/index_02.gif)">
                    <td colspan="4"><img src="images/toggle.gif" width="17" height="18" onClick="JavaScript:ToggleDiv('div_<?php echo str_replace(".","_",$ThisSite['ServerName']); ?>');" style="cursor:hand;"><font color="#FFFFFF">Valid FTP users for <b><i><?php echo $ThisSite['ServerName']; ?></i></b></font></td>
                  </tr>
                  <tr>
                    <td colspan="4"><div id="div_<?php echo str_replace(".","_",$ThisSite['ServerName']); ?>">
				<?php					  
					if (!($FTPlist = mysql_query("SELECT * FROM FTP_Records WHERE Owner='" . $_SESSION['Username'] . "' AND Website='" . $ThisSite['ServerName'] . "' ORDER BY Username"))) {
					   echo "There was an error retreiving the FTP records from the database.<br><b>MySQL Said:</b><br>" . mysql_error();
					   exit();
					} 	$FTPCount = mysql_num_rows($FTPlist);
			             while ($ThisUser = mysql_fetch_array($FTPlist)) {
						  if ($ThisUser['Username'] != $_SESSION['Username']) {						 
				?>
					 
                        <table class="menu" width="100%">
                          <tr class="highlight" bgcolor="#F9F9F9">
                            <td width="30%">Username</td>
                            <td colspan="2">Password</td>
                            <td width="19%">Action</td>
                          </tr>
                            <?php echo "<tr onmouseover=\"JavaScript:Highlight(this)\" onmouseout=\"JavaScript:Visited(this)\"><td><b>" . $ThisUser['Username'] . "</b></td><td>" . $ThisUser['Password'] . "</td><td></td><td><a href=\"ftp://" . $ThisUser['Username'] .":@" . $ThisUser['Website'] . "\" target=\"_blank\"><img src=\"images/FTP.gif\" alt=\"Log into " . $ThisUser['Website'] ."'s home folder.\" border=\"0\" style=\"cursor:hand;\"></a> &nbsp; &nbsp; <img src=\"images/keys.gif\" alt=\"Edit " . $ThisUser['Username'] . "'s password\" border=\"0\" onClick=\"JavaScript:EditPassword('" . $ThisUser['Username'] . "')\" style=\"cursor:hand;\"> &nbsp; &nbsp;&nbsp;<img src=\"images/delete.gif\" alt=\"Delete " . $ThisUser['Username'] . "'s user account\" border=\"0\" onClick=\"JavaScript:DeleteUser('" . $ThisUser['Username'] . "','" . $MasterUser['Owner'] . "','" . $ThisUser['Website'] . "')\" style=\"cursor:hand;\"></td></tr>\r\n"; ?>
                        </table>						
					
					<?php   
					         
							} else { if ($FTPCount == 1) { echo "<div id=\"div_" . str_replace(".","_",$ThisSite['ServerName']) . "\"><b><i>You do not have any additional users configured at this time.</i></b></div>"; } }
						   } // Ends FTP WHILE
                    ?>
				   </div>
				  </td>
				 </tr>
				</table>
				<?php  }
				     } 
				   } echo $Result . "<br>"; 
				   echo $Display;
				   echo $UserCount;
			    ?>				
				</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="22%">&nbsp;</td>
                <td width="25%">&nbsp;</td>
                <td width="35%">&nbsp;</td>
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