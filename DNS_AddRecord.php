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
function Step2(RecType) {
  location.href = 'DNS_AddRecord.php?Action=ChooseZone&RecType='+RecType;
}
function Step3(ZoneID,RecType) {
  location.href = 'DNS_AddRecord2.php?ZoneID='+ZoneID+'&RecType='+RecType;
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
                <td colspan="3" class="highlight">
				<div id="RecInfo" align="center">What type of record would you like to add?</div>
				<div id="DomInfo" align="center" style="display:none;">Which domain would you like to add this record to?</div>
				</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="3">
				<?php 
				   if (isset($_GET['Action']) && $_GET['Action'] == "ChooseZone") {
				?>
				<script language="javascript">
				 document.getElementById('RecInfo').style.display = 'none';
				 document.getElementById('DomInfo').style.display = '';
				</script>
				<table align="center" width="200" border="1" bordercolor="#F9F9F9" class="menu">
				   <?php 
				   if (!($ZoneQuery = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' ORDER BY Zone"))) {
					    $Result = "<tr><td>Could not execute SQL query.<br><b>MySQL Said:</b><br>" . mysql_error() . "</td></tr>";
					  } else {					
					       if (mysql_num_rows($ZoneQuery) == 0) {
						    echo "<tr><td align=\"center\"><b><i>You do not have any DNS zones configured at this time.</i></b></td></tr>";
						   }				             
							  while($ThisZone = mysql_fetch_array($ZoneQuery)) {
							   echo "<tr><td width=\"5\"><input type=\"radio\" onClick=\"JavaScript:Step3('" . $ThisZone['ZoneID'] . "','" . $_GET['RecType'] . "');\" style=\"cursor:hand;\"></td><td><a href=\"JavaScript:Step3('" . $ThisZone['ZoneID'] . "','" . $_GET['RecType'] . "');\">" . $ThisZone['Zone'] . "</a></td></tr>\r\n";
							  }
					  }
					  ?>
                </table>
				<?php 
				} else {				
				?>
				<table width="50%"  border="1" align="center" bordercolor="#F9F9F9">
                  <tr class="menu">
                    <td><input name="RecType" id="A_REC" type="radio" value="A" onClick="JavaScript:Step2('A');"><label for="A_REC" style="cursor:hand;">A-Record</label></td>
                    <td class="highlight"><label for="A_REC" style="cursor:hand;">Domain-To-IP</label></td>
                  </tr>
                  <tr class="menu">
                    <td><input name="RecType" id="CNAME_REC" type="radio" value="CNAME"  onClick="JavaScript:Step2('CNAME');">
                        <label for="CNAME_REC" style="cursor:hand;">CNAME-Record</label></td>
                    <td class="highlight"><label for="CNAME_REC" style="cursor:hand;">Ailas Record For Hostname</label></td>
                  </tr>
                  <tr class="menu">
                    <td><input name="RecType" id="MX_REC" type="radio" value="MX"  onClick="JavaScript:Step2('MX');">
                        <label for="MX_REC" style="cursor:hand;">MX-Record</label></td>
                    <td class="highlight"><label for="MX_REC" style="cursor:hand;">Authoritative Mail Servers</label></td>
                  </tr>
                  <tr class="menu">
                    <td><input name="RecType" id="NS_REC" type="radio" value="NS"  onClick="JavaScript:Step2('NS');">
                        <label for="NS_REC" style="cursor:hand;">NS-Record</label></td>
                    <td class="highlight"><label for="NS_REC" style="cursor:hand;">Authoritative Name Servers</label></td>
                  </tr>
                </table>
				<?php
				}
				?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="22%">&nbsp;</td>
                <td width="25%">&nbsp;</td>
                <td width="35%">&nbsp;</td>
              </tr>
            </table>
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