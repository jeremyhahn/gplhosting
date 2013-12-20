<?php
session_start();
require("includes/GetOuttaHere.php");
require("includes/DB_ConnectionString.php");
require("includes/GlobalConfigs.php");

if (isset($_GET['Action']) && $_GET['Action'] == "Update") {

   // Reset 'Active' flag bit to 0 on previous billing cycle
   if (!($UpdateQuery = mysql_query("Update BillingLog Set Active='0' WHERE InvoiceID='" . $LastBilling['InvoiceID'] . "'"))) {
      $Result .= "An error occurred while attempting to perform a SQL update query on the BillingLog database table.<br><b>MySQL Said:</b><br>" . mysql_error();
   }
   if (!($UpdateQuery = mysql_query("Update BillingLog Set Status='0' WHERE InvoiceID='" . $LastBilling['InvoiceID'] . "'"))) {
      $Result .= "An error occurred while attempting to perform a SQL update query on the BillingLog database table.<br><b>MySQL Said:</b><br>" . mysql_error();
   }
   
   
}


// Retrieve billing cycle type (1=monthly,2=yearly)
$Member = explode("\r\n",$MemberPlans);
foreach ($Member as $Value) { 
	   $ThisMember = explode(",",$Value);
	   if ($ThisMember[0] == $_SESSION['SiteRole']) {
		  $ThisPlan = $ThisMember[0];
		  $Fee = $ThisMember[12];
		  $BillingType = $ThisMember[13];
	   }
}
// Get clients billing information
if (!($InfoQuery = mysql_query("SELECT * FROM BillingInfo WHERE Username='" . $_SESSION['Username'] . "'"))) {
   $Result = "An error occurred while attempting to perform a SQL query on the ClientInfo database table.<br><b>MySQL Said:</b><br>" . mysql_error();
} else {
   $ClientInfo = mysql_fetch_array($InfoQuery);
}
$StartBilling = $ClientInfo['StartBilling'];
$ArrBilling = explode("/",$StartBilling);
$StartMonth = $ArrBilling[0];
// Create a new invoice number
if (!($InvoiceQuery = mysql_query("SELECT InvoiceID FROM BillingLog ORDER BY InvoiceID DESC"))) {
   $Result .= "An error occurred while attempting to perform a SQL select query on the BillingLog database table to retrieve new invoice number.<br><b>MySQL Said:</b><br>" . mysql_error();
} else {
   $Invoice = mysql_fetch_array($InvoiceQuery);
   $InvoiceID = $Invoice['InvoiceID']+1;
}
// Retrieve payment information for last billing cycle
if ($BillingType == 1) {
   if ($StartMonth == 1) {
      $SQL = "SELECT * FROM BillingLog WHERE Username='" . $_SESSION['Username'] . "' AND BillingCycle='12/01/" . (date("Y")-1) . 
	         "-" . date("m") . "/01/" . date("Y") . "' AND Active='1'";
   } else {
      $SQL = "SELECT * FROM BillingLog WHERE Username='" . $_SESSION['Username'] . "' AND BillingCycle='" . (date("m")-1) . "/01/" . 
	          date("Y") . "-" . date("m") . "/01/" . date("Y") . "' AND Active='1'";
   }
   if (!($LastBillingQuery = mysql_query($SQL))) {
	  $Result .= "An error occurred while attempting to perform a SQL select query on the BillingLog database table.1<br><b>MySQL Said:</b><br>" . mysql_error();
   }
} elseif ($BillingType == 2) {
   if (!($LastBillingQuery = mysql_query("SELECT * FROM BillingLog WHERE Username='" . $_SESSION['Username'] . "' AND BillingCycle='" . 
										 $StartMonth . "/01/" . (date("Y")-1) . "-" . $StartMonth . "/01/" . date("Y") . "' AND Active='1'"))) {
	  $Result .= "An error occurred while attempting to perform a SQL select query on the BillingLog database table.2<br><b>MySQL Said:</b><br>" . mysql_error();
   }
}
if (mysql_num_rows($LastBillingQuery) > 0) {
   // Get previous debts/credits
   $LastBilling = mysql_fetch_array($LastBillingQuery);
   if ($LastBilling['InCollections']) {
       // Turn this billing cycle into collections 
	   $PriorDebt = $LastBilling['InCollections'];
	   $chkID = mysql_query("SELECT InvoiceID FROM PaymentInCollection WHERE InvoiceID='" . $LastBilling['InvoiceID'] . "'");
	   if (!mysql_num_rows($chkID)) {
	      if (!($InsertQuery = mysql_query("INSERT INTO PaymentInCollection(InvoiceID,BillingCycle,Username,TotalPaid,InCollections,Status) VALUES('" .
									      $LastBilling['InvoiceID'] . "','" . $LastBilling['BillingCycle'] . "','" . $LastBilling['Username'] . "','" . 
									      $LastBilling['TotalPaid'] . "','" . $LastBilling['InCollections'] . "','" . $LastBilling['Status'] . "')"))) {
		     $Result .= "An error occurred while attempting to perform a SQL insert query on the PaymentInCollection database table.<br><b>MySQL Said:</b><br>" . mysql_error();
	      }
	  }
   }   
   if ($LastBilling['Credit']) {
      // Credit this billing cycle with left over credit
      $PriorCredit = $LastBilling['Credit'];
   } else {
      $PriorCredit = 0;
   }
}  else {
   $PriorDebt = 0;
   $PriorCredit = 0;
}
// --------------------------------------------->
// Get current billing cycle
switch ($BillingType) {
   case 1:
     $SQL = "SELECT * FROM BillingLog WHERE Username='" . $_SESSION['Username'] . "' AND BillingCycle='" . date("m") . "/01/" . date("Y") .
	        "-" . (date("m")+1) . "/01/" . date("Y") . "' AND Active='1'";
   break;
   
   case 2:
     $SQL = "SELECT * FROM BillingLog WHERE Username='" . $_SESSION['Username'] . "' AND BillingCycle='" . date("m") . "/01/" . date("Y") .
	        "-" . date("m") . "/01/" . (date("Y")+1) . "' AND Active='1'";
   break;
}
if (!($CurrentPaymentQuery = mysql_query($SQL))) {
   $Result .= "An error occurred while attempting to perform a SQL select query on the BillingLog database table.<br><b>MySQL Said:</b><br>" . mysql_error();
}
if (!mysql_num_rows($CurrentPaymentQuery) == 0) {
      
   // Get new payment information
   $CurrentPayments = mysql_fetch_array($CurrentPaymentQuery);
    switch ($BillingType) {
	   case 1:
	     $TotalPaid = $CurrentPayments['TotalPaid'];
		 $Debt = $CurrentPayments['InCollections'];
		 $Credit = $CurrentPayments['Credit'];
	   break;
	   
	   case 2:
	     $TotalPaid = $CurrentPayments['TotalPaid'];
		 $Debt = $CurrentPayments['InCollections'];
		 $Credit = $CurrentPayments['Credit'];
	   break;
	}
	
} else {
   $TotalPaid = 0;
   $Debt = $PriorDebt;
   $Credit = $PriorCredit;
}
   
