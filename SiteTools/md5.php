<?php

   if ($_SERVER['REQUEST_METHOD'] == "POST") {
     $EncryptedString = md5($_POST['EncryptThis']);
   }

?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="../style.css">
<script language="javascript">
function SubmitString() {
  if (document.EncryptionForm.EncryptThis.value == '') {
    alert('You must enter a string to encrypt, as blank passwords are not secure even if they are encrypted!');
	return false;
  }
  document.EncryptionForm.submit();
}
</script>
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699">
<table width=780 border=0 cellpadding=0 cellspacing=0 height="383" bgcolor="#FFFFFF">
  <tr> 
    <td rowspan=2> <img src="../images/index_01.gif" width=165 height=35></td>
    <td colspan=2> <img src="../images/index_02.gif" width=615 height=24></td>
  </tr>
  <tr> 
    <td> <img src="../images/index_03.gif" width=1 height=11></td>
    <td rowspan=2> <img src="../images/index_04_logo.jpg" width=614 height=73></td>
  </tr>
  <tr> 
    <td colspan=2 height="39"> <img src="../images/project_logo.gif" width=166 height=62></td>
  </tr>
  <tr> 
    <td colspan=3 background="../images/links.gif"> 
     <?php include("../navigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan=3 height="233"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr> 
          <td height="212"><p align="center" class="BodyHeader">MD5 HASH Generator</p>
           <form name="EncryptionForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		    <table width="50%"  border="0" align="center" class="menu">
            <tr>
              <td width="30%">String To Encrypt: </td>
              <td width="70%"><input name="EncryptThis" type="text" id="EncryptThis"></td>
            </tr>
            <tr>
			<?php if (isset($EncryptedString) && $EncryptedString !="") { ?>
              <td>The encrypted value is:</td>
              <td><?php echo $EncryptedString; ?></td>
			<?php } else { ?>
			  <td>&nbsp;</td>
              <td>&nbsp;</td>
			<?php } ?>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="button" value="Encrypt" onClick="JavaScript:SubmitString();"></td>
            </tr>
          </table>
		  </form>
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