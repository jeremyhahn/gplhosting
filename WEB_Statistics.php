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
                <td><div align="center"><span class="highlight">Brief Website Statistics</span></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
				<?php 				
					 if (!($Vhost_Query = mysql_query("SELECT * FROM HTTP_Records WHERE Username='" . $_SESSION['Username'] . "' ORDER BY ServerName"))) {
					   echo "There was an error retreiving the virtual host information.<br><b>MySQL Said:</b><br>" . mysql_error();
					 } else {
					  $VhostCount = mysql_num_rows($Vhost_Query);			
					  if ($VhostCount == 0) { $Display = "You do not have any websites configured at this time."; }
					     
		                 while ($ThisVhost = mysql_fetch_array($Vhost_Query)) {						      
							  if ($ArrApache[8] == 1) {
							     $PGPL->SudoLogin();
								 $ThisPath = getcwd();							 
							     shell_exec("sudo " . $ThisPath . "/Utilities/CommonLogSpider " . str_replace("/access_log","",$ThisVhost['LogFile']));
					             if ($handle = opendir(str_replace("/access_log","",$ThisVhost['LogFile']))) {
									  while (false !== ($file = readdir($handle))) { 
											if ($file != "." && $file != "..") { 
												if (substr($file,0,10) == "access_log") {
												   shell_exec("sudo webalizer -c " . $WebalizerHome . "/" . $ThisVhost['ServerName'] . ".conf " . str_replace("/access_log","",$ThisVhost['LogFile']) . "/" . $file);
												}
											} 
										}
										closedir($handle); 
								 }							  						  
							     $PGPL->SudoLogout();
						      }						 
							  // Show Statistical Information For Each Configured Virtual Host 
							  $strSpiderLog = str_replace("access_log","spider_log",$ThisVhost['LogFile']);
							  if (is_readable($strSpiderLog)) {
								  if ($Contents) { unset($Contents); }
								  if ($OverallBytes) { unset($OverallBytes); }
								  if ($OverallHits) { unset($OverallHits); }
								  if ($OverallUniques) { unset($OverallUniques); }
									$SpiderLog = fopen($strSpiderLog,"a+");					      
									  while ($Data = fgets($SpiderLog)) {
										 $Contents .= $Data . "\r\n";															
									  }
						 }						        	
				?>
				  <table width="100%"  border="0" class="menu">
				  <tr style="background-image:url(images/index_02.gif)">
                    <td><img src="images/toggle.gif" width="17" height="18" onClick="JavaScript:ToggleDiv('div_<?php echo str_replace(".","_",$ThisVhost['ServerName']); ?>');" style="cursor:hand;"><font color="#FFFFFF">Statistics for <i><?php echo $ThisVhost['ServerName']; ?></i></font></td>
                  </tr>
                  <tr>
                    <td>
					<div id="div_<?php echo str_replace(".","_",$ThisVhost['ServerName']); ?>">
					<?php
					unset($ArrData);
					unset($OverallBytes);
					unset($OverallHits);
					unset($OverallUniques);												
					   $ArrData = explode("\r\n",$Contents);
					   for ($i=0; $i < count($ArrData); $i++) {					   
						 list( $Month, $Year, $TotalBytes, $TotalHits, $UniqueVisitors) = explode(",",$ArrData[$i]);
						 if ($UniqueHits < 0) { $UniqueHits = 0; }					
						 if ($TotalBytes > 0) {
						   $KB = number_format(round($TotalBytes / 1024,1),1,".",",");
						   $MB = number_format(round($TotalBytes / 1048576,1),1,".",",");
						   $GB = number_format(round($TotalBytes / 1073741824,1),1,".",",");
						   $TB = number_format(round($TotalBytes / 1099511627776,1),1,".",",");
						   $PB = number_format(round($TotalBytes / 1125899906842624,1),1,".",",");
						   if (($TotalBytes / 1024) < 1) { $BandwidthConsumption = number_format($TotalBytes,1,".",","); $DisplaySize = " Bytes"; } 
						   if (($TotalBytes / 1024) > 1) { $BandwidthConsumption = $KB; $DisplaySize = " KiloBytes"; }
						   if (($TotalBytes / 1048576) > 1) { $BandwidthConsumption = $MB; $DisplaySize = " MegaBytes"; }
						   if (($TotalBytes / 1073741824) > 1) { $BandwidthConsumption = $GB; $DisplaySize = " GigaBytes"; }
						   if (($TotalBytes / 1099511627776) > 1) { $BandwidthConsumption = $TB; $DisplaySize = " TeraBytes"; }
						   if (($TotalBytes / 1125899906842624) > 1) { $BandwidthConsumption = $PB; $DisplaySize = " PetaBytes"; }
						     
				    ?>					 
					   <table class="menu" width="100%">
                          <tr class="highlight" bgcolor="#F9F9F9">
                            <td width="18%">Date</td>
                            <td width="43%">Bandwidth Consumption by 
                              <select class="menu" onChange="document.getElementById('<?php echo $Month . "_" . $Year; ?>_ByteDisplay_<?php echo str_replace(".","_",$ThisVhost['ServerName']); ?>').innerHTML = (this.value);">
							   <option value="<?php echo number_format($TotalBytes,1,".",",") . ' Bytes'; ?>">Bytes</option>
							   <option value="<?php echo $KB . ' KiloBytes'; ?>">KB</option>
							   <option value="<?php echo $MB . ' MegaBytes'; ?>">MB</option>
							   <option value="<?php echo $GB . ' GigaBytes'; ?>">GB</option>
							   <option value="<?php echo $TB . ' TeraBytes'; ?>">TB</option>
							   <option value="<?php echo $PB . ' PetaBytes'; ?>">PB</option>
							  </select>
						    </td>
                            <td width="21%">Total Hits</td>
							<td width="18%">Unique Visitors</td>
					      </tr>					
						   <tr>
						     <td><?php echo $Month . "/" . $Year; ?></td>
							 <td><div id="<?php echo $Month . "_" . $Year; ?>_ByteDisplay_<?php echo str_replace(".","_",$ThisVhost['ServerName']); ?>"><?php echo $BandwidthConsumption . $DisplaySize;?></div></td>
							 <td><?php echo number_format($TotalHits); ?></td>
							 <td><?php echo number_format($UniqueVisitors); ?></td>
						   </tr>
                        </table>
						 <?php 
						        $OverallBytes = ($OverallBytes + $TotalBytes);
								$OverallHits = ($OverallHits + $TotalHits);
								$OverallUniques = ($OverallUniques + $UniqueVisitors);
						       }
							}      
						 ?>
					  </div>
					 </td>
                    </tr>
                  </table>
				   <br>
				   <table border="0" width="100%" class="menu">
					<tr>
					  <td><b>Actual Overall Bandwidth Consumption:</b> <?php echo number_format($OverallBytes); ?> Bytes</td>
					</tr>
					<tr>
					  <td><b>Total Unique Hits:</b> <?php echo number_format($OverallUniques); ?></td>
					</tr>	
					<tr>
					  <td><b>Total Hits:</b> <?php echo number_format($OverallHits); ?></td>
					</tr>							
					<tr>
					 <td><?php if ($OverallHits > 0) {									
									 echo "<a href=\"http://" . $ThisVhost['ServerName'] . "/Statistics\" target=\"_blank\">Read Detailed Statistics</a>";
					           } 
						 ?>
					</td>
				   </tr>
				  </table><br><br>
					<?php
					  } // Ends WHILE which iterates through websites				
					  } // ENDS SQL IF						  					  
				   ?><div align="center"><b><i><?php echo $Display; ?><br><br><font color="#FF0000"><?php if (!$ArrApache[8]) { ?>Statistics are automatically generated and updated every <?php  echo $IntApacheStatSpider . "."; } ?></font><br></i></b></div>	
				   <p></p>
				   <p></p>
		 </table>
            <p><br>
              </p>
            <p>&nbsp;</p>
            <p><br>
		                </p></td>
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