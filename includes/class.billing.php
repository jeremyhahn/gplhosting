<?php

class PGPL_Billing extends PGPL_HOSTING {

      function PGPL_Billing($Gateway="",$MerchantID="",$Username="",$Password="",$DB_Table="") {
	            
			   $this->Gateway = $Gateway;
			   $this->MerchantID = $MerchantID;
               $this->Username= $Username;
			   $this->Password = $Password;
			   $this->DB_Table = $DB_Table;
	  }
	  // Create a new invoice number
		if (!($InvoiceQuery = mysql_query("SELECT InvoiceID FROM BillingLog ORDER BY InvoiceID DESC"))) {
		   $Result .= "An error occurred while attempting to perform a SQL select query on the BillingLog database table to retrieve new invoice number.<br><b>MySQL Said:</b><br>" . mysql_error();
		} else {
		   $Invoice = mysql_fetch_array($InvoiceQuery);
		   $InvoiceID = $Invoice['InvoiceID']+1;
		}
	  // Tally up previous debts/credits
	  function TallyAccount() {
	  
	  }
	  // Automatically update users personal information based on PayPal account information if different
	  function UpdateProfile() {
	  
	  }
	  // Makes a payment to the registered payment gateway
	  function MakePayment() {
	           
			   switch ($this->Gateway) {
			           
					   case "PayPal":
					   
					   break;
			   }
	  }









}


?>