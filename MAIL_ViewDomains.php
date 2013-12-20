<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/GlobalConfigs.php");


 if (isset($_GET['OnLoad']) && $_GET['OnLoad'] != "") {
   $PostedArray = explode(",",$_GET['OnLoad']);   
   
   $ThisQuery = mysql_query("SELECT RecType FROM MAIL_Domains WHERE RecID='" . $PostedArray[0] . "'");
   $Row = mysql_fetch_array($ThisQuery);

       $OnLoadStatement = "JavaScript:ToggleDiv('" . $PostedArray[1] . "','IMG_" . $PostedArray[1] . "');";
       $OnLoadStatement .= "JavaScript:ToggleDiv('" . $PostedArray[1] . "_" . $Row['RecType'] . "_Records','IMG_" . $Row['RecType'] . "_" . $PostedArray[1] . "')";
 }





if (isset($_GET['Action']) && $_GET['Action'] == "DeleteMailDomain") {
    require("includes/XMailCTRL.php");
	
		$ctrl->login();		
		switch ($_GET['Type']) {		
		  case "POP3":		
		      if (substr($ctrl->domaindel($_GET['Domain']),0,1) == "1") {				
			        if (!($DeleteQuery = mysql_query("DELETE FROM MAIL_Domains WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Type='" . $_GET['Type'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	                 echo "Could not delete domain from the database.<br><b>MySQL Said:</b><br>" . mysql_error();
					 return false;
	                }
					if (!($DelRecQuery = mysql_query("DELETE FROM MAIL_Records WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
			         echo "Could not delete domain from the database.<br><b>MySQL Said:</b><br>" . mysql_error();
			         return false;
			        }
			        echo "<script language=\"JavaScript\">alert('" . $_GET['Domain'] . " was successfully deleted from the mail server.');</script>";
			   } else {
				echo "<script language=\"JavaScript\">alert('There was an error deleting " . $_GET['Domain'] . " from the mail server. Operation aborted.');</script>";
			   }
		  break;
		  
		  case "MX_Backup" || "Port25":		
			   if (substr($ctrl->custdomset($_GET['Domain'],"."),0,1) == "+") {
				  if (!($DeleteQuery = mysql_query("DELETE FROM MAIL_Domains WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Type='" . $_GET['Type'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
	                 echo "Could not delete mail domain from the database.<br><b>MySQL Said:</b><br>" . mysql_error();
					 return false;
	              }
				  if (!($DelRecQuery = mysql_query("DELETE FROM MAIL_Records WHERE ZoneID='" . $_GET['ZoneID'] . "' AND Username='" . $_SESSION['Username'] . "'"))) {
			         echo "Could not delete domain from the database.<br><b>MySQL Said:</b><br>" . mysql_error();
			         return false;
			      }
			      echo "<script language=\"JavaScript\">alert('" . $_GET['Domain'] . " was successfully deleted from the mail server.');</script>";
		       } else {
				  echo "<script language=\"JavaScript\">alert('There was an error deleting " . $_GET['Domain'] . " from the mail server. Operation aborted.');</script>";
			   }
		  break;
		}		
		$ctrl->logout();	
  }


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
function EditDomain(ZoneID) {
 location.href = 'MAIL_EditDomain.php?Action=EditDomain&ZoneID='+ZoneID;
}
function EditMailboxes(ZoneID) {
 location.href = 'MAIL_EditMailbox.php?Action=EditMailbox&ZoneID='+ZoneID;
}
function DeleteDomain(Domain,ZoneID,Type) {
var decision = confirm("Are you sure you want to delete the domain "+Domain+" and all mailboxes associated with it?");
 if (decision == true) {
    location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?Action=DeleteMailDomain&Domain='+Domain+'&ZoneID='+ZoneID+'&Type='+Type;
 }
}
function NewMailbox(ZoneID) {
 location.href = 'MAIL_AddMailbox.php?Action=CreateMailbox&ZoneID='+ZoneID;
}
function ToggleDiv(DivID) {
  if (document.getElementById(DivID).style.display == 'none') {
   document.getElementById(DivID).style.display = '';
  } else {
   document.getElementById(DivID).style.display = 'none';
  }
}
</script>
<style type="text/css">
<!--
.style2 {color: #FFFFFF}
-->
</style>
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699" onLoad="<?php echo $OnLoadStatement; ?>">
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
                <td><div align="center" class="highlight">Choose an option from the 'Action' menu to the right of each domain to manage your mail domains. </div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><div align="center"></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
				  
				  <table width="100%"  border="0" class="menu">
                  <tr>
                    <td background="images/index_02.gif"><img src="images/toggle.gif" width="17" height="18" onClick="JavaScript:ToggleDiv('divPOP3');" style="cursor:hand;"><span class="style2"> POP3 Domains </span></td>
                    </tr>				 
				  <?php 
				   $Quota = explode("\r\n",$MemberPlans);
					foreach ($Quota as $Value) {
					  $Data = explode(",",$Value);
						if ($Data[0] == $_SESSION['SiteRole']) {
						   $MailboxQuota = $Data[5];
						}
					}
					if ($MailboxQuota == 0) { $MailboxQuota = "Unlimited"; }
				    if (!($DomainQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE Username='" . $_SESSION['Username'] . "' AND Type='POP3' ORDER BY MailDomain"))) {
					    $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
					} else {					     
				 ?>
				  <tr>
				    <td><div id="divPOP3">
					<table class="menu" width="100%">
					 <tr class="highlight" bgcolor="#F9F9F9">
					  <td width="32%">Domain</td>
					  <td width="21%">Active Mailboxes</td>
					  <td width="29%">Quota</td>
					  <td width="18%">Action</td>
					  <?php
					   if (mysql_num_rows($DomainQuery) == 0) {
						   echo "<tr><td colspan=\"3\"><b><i>You do not have any POP3 mail domains configured at this time.</i></b></td></tr>";
						 } else {
							 while ($row = mysql_fetch_array($DomainQuery)) {						 
							   if (!($MailboxQuery = mysql_query("SELECT * FROM MAIL_Records WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $row['ZoneID'] . "' ORDER BY Mailbox"))) {
								 echo "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
							   } 
							    echo "<tr onmouseover=\"JavaScript:Highlight(this)\" onmouseout=\"JavaScript:Visited(this)\"><td><b>" . $row['MailDomain'] . "</b></td><td><b>" . mysql_num_rows($MailboxQuery) . "</b></td>\r\n<td><b>" . $row['Quota'] / 1024 . " MB - $MailboxQuota Mailboxes</b></td><td width=\"100\"><img src=\"images/mailbox.gif\" alt=\"Add a mailbox to " . $row['MailDomain'] . "\" border=\"0\" onClick=\"JavaScript:NewMailbox('" . $row['ZoneID'] . "')\" style=\"cursor:hand;\">\r\n &nbsp; &nbsp;&nbsp;<img src=\"images/edit.gif\" alt=\"Edit mailboxes for " . $row['MailDomain'] . "\" border=\"0\" onClick=\"JavaScript:EditMailboxes('" . $row['ZoneID'] . "')\" style=\"cursor:hand;\">\r\n &nbsp; &nbsp;&nbsp;<img src=\"images/delete.gif\" alt=\"Delete " . $row['MailDomain'] . "\" border=\"0\" onClick=\"JavaScript:DeleteDomain('" . $row['MailDomain'] . "','" . $row['ZoneID'] . "','" . $row['Type'] . "')\" style=\"cursor:hand;\"></td></tr>\r\n";							  
							 }
						 }
					  }
				    ?>					
					</table>	
					</div>
					</td>
                  </tr>               
				    <tr><td>&nbsp;</td></tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                    <td background="images/index_02.gif"><img src="images/toggle.gif" width="17" height="18" onClick="JavaScript:ToggleDiv('divPort25');" style="cursor:hand;"> <span class="style2">Port 25 Deflection Domains</span> </td>
                  </tr>
                  <tr>
                    <td>
			 
				
				  <tr>
				    <td><div id="divPort25">
					<table class="menu" width="100%">
					 <tr class="highlight" bgcolor="#F9F9F9">
					  <td width="32%">Domain</td><td width="21%">Redirect To</td><td width="29%">Port</td><td width="18%">Action</td>
					<?php
					 if (!($DomainQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE Username='" . $_SESSION['Username'] . "' AND Type='Port25' ORDER BY MailDomain"))) {
					    $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
					  } else { 
					       if (mysql_num_rows($DomainQuery) == 0) {
						    echo "<tr><td colspan=\"3\"><b><i>You do not have any Port 25 deflection domains configured at this time.</i></b></td></tr>";
						    } else {				    
								 while ($row = mysql_fetch_array($DomainQuery)) {	
													   
								   if (!($Port25Query = mysql_query("SELECT * FROM MAIL_Records WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $row['ZoneID'] . "' ORDER BY Mailbox"))) {
									 echo "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
								   } else {
									 $Recs = mysql_fetch_array($Port25Query);
									 $ReDirArray = explode(":",$Recs['RedirectTo']);
									 $Domain = $ReDirArray[0];
									 $Port = $ReDirArray[1];							     				        
									 echo "<tr onmouseover=\"JavaScript:Highlight(this)\" onmouseout=\"JavaScript:Visited(this)\"><td><b>" . $row['MailDomain'] . "</b></td><td><b>" . $Domain . "</b></td>\r\n<td><b>" . $Port . "</b></td><td width=\"100\"><img src=\"images/edit.gif\" alt=\"Edit " . $row['MailDomain'] . "\" border=\"0\" onClick=\"JavaScript:EditDomain('" . $row['ZoneID'] . "')\" style=\"cursor:hand;\">\r\n &nbsp; &nbsp;&nbsp;<img src=\"images/delete.gif\" alt=\"Delete " . $row['MailDomain'] . "\" border=\"0\" onClick=\"JavaScript:DeleteDomain('" . $row['MailDomain'] . "','" . $row['ZoneID'] . "','" . $row['Type'] . "')\" style=\"cursor:hand;\"></td></tr>\r\n";							  
								  }						 
								 }
						  }
					  }
				    ?>					
					</table>	
					</div>
					</td>
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
                    <td background="images/index_02.gif"><img src="images/toggle.gif" width="17" height="18" onClick="JavaScript:ToggleDiv('div_MX');" style="cursor:hand;"> <span class="style2">MX-Backup Domains</span> </td>
                  </tr>
                  <tr>
                    <td><div id="div_MX">
					<table class="menu" width="100%">
					 <tr class="highlight" bgcolor="#F9F9F9">
					  <td width="32%">Redundant Domain</td><td width="50%">Your Server</td><td width="18%">Action</td>
					<?php
					 if (!($DomainQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE Username='" . $_SESSION['Username'] . "' AND Type='MX_Backup' ORDER BY MailDomain"))) {
					    $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
					  } else { 
				         if (mysql_num_rows($DomainQuery) == 0) {
						   echo "<tr><td colspan=\"3\"><b><i>You do not have any redundant mail domains configured at this time.</i></b></td></tr>";
						 } else {
							 while ($MXrow = mysql_fetch_array($DomainQuery)) {							   					   
							   if (!($Port25Query = mysql_query("SELECT * FROM MAIL_Records WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $MXrow['ZoneID'] . "' ORDER BY Mailbox"))) {
								 echo "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
							   } else {
								 $MX_Rec = mysql_fetch_array($Port25Query);
								 echo "<tr onmouseover=\"JavaScript:Highlight(this)\" onmouseout=\"JavaScript:Visited(this)\"><td><b>" . $MXrow['MailDomain'] . "</b></td><td><b>" . $MX_Rec['RedirectTo'] . "</b></td><td width=\"100\"><img src=\"images/edit.gif\" alt=\"Edit " . $MXrow['MailDomain'] . "\" border=\"0\" onClick=\"JavaScript:EditDomain('" . $MXrow['ZoneID'] . "')\" style=\"cursor:hand;\">\r\n &nbsp; &nbsp;&nbsp;<img src=\"images/delete.gif\" alt=\"Delete " . $MXrow['MailDomain'] . "\" border=\"0\" onClick=\"JavaScript:DeleteDomain('" . $MXrow['MailDomain'] . "','" . $MXrow['ZoneID'] . "','" . $MXrow['Type'] . "')\" style=\"cursor:hand;\"></td></tr>\r\n";							  
							  }						 
							 }
						 }
					  }
				    ?>					
					</table>
					</div>
					</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
							</td>
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