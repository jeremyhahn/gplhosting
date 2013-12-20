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
          <td height="212"><div align="center">
            <p>&nbsp;</p>
            <p class="BodyHeader">Help :: 
			<?php
			  switch ($_GET['Display']) {
			    case "POP3":
				echo "POP3 Mailboxes";
				break;
				
				case "MX":
				echo "MX Backup Services";
				break;
				
				case "Port25":
				echo "Port 25 Deflections";
				break;
			  } 
			?></p>
            <p class="menu">
			<?php
			  switch ($_GET['Display']) {
			    case "POP3":
				?>
			  </p>
            <p class="menu">To add a new POP3 domain to your mail management center, locate the Mail Hosting link in the Control Panel.</p>
            <p class="menu"><img src="images/Help/ControlPanel.gif" width="137" height="151"></p>
            <p class="menu">After rolling over the Mail Hosting link, you will see the following submenu appear.</p>
            <p class="menu"><img src="images/Help/MAIL_Menu.gif" width="96" height="26"> </p>
            <p class="menu">Click on the New Domain link, and then you will see the following menu:</p>
            <p class="menu"><img src="images/Help/MAIL_AddDomain.gif" width="285" height="127"> </p>
            <p class="menu">Click on the option labeled 'Standard POP3 Domain', and then type in the Fully Qualified Domain Name (FQDN) of the zone you wish to collect mail for into the textbox provided. When you are done, click on the 'create' button. </p>
            <p class="menu">
			    <?php
				break;
				
				case "MX":
				?>
			  </p>
            <p class="menu">To add a new MX Backup domain to your mail management center, locate the Mail Hosting link in the Control Panel.</p>
            <p class="menu"><img src="images/Help/ControlPanel.gif" width="137" height="151"></p>
            <p class="menu">After rolling over the Mail Hosting link, you will see the following submenu appear.</p>
            <p class="menu"><img src="images/Help/MAIL_Menu.gif" width="96" height="26"> </p>
            <p class="menu">Click on the New Domain link, and then you will see the following menu:</p>
            <p class="menu"><img src="images/Help/MAIL_AddDomain.gif" width="285" height="127"> </p>
            <p class="menu">Click on the option labeled 'MX-Backup Domain', and then type in the Fully Qualified Domain Name (FQDN) of the zone which you collect mail on behalf of, into the first textbox provided. In the second textbox provided, you will enter the FQDN of <em>your</em> mail server. When you are done, click on the 'create' button.</p>
            <p class="menu">
			    <?php
				break;
				
				case "Port25":
				?>
			  </p>
            <p class="menu">To add a new Port 25 deflection domain to your mail management center, locate the Mail Hosting link in the Control Panel.</p>
            <p class="menu"><img src="images/Help/ControlPanel.gif" width="137" height="151"></p>
            <p class="menu">After rolling over the Mail Hosting link, you will see the following submenu appear.</p>
            <p class="menu"><img src="images/Help/MAIL_Menu.gif" width="96" height="26"> </p>
            <p class="menu">Click on the New Domain link, and then you will see the following menu:</p>
            <p class="menu"><img src="images/Help/MAIL_AddDomain.gif" width="285" height="127"> </p>
            <p class="menu">Click on the option labeled 'Port 25 Redirect', and then type in the Fully Qualified Domain Name (FQDN) of the zone which you collect mail on behalf of, into the first textbox provided. In the second textbox provided, you will enter the FQDN of <em>your</em> mail server. In the third textbox, enter the port number where your mail server is listening. When you are done, click on the 'create' button.</p>
            <p class="menu">
                <?php
				break;
			  } 
			?>
		      </p>
            <p>&nbsp;</p>
            <table width="50%" border="0" class="menu">
              <tr>
                <td width="53%"><div align="center">
                    <p><a href="HELP_MAIL.php">Return To Mail Help Menu</a></p>
                    </div>                  </td>
                <td width="47%"><div align="center"><a href="HELP.php">Return To Main Help Menu</a></div></td>
              </tr>
            </table>
            <p>&nbsp; </p>
          </div></td>
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