<?php
/*
if ($_SESSION['Username'] != $_POST['Username']) {
  require("includes/AdminSecurity.php");
  $Username = "test";      //$_POST['Username'];
} else {
  $Username = $_SESSION['Username'];
}
*/
$Username = "jeremy";

require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");
require("includes/class.bind.php");
require("includes/class.xmail.php");
require("includes/class.apache2.php");
require("includes/class.vsftpd.php");

if (!($UserQuery = mysql_query("SELECT * FROM Clients WHERE Username='" . $Username . "'"))) {
   echo "Could not execute select statement on clients table.<br><b>MySQL Said:</b><br>" . mysql_error();
   exit();
}
if (mysql_num_rows($UserQuery) ==0) { echo "User not found in database"; return false; }
if (!($DNS_Query = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $Username . "'"))) {
   echo "Could not execute select query on DNS_Zones table.<br><b>MySQL Said:</b><br>" . mysql_error();
   exit();
}
if (!($MailQuery = mysql_query("SELECT * FROM MAIL_Domains WHERE Username='" . $Username . "'"))) {
   echo "Could not execute select query on MAIL_Domains table.<br><b>MySQL Said:</b><br>" . mysql_error();
   exit();
}
if (!($WebQuery = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $Username . "'"))) {
   echo "Could not execute select query on HTTP_Records table.<br><b>MySQL Said:</b><br>" . mysql_error();
   exit();
}
if (!($FTP_Query = mysql_query("SELECT * FROM FTP_Records WHERE Username='" . $Username . "'"))) {
   echo "Could not execute select query on FTP_Records table.<br><b>MySQL Said:</b><br>" . mysql_error();
   exit();
}
$ThisUser = mysql_fetch_array($UserQuery);
 $DNS_Count = mysql_num_rows($DNS_Query);
 $MailCount = mysql_num_rows($MailQuery);
 $WebCount = mysql_num_rows($WebQuery);
 $FTP_Count = mysql_num_rows($FTP_Query);

  /* REMOVE DNS ACCOUNTS
   while ($ZoneSQL = mysql_fetch_array($DNS_Query)) {
		if ($ZoneSQL['Type'] == "slave") {
			  if (!($DelRecQuery = mysql_query("DELETE FROM DNS_Zones WHERE ZoneID='" . $ZoneSQL['ZoneID'] . "'"))) {
				 $Result = "An error occurred while attempting to remove the DNS domain " . $ZoneSQL['Zone'] . " from the database.<br><b>MySQL Said:</b><br>" . mysql_error() . "<br>";
			  }
			  $PGPL_BIND->DeleteZone($ZoneSQL['Zone']);
			  $PGPL_BIND->DeleteZoneFile($ZoneSQL['Zone']);
		} else { 
			 if (!$RemoveError = $PGPL_BIND->DeleteZone($ZoneSQL['Zone'])) {
			   $Result .= "An error occurred while attempting to remove the DNS domain " . $ZoneSQL['Zone'] . ".<br>";
			 } else {
				if (!($DelQuery = mysql_query("DELETE FROM DNS_Zones WHERE Zone='" . $ZoneSQL['Zone'] . "'"))) {
				 $Result .= "An error occurred while attempting to remove the DNS domain " . $ZoneSQL['Zone'] . " from the DNS_Zones database table.<br><b>MySQL Said:</b><br>" . mysql_error() . "<br>";
				}
				if (!($DelRecQuery = mysql_query("DELETE FROM DNS_Records WHERE ZoneID='" . $ZoneSQL['ZoneID'] . "'"))) {
				 $Result .= "An error occurred while attempting to remove the associated resource records for the DNS domain " . $ZoneSQL['Zone'] . ".<br><b>MySQL Said:</b><br>" . mysql_error() . "<br>";
				}  
				$PGPL_BIND->DeleteZoneFile($ZoneSQL['Zone']);
			}
		}
   }
   */
   // Remove Mail Accounts  
   $LoggedIn = 0; 
   $ctrl->login();
   $LoggedIn = 1;
   while ($ThisMailDomain = mysql_fetch_array($MailQuery)) {   
		switch ($ThisMailDomain['Type']) {		
		  case "POP3":		
		      if (substr($ctrl->domaindel($ThisMailDomain['MailDomain']),0,1) == "1") {				
			     if (!($DeleteQuery = mysql_query("DELETE FROM MAIL_Domains WHERE MailDomain='" . $ThisMailDomain['MailDomain'] . "'"))) {
	                $Result .= "There was an error deleting " . $ThisMailDomain['MailDomain'] . " from the mail database.<br><b>MySQL Said:</b><br>" . mysql_error() . "<br>";
	             }
				 if (!($DelRecQuery = mysql_query("DELETE FROM MAIL_Records WHERE ZoneID='" . $ThisMailDomain['ZoneID'] . "'"))) {
			        $Result .= "There was an error deleting " . $ThisMailDomain['MailDomain'] . " from the mail database.<br><b>MySQL Said:</b><br>" . mysql_error() . "<br>";
			     }
			   } else {
				$Result .= "There was an error deleting " . $ThisMailDomain['MailDomain'] . " from the mail server.<br>";
			   }
		  break;		  
		  
		  case "MX_Backup" || "Port25":		
		  if (!$LoggedIn) {
		     $ctrl->login();
		     $LoggedIn = 1;
		  }
		       if (substr($ctrl->custdomset($ThisMailDomain['MailDomain'],""),0,1) == "+") {
				  if (!($DeleteQuery = mysql_query("DELETE FROM MAIL_Domains WHERE ZoneID='" . $ThisMailDomain['ZoneID'] . "'"))) {
	                 $Result .= "There was an error deleting " . $ThisMailDomain['MailDomain'] . " from the mail database.<br><b>MySQL Said:</b><br>" . mysql_error() . "<br>";
	              }
				  if (!($DelRecQuery = mysql_query("DELETE FROM MAIL_Records WHERE ZoneID='" . $ThisMailDomain['ZoneID'] . "'"))) {
			         $Result .= "There was an error deleting " . $ThisMailDomain['MailDomain'] . " from the mail database.<br><b>MySQL Said:</b><br>" . mysql_error() . "<br>";
			      }
			   } else {
			     $Result .= "There was an error deleting " . $ThisMailDomain['MailDomain'] . " from the mail server.<br>";
			   }
		//  $ctrl->logout();  
		 // $LoggedIn = 0;
	 	  break;
	    }
   }  
   $ctrl->logout();
   $LoggedIn = 0;
   /* Remove Web/FTP Accounts
   if (mysql_num_rows($WebQuery) == 0) {
      if (!($FTPchk = mysql_query("SELECT * FROM FTP_Records WHERE Username='" . $Username . "'"))) {
	    echo "Could not query the FTP users table in the database to verify that this user exists!<br><b>MySQL Said:</b><br>" . mysql_error();
	  } else {
	    if (mysql_num_rows($FTPchk) > 0) {
		       mysql_query("DELETE FROM FTP_Records WHERE Username='" . $Username . "'");
			   if ($GrpError = DelFTPuser($Username,0,2)) {
			      $Result .= $GrpError;
			   }
			   SudoFTP_Logout();
			 }		
		}
   }
   $ArrApache = explode(",",$Apache2);		
   $SudoPasswd = base64_decode($SudoPassword);	    
   SudoFTP_Login($SudoPasswd);
   while ($ThisWeb = mysql_fetch_array($WebQuery)) {
			  if (!($ThisWeb['ServerName'] == "Root")) { 		   	    
					if ($DelVhostError = DelVhost($ThisWeb['ServerName'],$ArrApache[2],$ArrApache[3])) {
					   $Result .= $DelVhostError;
					 }
					 if (!($MySQL_ERROR = mysql_query("DELETE FROM HTTP_Records WHERE ServerName='" . $ThisWeb['ServerName'] . "'"))) {
					   $Result .= "Could not execute delete query.<br><b>MySQL Said:</b><br>" . mysql_error();
					 }			   
					 $FTPusers = mysql_query("SELECT * FROM FTP_Records WHERE Website='" . $ThisWeb['ServerName'] . "'");
						   while ($ArrFTP = mysql_fetch_array($FTPusers)) {
							  mysql_query("DELETE FROM FTP_Records WHERE Username='" . $ArrFTP['Username'] . "'");				  
							  if ($FTP_Error = DelFTPuser($ArrFTP['Username'],0,0)) {
								$Result .= $FTP_Error;
							  }	 
						   }		 
			   }
				   if (!($DeleteRootLevelUsers = mysql_query("SELECT * FROM FTP_Records WHERE Owner='" . $Username . "' AND Username!='" . $Username . "'"))) {
					 $Result .= "Could not query the database for root level users.<br><b>MySQL Said:</b><br>" . mysql_error();
				   } else {
						 while ($Master = mysql_fetch_array($DeleteRootLevelUsers)) {
							 mysql_query("DELETE FROM FTP_Records WHERE Username='" . $Master['Username'] . "'");
							 if ($DelFTP_Error = DelFTPuser($Master['Username'],0,0)) {
								$Result .= $DelFTP_Error;
							 }
						 }
				   }
		 unlink($WebalizerHome . "/" .  $ThisWeb['ServerName'] . ".conf");			    		  
		}
		         
				if ($UserDelError = DelFTPuser($Username,$ArrApache[0] . $Username,1)) {
				 $Result .= $UserDelError;
			    } 
				if ($GroupDelError = DelFTPuser($Username,0,2)) {
				 $Result .= $GroupDelError;
			    }
				mysql_query("DELETE FROM FTP_Records WHERE Owner='" . $Username . "' AND Username='" . $Username . "'");
				if (is_dir($ArrApache[0] . $Username)) {
				   if (!RemoveHomeDir($ArrApache[0] . $Username)) {
					 $Result .= "Could not delete the directory located at " . $ArrApache[0] . $Username;
				   }
			    }
				if (is_dir($ArrApache[1] . $Username)) {
				   if (!RemoveDirs($ArrApache[1] . $Username)) {
					 $Result .= "Could not delete the directory located at " . $ArrApache[1] . $Username;
				   }
			    }
				  SudoFTP_Logout();	
   
      mysql_query("DELETE FROM Comments WHERE Username='" . $Username . "' AND VisibleToClient!='0'");
      PaymentLog
      PaymentSummary   
   */
   
   // mysql_query("DELETE FROM Clients WHERE Username='" . $Username . "'");
   if (!$Result) {
     echo "<script language=\"JavaScript\">alert('" . ucfirst($Username) . " was successfully deleted.');</script>";
   }
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
          <td height="212"><table width="90%"  border="0" align="center">
            <tr>
              <td width="28%">&nbsp;</td>
              <td class="TableHeader" width="72%"><?php echo $Result; ?></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table>
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