if ($TotalPaid) {
   $Credit = ($Credit + $TotalPaid);
}
// Get enabled tax values from the database
if (!($TaxQuery = mysql_query("SELECT * FROM BillingTaxes WHERE Enabled='1'"))) {
   $Result .= "An error occurred while attempting a select query on the PaymentTaxes database table.<br><b>MySQL Said:</b><br>" . mysql_error();
}
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<script language="javascript">
function MakePayment(InvoiceID,Business,ItemName,ItemNumber,Amount,Custom,GATEWAY,MODE) {
  switch (GATEWAY) {
    
	case "PayPal":
	     PostString = new String("invoice="+InvoiceID+"&business="+Business+"&item_name="+ItemName+"&item_number="+ItemNumber+"&amount="+Amount+"&custom="+Custom);
		 if (!MODE) {            
            window.open('http://www.paypal.com/xclick/'+PostString,'','');
		 } else {
 		    window.open('http://www.sandbox.paypal.com/xclick/'+PostString,'','');
		 }
	break;
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
          <td height="212"><table width="90%"  border="0" align="center" class="style2">
            <tr>
              <td><div align="center"><?php echo $Result; ?></div></td>
            </tr>
          </table>            <p>&nbsp;</p>
            <table width="556"  border="0" align="center" class="CustomTable" cellpadding="0" cellspacing="0">
            <tr>
              <td width="52%" class="TableHeader">Project GPL Hosting<br>
                <span class="style1">10151 NW 35 ST - 2B<br>
                Coral Springs, Fl. 33065</span></td>
              <td width="48%"><div align="right">INVOICE # &nbsp; &nbsp; <font class="style2"><?php echo $InvoiceID; ?></font> &nbsp;
			   &nbsp; </div></td>
              </tr>
            <tr>
              <td height="192" colspan="2"><p>&nbsp;</p>
                <table width="636"  border="0" align="center" background="images/Invoice.gif" class="menu">
                  <tr>
                    <td width="11%">&nbsp;</td>
                    <td width="28%">&nbsp;</td>
                    <td width="13%">&nbsp;</td>
                    <td width="29%">&nbsp;</td>
                    <td width="19%">&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr class="style2">
                    <td height="28">&nbsp;</td>
                    <td><strong><?php echo $_SESSION['Username']; ?></strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><strong><?php echo date("m-d-y"); ?></strong></td>
                  </tr>
                  <tr class="style2">
                    <td height="23">&nbsp;</td>
                    <td><strong><?php echo $ClientInfo['Address']; ?></strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr class="style2">
                    <td height="24">&nbsp;</td>
                    <td><strong><?php echo $ClientInfo['City']; ?></strong></td>
                    <td><strong><?php echo $ClientInfo['State']; ?></strong></td>
                    <td><strong><?php echo $ClientInfo['Zip']; ?></strong></td>
                    <td><strong>Tux</strong></td>
                  </tr>
                  <tr class="style2">
                    <td height="24">&nbsp;</td>
                    <td><strong><?php echo $ClientInfo['Phone']; ?></strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table>                </td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><table width="100%" class="menu">
                <tr class="TableHeader">
                  <td colspan="2">Billing Cycle </td>
                  <td width="43%">Service/Description </td>
                  <td width="14%">&nbsp;</td>
                  <td width="17%" bgcolor="#FFFFCC">Total</td>
                  </tr>
                <tr>
                  <td width="13%">From</td>
                  <td width="13%">To</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="5"><hr class="highlight"></td>
                </tr>
				<?php
				switch ($BillingType) {
													  
					  case 1:
					  ?>
						<tr class="<?php echo $Class; ?>">
						  <td class="highlight"><?php echo (date("m")-1) . "-" . date("d-Y"); ?></td>
						  <td class="highlight"><?php echo date("m-d-y"); ?></td>
						  <td colspan="2" class="highlight"><?php echo $ThisPlan; ?></td>
						  <td bgcolor="#FFFFCC" class="highlight"><?php echo "\$" . money_format("%i" , $Fee); ?></td>
						</tr>
					  <?php						  
					  break;
					  
					  case 2:
					  ?>
						<tr class="<?php echo $Class; ?>">
						  <td class="highlight"><?php echo date("m-d") . "-" . (date("Y")-1); ?></td>
						  <td class="highlight"><?php echo date("m-d") . "-" . date("Y"); ?></td>
						  <td colspan="2" class="highlight"><?php echo $ThisPlan . " Membership"; ?></td>
						  <td bgcolor="#FFFFCC" class="highlight"><?php echo "\$" . money_format("%i" , $Fee); ?></td>
						</tr>
					  <?php
					  break;
				   }
                   $Class = "TableRow";  
				   $ArrServices = explode(",",$ClientInfo['Services']);
				   foreach ($ArrServices as $ServiceID) {
					       if (!($SvcQuery = mysql_query("SELECT * FROM AdditionalServices WHERE ServiceID='" . $ServiceID . "'"))) {
							  $Result .= "An error occurred while attempting a select query on the AdditionalServices database table.<br><b>MySQL Said:</b><br>" . mysql_error();
                           } 
						   if (mysql_num_rows($SvcQuery)) {
							  $ServiceDetails = mysql_fetch_array($SvcQuery);								 
						  }
					      if ($Class == "TableRow") { $Class = "TableRow2"; } else { $Class = "TableRow"; }
						  switch ($BillingType) {
													  
							  case 1:
							  $Subtotal = ($ServiceDetails['Cost/mo'] + $Subtotal);
							  ?>
								<tr class="<?php echo $Class; ?>">
								  <td class="highlight"><?php echo (date("m")-1) . "-" . date("d-Y"); ?></td>
								  <td class="highlight"><?php echo date("m") . "-" . date("d-Y"); ?></td>
								  <td colspan="2" class="highlight"><?php echo $ServiceDetails['Service']; ?> - <?php echo $ServiceDetails['Description']; ?></td>
								  <td bgcolor="#FFFFCC" class="highlight"><?php echo "\$" . money_format("%i", $ServiceDetails['Cost/mo']); ?></td>
								</tr>
							  <?php		
							  break;
								  
							  case 2:
							  $Subtotal = ($ServiceDetails['Cost/yr'] + $Subtotal);
							  ?>
								<tr class="<?php echo $Class; ?>">
								  <td class="highlight"><?php echo date("m-d") . "-" . (date("Y")-1); ?></td>
								  <td class="highlight"><?php echo date("m-d") . "-" . date("Y"); ?></td>
								  <td colspan="2" class="highlight"><?php echo $ServiceDetails['Service']; ?> - <?php echo $ServiceDetails['Description']; ?></td>
								  <td bgcolor="#FFFFCC" class="highlight"><?php echo "\$" . money_format("%i", $ServiceDetails['Cost/yr']); ?></td>
								</tr>
							  <?php
							  break;
						   }
					  }
			        ?>                
               				
                <tr>
                  <td colspan="5">&nbsp;</td>
                  </tr>
                <tr>
                  <td colspan="2">&nbsp;</td>
                  <td>
				     <?php if ($TotalPaid >= $Fee) { ?>
                       <img src="images/paid.gif" width="194" height="76">
                      <?php } ?>
				 </td>
                  <td>Subtotal:</td>
                  <td bgcolor="#FFFFCC" class="style2"><?php echo "\$" . money_format("%i", $Subtotal); ?></td>
                </tr>
				<?php if (mysql_num_rows($TaxQuery) > 0 && $ClientInfo['TaxExempt'] < 1) { ?>
                <tr>
                  <td colspan="2">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>Tax:</td>
                  <td bgcolor="#FFFFCC">
				    <?php 
					     if (mysql_num_rows($TaxQuery)) {
						 ?>						 
						 <select name="Tax" id="Tax" class="menu" onChange="JavaScript:document.getElementById('GrandTotal').innerHTML = '$'+CalculateTotal();">
						   <?php
						      $TaxCount =0;
						      while ($ThisTax = mysql_fetch_array($TaxQuery)) {
								  $TaxCount++;
								  if ($TaxCount == 1) {
									  echo "<option value=\"" . $ThisTax['TaxValue'] . "\" selected>" . $ThisTax['TaxName'] . " - " . $ThisTax['TaxValue'] * 100 . "%" . "</option>";
									  $Tax = $ThisTax['TaxValue'];
								  } elseif ($TaxCount > 1) {
									  echo "<option value=\"" . $ThisTax['TaxValue'] . "\">" . $ThisTax['TaxName'] . " - " . $ThisTax['TaxValue'] * 100 . "%" . "</option>";
									  if (!$Tax) { $Tax = $ThisTax['TaxValue']; }
								  } 
						      }
						   ?>
					    </select>
						   <script language="javascript">
						   function CalculateTotal() {
						    var SubTotal = Number('<?php echo (($Subtotal + $Debt) - $Credit); ?>');
						    var Tax = (Number(SubTotal) * Number(document.getElementById('Tax').value));
						    var GrandTotal = (Number(SubTotal) + (Number(Tax)));
						    strTotal = new String(GrandTotal);
						    ArrTotal = strTotal.split(".");
						    if ((ArrTotal[1].length) < 2) {
						       Decimals = ArrTotal[1] + 0;
							   return (ArrTotal[0]+'.'+Decimals);
						    } else if ((ArrTotal[1].length) > 2) {
							   Base = new String(ArrTotal[1]);
							   NewBase = Number(Base.substr(0,2));
							   RoundBase = Number(Base.substr(3,(ArrTotal[1].length)));
							   Decimal = Math.ceil(NewBase+"."+RoundBase);
							   return (ArrTotal[0]+'.'+Decimal);
							}						    
						   }
						   </script>
						  <?php
						  }
					      ?>
				   </td>
                 </tr>
				 <?php } ?>
				 <?php if ($Debt) { ?>
                 <tr>
                  <td colspan="2">&nbsp;</td>
                  <td>&nbsp;</td>
				  <td>Overdue:</td>
                  <td bgcolor="#FFFFCC" class="style2"> <?php echo "\$" . money_format("%i", $Debt); ?></td>
				</tr>
				<?php } ?>              
				<?php if ($Credit) { ?>
                <tr>
                  <td colspan="2">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>Credit:</td>
                  <td bgcolor="#FFFFCC" class="style2"> <?php echo "\$" . money_format("%i", $Credit); ?></td>
                </tr>
				<?php } ?>
                <tr class="style2">
                  <td colspan="2">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td><strong>Total:</strong></td>
                  <td bgcolor="#FFFFCC">
				    <div id="GrandTotal">
				    <?php
					   $GrandTotal = ($Subtotal + $Debt) - $Credit;
					   $TotalTax = ($GrandTotal * $Tax);
					   $GrandTotal = ($GrandTotal + $TotalTax);
					   echo "\$" . money_format("%i", $GrandTotal); 
					 ?>
					 </div>
				  </td>
                  </tr>
              </table>
			 </td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              </tr>
            <tr>
              <td colspan="2"><div align="center"><img src="images/InvoiceFooter.gif" width="614" height="108"></div></td>
              </tr>
          </table>
            <table width="636"  border="0" align="center">
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td width="491">&nbsp;</td>
                <td width="135"><input type="button" value="Make Payment" onClick="JavaScript:MakePayment('<?php // echo $InvoiceID; ?>105','jeremy@pc-technics.com','Project GPL Hosting','Project GPL Hosting Donations','<?php echo $GrandTotal; ?>','<?php echo $_SESSION['Username']; ?>','PayPal',1)"></td>
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