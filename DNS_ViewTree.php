<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");

 if (isset($_GET['OnLoad']) && $_GET['OnLoad'] != "") {
   $PostedArray = explode(",",$_GET['OnLoad']);   
   
   $ThisQuery = mysql_query("SELECT RecType FROM DNS_Records WHERE RecID='" . $PostedArray[0] . "'");
   $Row = mysql_fetch_array($ThisQuery);

       $OnLoadStatement = "JavaScript:ToggleDiv('" . $PostedArray[1] . "','IMG_" . $PostedArray[1] . "');";
       $OnLoadStatement .= "JavaScript:ToggleDiv('" . $PostedArray[1] . "_" . $Row['RecType'] . "_Records','IMG_" . $Row['RecType'] . "_" . $PostedArray[1] . "')";
 }
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">

<script language="javascript">
function ToggleContainer(ID) {
  if (document.getElementById(ID).src.indexOf("minus") != -1) {
   document.getElementById(ID).src = 'images/plus.gif';
  } else {
   document.getElementById(ID).src = 'images/minus.gif'; 
  }  
}
function ToggleDiv(DivID,IMG_ID) {
   if (document.getElementById(DivID).style.display == '') {
    document.getElementById(DivID).style.display = 'none';
	ToggleContainer(IMG_ID);
   } else {
   document.getElementById(DivID).style.display = '';
   ToggleContainer(IMG_ID);
   }
}
function EditRecord(RecID,RecType,Record,ZoneID) {
 var decision = confirm("Do you want to make a change to "+Record+"?");
   if (decision == true) {
    location.href = 'DNS_EditRecord.php?RecID='+RecID+'&RecType='+RecType+'&ZoneID='+ZoneID;
   }
}
</script>
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699" onLoad="<?php echo $OnLoadStatement; ?>">
<table width="780" border="0" cellpadding="0" cellspacing="0" height="383" bgcolor="#FFFFFF">
<?php include("header.html"); ?>
  <tr> 
    <td colspan=3 background="images/links.gif"> 
     <?php include("navigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan="3" height="233"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr>
		<td height="212"><table class="menu" width="100%" border="0">
			  <tr>
			    <td width="18%" rowspan="9"><?php include("CP_Navigation.php"); ?></td><td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
                <td><?php include("CenterOfAttention.php"); ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><div align="center" class="highlight">Click on a node to expand/collapse its branch. To modify a record, simply click on its link. </div></td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;
                  <table width="100%"  border="0">
                    <tr>
                      <td>&nbsp;</td>
                      <td><img src="images/tux_lcd.gif" width="32" height="32"></td>
                    </tr>
                    <tr>
                      <td width="12%">&nbsp;</td>
                      <td width="88%"><img src="images/hr_I.gif"> </td>
                    </tr>
                  </table>
                  <table width="69%"  border="0" align="center" class="menu">
                    <tr>
                      <td><?php
				       if (!($ZoneQuery = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' AND Type='master' ORDER BY Zone"))) {
					    $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
					  } else {
					          if (mysql_num_rows($ZoneQuery) == 0) {
						        echo "<div align=\"center\"><b><i>You do not have any primary DNS zones configured at this time.</i></b></div>";
						      }           
								 while($ThisZone = mysql_fetch_array($ZoneQuery)) {								     
								   echo "<img id=\"IMG_" . $ThisZone['ZoneID'] . "\" border=\"0\" src=\"images/plus.gif\" onClick=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "','IMG_" . $ThisZone['ZoneID'] . "');\" style=\"cursor:hand;\"> &nbsp;<a href=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "','IMG_" . $ThisZone['ZoneID'] . "');\" style=\"cursor:hand;\">" . $ThisZone['Zone'] . "</a><br><br>";
									 echo "\r\n\t\t<div id=\"" . $ThisZone['ZoneID'] . "\" style=\"display:none;\">";										
											  echo "\r\n<img src=\"images/hr.gif\">&nbsp; &nbsp; <img border=\"0\" src=\"images/SOA.gif\" alt=\"View/Edit the SOA record for " . $ThisZone['Zone'] . "\"> <a href=\"DNS_EditSOA.php?ZoneID=" . $ThisZone['ZoneID'] . "\">SOA Record</a><br><br>";
								 
											 if (!($A_RecordQuery = mysql_query("SELECT * FROM DNS_Records WHERE ZoneID='" . $ThisZone['ZoneID'] . "' AND RecType='A' ORDER BY Hostname"))) {
											  $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
											 } else {
											             if (!(mysql_num_rows($A_RecordQuery) == 0)) { echo "<img src=\"images/hr.gif\">&nbsp; &nbsp; <img id=\"IMG_A_" . $ThisZone['ZoneID'] . "\" src=\"images/plus.gif\" border=\"0\" onClick=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "_A_Records','IMG_A_" . $ThisZone['ZoneID'] . "');\" style=\"cursor:hand;\"> <img src=\"images/folder.gif\" border=\"0\">&nbsp; <a href=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "_A_Records','IMG_A_" . $ThisZone['ZoneID'] . "');\" style=\"cursor:hand;\"><font color=\"#0000FF\">A-Records</font></a><br>\r\n<div id=\"" . $ThisZone['ZoneID'] . "_A_Records\" style=\"display:none;\">"; }
											  	            while ($A_Record = mysql_fetch_array($A_RecordQuery)) {
														     echo "\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"JavaScript:EditRecord('" . $A_Record['RecID'] . "','A','" . $A_Record['Hostname'] . "." . $ThisZone['Zone'] . "','" . $ThisZone['ZoneID'] . "');\">" . $A_Record['Hostname'] . "." . $ThisZone['Zone'] . " = " . $A_Record['RecData'] . "<a/><br>"; 
														    } 
														 if (!(mysql_num_rows($A_RecordQuery) == 0)) { echo "</div>\r\n<br>"; }
									                }
													
													
											if (!($MX_RecordQuery = mysql_query("SELECT * FROM DNS_Records WHERE ZoneID='" . $ThisZone['ZoneID'] . "' AND RecType='MX' ORDER BY MX_Pref"))) {
											  $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
											 } else {
											             if (!(mysql_num_rows($MX_RecordQuery) == 0)) { echo "<img src=\"images/hr.gif\">&nbsp; &nbsp; <img id=\"IMG_MX_" . $ThisZone['ZoneID'] . "\" src=\"images/plus.gif\" border=\"0\" onClick=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "_MX_Records','IMG_MX_" . $ThisZone['ZoneID'] . "');\" style=\"cursor:hand;\"> <img src=\"images/folder.gif\" border=\"0\">&nbsp; <a href=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "_MX_Records','IMG_MX_" . $ThisZone['ZoneID'] . "');\" style=\"cursor:hand;\"><font color=\"#0000FF\">MX-Records</a></font><br>\r\n<div id=\"" . $ThisZone['ZoneID'] . "_MX_Records\" style=\"display:none;\">"; }
											  	            while ($MX_Record = mysql_fetch_array($MX_RecordQuery)) {
														     echo "\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"JavaScript:EditRecord('" . $MX_Record['RecID'] . "','MX','" . $MX_Record['Hostname'] . "." . $ThisZone['Zone'] . "','" . $ThisZone['ZoneID'] . "');\">" . $MX_Record['Hostname'] . "." . $ThisZone['Zone'] . " = " . $MX_Record['RecData'] . "</a> &nbsp; <font color=\"#FF0000\">Preference:</font> " . $MX_Record['MX_Pref'] . "<br>"; 
														    } 
													     if (!(mysql_num_rows($MX_RecordQuery) == 0)) { echo "</div>\r\n<br>"; }
									                }		
													
													
										    if (!($CNAME_RecordQuery = mysql_query("SELECT * FROM DNS_Records WHERE ZoneID='" . $ThisZone['ZoneID'] . "' AND RecType='CNAME' ORDER BY Hostname"))) {
											  $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
											 } else {
											             if (!(mysql_num_rows($CNAME_RecordQuery) == 0)) { echo "<img src=\"images/hr.gif\">&nbsp; &nbsp; <img id=\"IMG_CNAME_" . $ThisZone['ZoneID'] . "\" src=\"images/plus.gif\" border=\"0\" onClick=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "_CNAME_Records','IMG_CNAME_" . $ThisZone['ZoneID'] . "');\" style=\"cursor:hand;\"> <img src=\"images/folder.gif\" border=\"0\">&nbsp; <a href=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "_CNAME_Records','IMG_CNAME_" . $ThisZone['ZoneID'] . "');\";\" style=\"cursor:hand;\"><font color=\"#0000FF\">CNAME-Records</font></a><br>\r\n<div id=\"" . $ThisZone['ZoneID'] . "_CNAME_Records\" style=\"display:none;\">"; }
											  	            while ($CNAME_Record = mysql_fetch_array($CNAME_RecordQuery)) {
														     echo "\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"JavaScript:EditRecord('" . $CNAME_Record['RecID'] . "','CNAME','" . $CNAME_Record['Alias'] . "','" . $ThisZone['ZoneID'] . "');\">" . $CNAME_Record['Alias'] . " = " . $CNAME_Record['Hostname'] . "</a><br>"; 
														    } 
													     if (!(mysql_num_rows($CNAME_RecordQuery) == 0)) { echo "</div>\r\n<br>"; }
									                }
													
													
											if (!($NS_RecordQuery = mysql_query("SELECT * FROM DNS_Records WHERE ZoneID='" . $ThisZone['ZoneID'] . "' AND RecType='NS' ORDER BY Hostname"))) {
											  $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
											 } else {
											             if (!(mysql_num_rows($NS_RecordQuery) == 0)) { echo "<img src=\"images/hr_I.gif\">&nbsp; &nbsp; <img id=\"IMG_NS_" . $ThisZone['ZoneID'] . "\" src=\"images/plus.gif\" border=\"0\" onClick=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "_NS_Records','IMG_NS_" . $ThisZone['ZoneID'] . "');\" style=\"cursor:hand;\"> <img src=\"images/folder.gif\" border=\"0\">&nbsp; <a href=\"JavaScript:ToggleDiv('" . $ThisZone['ZoneID'] . "_NS_Records','IMG_NS_" . $ThisZone['ZoneID'] . "');\" style=\"cursor:hand;\"><font color=\"#0000FF\">NS-Records</font></a><br>\r\n<div id=\"" . $ThisZone['ZoneID'] . "_NS_Records\" style=\"display:none;\">"; }
											  	            while ($NS_Record = mysql_fetch_array($NS_RecordQuery)) {
														     echo "\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"JavaScript:EditRecord('" . $NS_Record['RecID'] . "','NS','" . $NS_Record['RecData'] . "','" . $ThisZone['ZoneID'] . "');\">" . $NS_Record['RecData'] . "</a><br>"; 
														    } 
													     if (!(mysql_num_rows($NS_RecordQuery) == 0)) { echo "</div>\r\n<br>"; }
									                }													
									 echo "\r\n\t\t</div>";								   
								 }
								
					        } 
							echo $Result;
							
					  if (!($SlaveZoneQuery = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' AND Type='slave' ORDER BY Zone"))) {
					    $Result = "Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error();
					  } else {
					          if (mysql_num_rows($SlaveZoneQuery) != 0) {							   
						        echo "<br><br><div><b><i>Slave Zones</i></b></div>";
								echo "<table border=\"0\" width=\"100%\" class=\"menu\">";
								while ($SlaveSQL = mysql_fetch_array($SlaveZoneQuery)) {
								  echo "<tr><td><a href=\"JavaScript:EditRecord('0','slave','" . $SlaveSQL['Zone'] . "','" . $SlaveSQL['ZoneID'] . "');\">" . $SlaveSQL['Zone'] . "</td></tr>";
						        }
								echo "</table>";
							  }
					 }
				?></td>
                    </tr>
                  </table>
			    </td>
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