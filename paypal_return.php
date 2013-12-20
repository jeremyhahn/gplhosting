<?php
if ($_GET['Action'] == "Process") {

  require("includes/DB_ConnectionString.php");
  require("includes/GlobalConfigs.php");

function MakePayment($Username,$Plan,$PaidAmount,$InCollections,$OverDue,$Credits) {
  if (!($LogInsert = mysql_query("INSERT INTO PaymentLog(Date,Username,Plan,PaidAmount,InCollections,Overdue,Credits,UniqueID) VALUES ('" .
                                 date("Y-m-d") . "','" . $Username . "','" . $Plan . "','" . $PaidAmount . "','" . $InCollections . "','" . 
								 $OverDue . "','" . $Credits . "','" . md5(mktime()) . "')"))) {
    return "Could not create a log entry in the payment log table.<br><b>MySQL Said:</b><br>" . mysql_error();
  }
  if (!($BalanceQuery = mysql_query("SELECT * FROM PaymentSummary WHERE Username='" . $Username . "' AND Date='" . date("Y-m-d") . "'"))) {
    return "Could not query the database for overdue balance.<br><b>MySQL Said:</b><br>" . mysql_error();
  } else {
       if (mysql_num_rows($BalanceQuery) == 0) {
	      if (!($CompileQuery = mysql_query("SELECT * FROM PaymentSummary WHERE Username='" . $Username . "'"))) {
		       return "Could not query the databse for compilation factor.<br><b>MySQL Said:</b><br>" . mysql_error();
		  }
		  if (mysql_num_rows($CompileQuery) > 0) {
		     while ($PastHistory = mysql_fetch_array($CompileQuery)) {
			   $TotalPaid = ($TotalPaid + $CompileQuery['PaidAmount']);
               $TotalInCollections = ($TotalInCollections + $CompileQuery['InCollections']);
	           $TotalCredits = ($TotalCredits + $CompileQuery['Credits']);
			   mysql_query("DELETE FROM PaymentSummary WHERE Username='" . $Username . "' AND Date='" . $PastHistory . "'");
			 }
			 if ($TotalInCollections > 0) { $Overdue = 0; } else { $Overdue = 1; }
			 if (!($PaymentInsert = mysql_query("INSERT INTO PaymentSummary(Date,Username,Plan,PaidAmount,InCollections,Overdue,Credits,UniqueID) VALUES ('" .
		                                     date("Y-m-d") . "','" . $Username . "','" . $Plan . "','" . $TotalPaid . "','" . $TotalInCollections . "','" . 
											 $Overdue . "','" . $TotalCredits . "','" . md5(mktime()) . "')"))) {
		        return "Could not insert new record into payment table.<br><b>MySQL Said:</b><br>" . mysql_error();
		     } 			 
		  } else {
	         if (!($PaymentInsert = mysql_query("INSERT INTO PaymentSummary(Date,Username,Plan,PaidAmount,InCollections,Overdue,Credits,UniqueID) VALUES ('" .
		                                     date("Y-m-d") . "','" . $Username . "','" . $Plan . "','" . $PaidAmount . "','" . $InCollections . "','" . $OverDue . "','" . 
											 $Credits . "','" . md5(mktime()) . "')"))) {
		        return "Could not insert new record into payment table.<br><b>MySQL Said:</b><br>" . mysql_error();
		     }
		  }
	  } else {
	    $ExistingPayment = mysql_fetch_array($BalanceQuery);
	    if (!($CollectionsUpdate = mysql_query("UPDATE PaymentSummary Set InCollections='" . $InCollections . "'"))) {
		  return "Could not update the database field 'InCollections'.<br><b>MySQL Said:</b><br>" . mysql_error();
		}
		if (!($PaymentUpdate = mysql_query("UPDATE PaymentSummary Set PaidAmount='" . ($PaidAmount + $ExistingPayment['PaidAmount']) . "'"))) {
		  return "Could not update the database field 'InCollections'.<br><b>MySQL Said:</b><br>" . mysql_error();
		}
		if (!($Overdue = mysql_query("UPDATE PaymentSummary Set Overdue='" . $OverDue . "'"))) {
		  return "Could not update the database field 'InCollections'.<br><b>MySQL Said:</b><br>" . mysql_error();
		}
		if (!($Overdue = mysql_query("UPDATE PaymentSummary Set Credits='" . $Credits . "'"))) {
		  return "Could not update the database field 'InCollections'.<br><b>MySQL Said:</b><br>" . mysql_error();
		}		
	 }
  }
  return false;
}

  if ($_SESSION['Username'] != "") { $Username = $_SESSION['Username']; } else { $Username = $_POST['Username']; }
  
  if (!($SelectQuery = mysql_query("SELECT Plan FROM Clients WHERE Username='" . $Username . "'"))) {
    echo "Could not query the database for your registered plan.<br><b>MySQL Said</b><br>" . mysql_error();
  } else {
    $ThisUser = mysql_fetch_array($SelectQuery);
  }
  $Plan = explode("\r\n",$MemberPlans);
    foreach ($Plan as $Value) {
 	   $Data = explode(",",$Value);
 		  if ($Data[0] == $ThisUser['Plan']) {
			  $AmountDue = $Data[12];
	       }
    }
  if (!($BalanceQuery = mysql_query("SELECT * FROM PaymentSummary WHERE Username='" . $Username . "' AND Date='" . date("Y-m-d") . "'"))) {
    echo "Could not query the database for overdue balance.<br><b>MySQL Said:</b><br>" . mysql_error();
  } else {
       $Account = mysql_fetch_array($BalanceQuery);
	   if ($Account['PaidAmount'] > $AmountDue) { echo "<script language=\"JavaScript\">alert('You have already satisfied your monthly membership fee. Operation aborted.');location.href = 'Billing.php';</script>"; exit(); }
	   if ($Account['InCollections'] > 0) {
	     $ThisAmount = $Account['InCollections'];
	     $DB_InCollections = ($DB_InCollections + $ThisAmount);
	   }
	   if ($Account['Credits'] > 0) {
	     $ThisCredit = $Account['Credits'];
		 $DB_Credits = ($DB_Credits + $ThisCredit);	    
	   }
  }
 
	   if ($AmountDue) {
   		   
		   // Exact Payment In Good Standings
		   if ($AmountDue == $_POST['Donation'] && $DB_InCollections <= 0 && $Processed !=1) {
		      if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),0,0,0)) {
			     $Result = $PaymentError;
			  } else {
			    $Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". Click <a href=\"manage.php\">here</a> to log back in."; 
		        $Processed = 1;
			  }
		   }
		   // Exact Payment With An Overdue Balance		                                  
		   if ($AmountDue == $_POST['Donation'] && $DB_InCollections > 0 && $Processed !=1) {
		      if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),$DB_InCollections,1,0)) {
			     $Result = $PaymentError;
			  } else {
			    $Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>You still have an outstanding balance of \$$DB_InCollections.</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
		        $Processed = 1;
			  }
		   }
		   // Exact Payment With Credit Balance		                                  
		   if ($AmountDue == $_POST['Donation'] && $DB_Credits > 0 && $Processed !=1) {
		      if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),0,0,$DB_Credits)) {
			     $Result = $PaymentError;
			  } else {
			    $Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". You have a total credit amount of \$$DB_Credits.<br>Click <a href=\"manage.php\">here</a> to log back in."; 
		        $Processed = 1;
			  }
		   }
		   
		   
		   // Short Payment; No OverDue Balance; No Credit
		   if ($AmountDue > $_POST['Donation'] && $DB_InCollections <= 0 && $DB_Credits <= 0 && $Processed !=1) {
		      $NewBalance = $AmountDue - $_POST['Donation'];
		      if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),$NewBalance,1,0)) {
			    $Result = $PaymentError;
			  } else {
			    $Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>You now have an outstanding balance of \$$NewBalance.</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
		        $Processed = 1;
			  }
		   }
		   // Short Payment; OverDue Balance; No Credit
		   if ($AmountDue > $_POST['Donation'] && $DB_InCollections > 0 && $DB_Credits <= 0 && $Processed !=1) {
		      $NewBalance = ($AmountDue - $_POST['Donation']);
			  $NewDebt = (abs($NewBalance) + $DB_InCollections);		      
		      if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),$NewDebt,1,0)) {
			    $Result = $PaymentError;
			  } else {
			    $Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>Your outstanding balance of \$$DB_InCollections has now increased to \$$NewDebt.</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
		        $Processed = 1;
			  }
		   }
		   // Short Payment; Has Credit;
		   if ($AmountDue > $_POST['Donation'] && $DB_Credits > 0 && $Processed !=1) {
		      $NewBalance = $AmountDue - $_POST['Donation'];
			  if (($NewBalance + $DB_Credits) == $AmountDue) {
				  // Just Enough Credit
				  if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),0,0,0)) {
					$Result = $PaymentError;
				  } else {
					$Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>Your credit is now at a zero balance.</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
					$Processed = 1;
				  }			  			  
			  } elseif (($NewBalance + $DB_Credits) > $AmountDue) {
			      $NewCredit = (($NewBalance + $DB_Credits) - AmountDue) - $AmountDue;
				  // Has Credit Left Over
				  if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),0,0,$NewCredit)) {
						$Result = $PaymentError;
					  } else {
						$Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>Your credit balance is now \$$NewCredit.</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
						$Processed = 1;
				  }
			  } elseif (($NewBalance + $DB_Credits) < $AmountDue) {
			      // Has NO Credit Left Over; Exchanged It For An OverDue Balance Instead :-/
				  $NewDebt = ($NewBalance + $DB_Credits) - $AmountDue;
				  if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']), str_replace("-","",$NewDebt),1,0)) {
							$Result = $PaymentError;
				  } else {
					$Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>You have an outstanding balance left of \$" . str_replace("-","",$NewDebt) . ".</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
					$Processed = 1;
				  }		  
			  }		  
		   }
		   // Over Payment; No Credit; No Overdue Balance;
		   if ($AmountDue < $_POST['Donation'] && $DB_InCollections <= 0 && $DB_Credits <= 0 && $Processed !=1) {
			  $NewCredit = $_POST['Donation'] - $AmountDue;
			  if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),0,0,$NewCredit)) {
			    $Result = $PaymentError;
			  } else {
			    $Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>You now have a credit of \$$NewCredit.</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
			    $Processed = 1;
			  }
		   }
		   // Over Payment; Has Credit; No Overdue Balance;			
		   if ($AmountDue < $_POST['Donation'] && $DB_InCollections <= 0 && $DB_Credits > 0 && $Processed !=1) {
		      $Over = $_POST['Donation'] - $AmountDue;
			  $NewCredit = $Over + $DB_Credits;
			  if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),0,0,$NewCredit)) {
			    $Result = $PaymentError;
			  } else {
			    $Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>You have a total credit of \$$NewCredit.</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
			    $Processed = 1;
			  }
		    }
		   // Over Payment; No Credit; Overdue Balance;			
		   if ($AmountDue < $_POST['Donation'] && $DB_InCollections > 0 && $DB_Credits <= 0 && $Processed !=1) {
		      $Over = $_POST['Donation'] - $AmountDue;
			  $NewCredit = $Over - $DB_InCollections;
			  if ($NewCredit < 0 && $Processed !=1) {
				  // Still not enough to satisfy debt
				  if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),str_replace("-","",$NewCredit),1,0)) {
					$Result = $PaymentError;
				  } else {
					$Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>You have an outstanding balance of \$" . str_replace("-","",$NewCredit) . ".</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
					$Processed = 1;
				  }
			  } elseif ($NewCredit == 0 && $Processed !=1) {
				  // Just Enough
				  if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),0,0,0)) {
					$Result = $PaymentError;
				  } else {
					$Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>Your outstanding balance has been satisfied.</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
					$Processed = 1;
				  }
			  } elseif ($NewCredit > 0 && $Processed !=1) {
				  if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),0,0,str_replace("-","",$NewCredit))) {
					$Result = $PaymentError;
				  } else {
					$Result = "You have successfully entered the payment amount of $" . money_format("%i", $_POST['Donation']) . ". <b>You now have a credit balance of \$" . $NewCredit . ".</b><br>Click <a href=\"manage.php\">here</a> to log back in."; 
					$Processed = 1;
				  }
			  }			 
		    }
			 
		   
		   
		   
	   } else {
	     // A Donation!
	     if ($PaymentError = MakePayment($Username,$ThisUser['Plan'],money_format("%i",$_POST['Donation']),0,0,0)) {
			$Result = $PaymentError;
		  } else {
			$Result = "<br>Your donation of $" . $_POST['Donation'] . " has been processed.<br>Thank you very much!</b><br>Click <a href=\"manage.php\">here</a> to log back in.";
          }
	   }
  
}
?>
<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699">
<table width=780 border=0 cellpadding=0 cellspacing=0 height="383" bgcolor="#FFFFFF">
  <tr> 
    <td rowspan=2> <img src="images/index_01.gif" width=165 height=35></td>
    <td colspan=2> <img src="images/index_02.gif" width=615 height=24></td>
  </tr>
  <tr> 
    <td> <img src="images/index_03.gif" width=1 height=11></td>
    <td rowspan=2> <img src="images/index_04_logo.jpg" width=614 height=73></td>
  </tr>
  <tr> 
    <td colspan=2 height="39"> <img src="images/project_logo.gif" width=166 height=62></td>
  </tr>
  <tr> 
    <td colspan=3 background="images/links.gif"> 
     <?php include("navigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan=3 height="233"> 
      <div align="center"><br>
         <table width="50%"  border="0">
           <tr>
             <td>&nbsp;</td>
           </tr>
           <tr>
             <td>&nbsp;</td>
           </tr>
           <tr>
             <td>
               <div align="center"><?php echo $Result; ?></div>
               <div align="center"></div></td>
           </tr>
         </table>
      </div>
	   <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr> 
          <td height="212">
		  <?php
			if (isset($_GET['Confirm']) && $_GET['Confirm'] === "1") {    
				switch ($_GET['Purchase']) {   
				   case "Donation":
				   echo "<script language=\"JavaScript\">alert('Thank you for your donation to the project!');</script>";   
				}			 
		  ?>	
		 <form name="Donation" action="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Process" method="post">
		  <table width="53%"  border="0" align="center" class="menu">
            <tr>
              <td colspan="2" class="menu"><div align="center"><strong>Thank you very much for donating to the project! Please enter the amount that you just donated, so that we can update your account. Again, thank you very much! Enjoy your time with us. </strong></div></td>
              </tr>
            <tr>
              <td class="menu">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2" class="menu"></td>
              </tr>
            <tr>
              <td class="menu">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td width="30%" class="menu">Username:</td>
              <td width="70%"><input name="Username" type="text" id="Username"></td>
            </tr>
            <tr>
              <td>Donation Amount: </td>
              <td><input name="Donation" type="text" id="Donation"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="submit" name="Submit" value="Submit"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table>		
		    </form>  
		  <?php } ?>
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