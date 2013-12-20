<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/class.pgpl.php");
require("includes/class.apache2.php");
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<script language="javascript">
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
			    <td><div align="center" class="highlight">Website Summary Details</div></td></tr>
              <tr>
                <td>&nbsp;</td>
                <td>				
					    <?php 
							  if (!($SiteListing = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "' ORDER BY ServerName"))) {
								 $Result = "Could not gather a site listing.<br><b>MySQL Said:</b><br>" . mysql_error();
							  } else {
								 $TotalSites = mysql_num_rows($SiteListing);
								 if (!$TotalSites == 0) {	
						?>
						<table width="100%"  border="0" class="menu">
                          <tr style="background-image:url(images/index_02.gif)">
                           <td><img src="images/toggle.gif" width="17" height="18" onClick="JavaScript:ToggleDiv('Div_Website_List');" style="cursor:hand;"><font color="#FFFFFF"> Website Accounts</font></td>
                          </tr>
                          <tr>
                           <td><div id="Div_Website_List"> 
					<?php
								 $Display = "Total # of Websites: $TotalSites";						 		 
									while ($ThisSite = mysql_fetch_array($SiteListing)) {											    
									    	$Apache = explode(",",$Apache2);				
											$DiskUsage = $PGPL_Apache2->DiskUsage($Apache[0] . $_SESSION['Username'] . "/" . $ThisSite['ServerName']);					                    
											   $KB = number_format(round($DiskUsage / 1024,1),1,".",",");
											   $MB = number_format(round($DiskUsage / 1048576,1),1,".",",");
											   $GB = number_format(round($DiskUsage / 1073741824,1),1,".",",");
											   if (($DiskUsage / 1024) < 1024) { $TotalDiskUsage = number_format($DiskUsage,1,".",","); } 
                               				   if (($DiskUsage / 1024) > 1) { $TotalDiskUsage = $KB; }
										       if (($DiskUsage / 1048576) > 1) { $TotalDiskUsage = $MB; }
										       if (($DiskUsage / 1073741824) > 1) { $TotalDiskUsage = $GB; }
						  ?>					   
					    <table class="menu" width="100%">
                          <tr class="highlight" bgcolor="#F9F9F9">
                            <td width="37%">Website</td>
                            <td width="26%">Disk Usage
                              <select class="menu" onChange="document.getElementById('div_ByteDisplay_<?php echo str_replace(".","_",$ThisSite['ServerName']); ?>').innerHTML = (this.value);">
                                <option selected value="<?php echo number_format($DiskUsage) . ' Bytes'; ?>">Bytes</option>
								<option value="<?php echo $KB . ' KiloBytes'; ?>">KB</option>
                                <option value="<?php echo $MB . ' MegaBytes'; ?>">MB</option>
                                <option value="<?php echo $GB . ' GigaBytes'; ?>">GB</option>
                              </select>
							 </td>
                            <td colspan="2">Disk Quota</td>
							<td colspan="2">Bandwidth Quota </td>
                            </tr>
                          <tr>
							<td><a href="http://<?php echo $ThisSite['ServerName']; ?>" target="_blank"><font color="#000000"><?php echo $ThisSite['ServerName']; ?></font></a></td>
							<td><div id="div_ByteDisplay_<?php echo str_replace(".","_",$ThisSite['ServerName']); ?>"><?php echo $TotalDiskUsage . ' Bytes'; ?></div></td>
							<td width="13%"><?php if (!$ThisSite['DiskQuota']) { echo "Unlimited"; } else { echo number_format($ThisSite['DiskQuota'] / 1048576,1,".",",") . " MB "; } ?></td>
							<td width="4%"><?php if (($ThisSite['DiskQuota'] < $DiskUsage)  && $ThisSite['DiskQuota'] != 0) { ?>
                              <img src="images/RedLight.gif" border="0" alt="ATTENTION: YOU HAVE EXCEEDED YOUR DISK QUOTA!">
                              <?php } elseif (($ThisSite['DiskQuota'] / 2 <= $DiskUsage) && $ThisSite['DiskQuota'] != 0) { 
								?>
                              <img src="images/YellowLight.gif" border="0" alt="CAUTION: Disk usage is at 50% or MORE.">
                              <?php } else {
								?>
                              <img src="images/GreenLight.gif" border="0" alt="INFORMATION: Disk usage is at 50% or less.">
                              <?php } 
								?>
							 </td>
							<td width="13%">
							  <?php if ($ThisSite['BandwidthQuota'] == 0) {		
										   echo "Unlimited";
									 }	else {
							             	 $DispChk = $_SESSION[$ThisSite['ServerName'] . 'BandwidthBytes'] / 1073741824;
											 $BandQuota = $ThisSite['BandwidthQuota'] / 1073741824;
											 
											 if ($BandQuota < 1024) {
											   echo number_format($ThisSite['BandwidthQuota'] / 1048576,1,".",",") . " MB";
											 } else {
											   echo number_format($BandQuota / 1073741824,1,".",",") . " GB";
											 }
									}								  
							   ?>
							  <br> 
							 </td>
						    <td width="4%"><?php if (($ThisSite['BandwidthQuota'] < $_SESSION[$ThisSite['ServerName'] . 'BandwidthBytes'])  && $ThisSite['BandwidthQuota'] != 0) { ?>
                              <img src="images/RedLight.gif" border="0" alt="ATTENTION: YOU HAVE EXCEEDED YOUR BANDWIDTH QUOTA!">
                              <?php } elseif (($ThisSite['BandwidthQuota'] / 2 <= $_SESSION[$ThisSite['ServerName'] . 'BandwidthBytes']) && $ThisSite['BandwidthQuota'] != 0) { 
								?>
                                 <img src="images/YellowLight.gif" border="0" alt="CAUTION: Bandwidth usage is at 50% or MORE.">
                              <?php } else {
								?>
                                 <img src="images/GreenLight.gif" border="0" alt="INFORMATION: Bandwidth usage is at 50% or less.">
                              <?php } ?>
							</td>
                          </tr>
						 </table>
						  <?php 				          
								}
							  }	else { $Display = "<div align=\"center\" style=\"menu\">You do not have any websites configured at this time.</div>"; }
						   } 
						  ?>                          
                      </div><?php echo "<br><br><b><i>$Display</i></b>"; ?>
				  <?php if (!$TotalSites == 0) { ?>
				  </td>
                 </tr>
                </table>
				<?php } ?>
			   </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td></td>				
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>  
			  </tr>
		 </table>
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