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
          <td height="212">
		  <div align="center">
		    <p class="BodyHeader">&nbsp;</p>
            <p class="BodyHeader">Help :: DNS Configurations </p>
		  <?php if (!(isset($_GET['Display']) || $_GET['Display'] === "")) { ?>
            <p class="menu">The GPL Hosting website offers a very easy interface for managing your DNS namespaces. What exactly are you trying to do? </p>
            <p>&nbsp;</p>
		  <?php
			} else {
			    switch ($_GET['Display']) {
				
				
			      case "AddDomain":
				  ?>
				    <p class="menu">To add a new domain to your DNS management center, locate the DNS Hosting link in the Control Panel.</p>
				    <p class="menu"><img src="images/Help/ControlPanel.gif" width="137" height="151"></p>
				    <p class="menu">Once you roll over the DNS Hosting link, you will see the following menu appear.</p>
				    <p class="menu"><img src="images/Help/DNS_Menu.gif" width="107" height="68"> </p>
				    <p class="menu">After you click on New Domain, you will be prompted with a selection between a top level, and thrid level domain. If you choose one of our FREE third level domains, there is no cost involved in setting up a domain name. If you want your own top level domain (such as domain.com), then you must register a domain name yourself, or purchase one through <a href="http://www.cyberdataengineering.com" target="_blank">CyberData Engineering</a> for $12 /yr. After you have purchased your top level domain, then you may use ns1.gplhosting.org, and ns2.gplhosting.org as your registered name servers, where you can then control your zones using our website control panel. </p>
				    <p class="menu">From here, you will then be prompted to type in the domain name which you would like to use. If you are using a third level domain, you will only need to type in a hostname, where the desired third level domain will be appended to the hostname when you click on the 'submit' button. If you are using your own top level domain here, simply type in the Fully Qualified Domain Name (FQDN) and then click 'submit'.</p>
				    <p class="menu">Notice that your IP address is automatically detected, and filled out in the textboxes. If you wish to change these values, please know what you are doing! If you leave a testbox empty, the associated records for that pertain to that host will be omited. </p>
				    <p><?php
				  break;
				
				
				  case "DeleteDomain":
				  ?></p>
		      <p>&nbsp;</p>
		         <p class="menu">To delete an existing domain from your DNS management center, begin by locating the DNS Hosting link in the Control Panel.</p>
		         <p class="menu"><img src="images/Help/ControlPanel.gif" width="137" height="151"></p>
		         <p class="menu">Once you roll over the DNS Hosting link, you will see the following menu appear.</p>
		         <p class="menu"><img src="images/Help/DNS_Menu.gif" width="107" height="68"> </p>
		         <p class="menu">From here, simply click on the Delete Domain link, and then click on the domain name that you want to delete. A JavaScript confirmation box will appear asking you if you are sure you want to delete this domain. Just click ok, and you will return to the DNS home administration panel where you will note that the selected domain has just been deleted. </p>
		         <p><?php
				  break;
				
				
				
				  case "AddRecord":
				  ?>
			        </p>
				    <p class="menu">To add a new record to an existing DNS domain, first locate the DNS Hosting link in the Control Panel.</p>
				    <p class="menu"><img src="images/Help/ControlPanel.gif" width="137" height="151"></p>
				    <p class="menu">Once you roll over the DNS Hosting link, you will see the following menu appear.</p>
				    <p class="menu"><img src="images/Help/DNS_Menu.gif" width="107" height="68"></p>
				    <p class="menu">Click on the 'New Record' link, and then you will see the following options.</p>
				    <p class="menu"><img src="images/Help/DNS_AddRecord.gif" width="319" height="151"> </p>
				    <p class="menu">Please see the <a href="HELP_DNS_Jargon.php">Jargon &amp; Acronyms</a> page for technical details on what each of these record types are.</p>
				    <p class="menu">From here, simply select the record type you would like to add to your DNS zone, and then follow the record-specific instructions on the following page.</p>
				    <p>
              <?php
				    break;
				  
				  
				  
				
				  case "DeleteRecord":
				  ?></p>
				    <p class="menu">To delete an existing record from a DNS domain, first locate the DNS Hosting link in the Control Panel.</p>
				    <p class="menu"><img src="images/Help/ControlPanel.gif" width="137" height="151"></p>
				    <p class="menu">From here, click on the DNS Hosting link to display a listing of each of your DNS domains. Expand the tree to show the specified record which you would like to delete, and then simply click on the record. You will be asked if you wish to modify the record. Click ok, and then proceed to the following page where you have the option of updating/removing the record from the zonefile.</p>
				    <p><?php
				  break;
			    }			  
			}
		  ?></p>
				    <p>&nbsp;</p>
				    <table width="50%" border="0" class="menu">
              <tr>
                <td width="195"><div align="center">
                  <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Display=AddDomain">Add a new domain </a></p>
                  </div></td>
                <td width="177"><div align="center">
                  <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Display=DeleteDomain">Delete a domain </a></p>
                  </div></td>
              </tr>
              <tr>
                <td><div align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Display=AddRecord">Add a new record </a></div></td>
                <td><div align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Display=DeleteRecord">Delete a record </a></div></td>
              </tr>
			  <tr>
			    <td colspan="2">&nbsp;</td>
			    </tr>
			  <tr>
			  <td colspan="2"><div align="center"><a href="HELP.php">Return To Help Menu</a> </div></td>
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