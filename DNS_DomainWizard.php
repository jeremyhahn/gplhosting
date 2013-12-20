<?php
session_start();
require("includes/GetOuttaHere.php");
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<script language="javascript">
function ClearMe(el) {
  el.value = '';
}
function RestoreMe(el) {
   if (el.value == '') {
    el.value = '<?php echo $_SERVER['REMOTE_ADDR']; ?>';
   } 
}
function PostSelection(el) {
location.href = 'DNS_DomainWizard2.php?DomainType='+el.value;
}
</script>
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" link="#666699">
<table width=780 border=0 cellpadding=0 cellspacing=0 bgcolor="#FFFFFF">
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
                <td colspan="3"><table width="90%"  border="0" align="center" class="menu">
                  <tr>
                    <td class="highlight">Would you like to create one of our free third level domains (you.domain.com), or do you have your own top level domain (domain.com) that you would like to configure?</td>
                  </tr>
                </table>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td width="22%">&nbsp;</td>
                <td width="4%">&nbsp;</td>
                <td width="56%">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><input type="radio" value="ThirdLevelDomain" id="optDom1" onClick="JavaScript:PostSelection(this);"></td>
                <td><label for="optDom1" style="cursor:hand;">I want to create a FREE third level domain.</label></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><input type="radio" value="OwnDomain" id="optDom2" onClick="JavaScript:PostSelection(this);"></td>
                <td><label for="optDom2" style="cursor:hand;">I have my own top level domain.</label></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
			<p>&nbsp;</p>
            <p>&nbsp;</p>
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