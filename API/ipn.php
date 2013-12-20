<?php
#########################################################
#    Copyright © EliteWeaver UK All rights reserved.    #
#########################################################
#                                                       #
#  Program         : IPN Development Handler            #
#  Author          : Marcus Cicero                      #
#  File            : license.txt                        #
#  Function        : MINI EULA                          #
#  Version         : 2.0                                #
#  Last Modified   : 10/04/2003                         #
#  Copyright ©     : EliteWeaver UK                     #
#                                                       #
#########################################################
#    THIS SCRIPT IS FREEWARE AND IS NOT FOR RE-SALE!    #
#########################################################
#              END USER LICENCE AGREEMENT               #
# Redistribution and  use in source and/or binary forms #
# with or without  modification, are permitted provided #
# that the above copyright notice is  reproduced in the #
# script, documentation and/or any other materials that #
# may  have been provided in the original distribution. #
#########################################################
#    Copyright © EliteWeaver UK All rights reserved.    #
#########################################################

// ----------------------------------------------------------------------->
//                  -- CHANGE LOG --
//                     ----------
// Modified by Jeremy Hahn, on July 13, 2004.
// Added support for testing via PayPal's sandbox
// Integrated IPN code into Project GPL Hosting payment management system.
// ------------------------------------------------------------------------>

// IPN validation modes, choose: 1, 2 or 3.
$postmode=3;
           //* 1 = Live Via PayPal Network
           //* 2 = Test Via EliteWeaver UK
           //* 3 = Test Via PayPal's Sandbox (www.sandbox.paypal.com)

// Debugger, 1 = on and 0 = off
$debugger=1;

// Convert super globals on older php builds
	if (phpversion() <= '4.0.6') {
		$_SERVER = ($HTTP_SERVER_VARS);
		$_POST = ($HTTP_POST_VARS); 
	}

// No ipn post means this script does not exist
	if (!@$_POST['txn_type']) {
		@header("Status: 404 Not Found"); 
		exit; 
	} else {
		@header("Status: 200 OK");  // Prevents ipn reposts on some servers

		// Add "cmd" to prepare for post back validation
		// Read the ipn post from paypal or eliteweaver uk
		// Fix issue with php magic quotes enabled on gpc
		// Apply variable antidote (replaces array filter)
		// Destroy the original ipn post (security reason)
		// Reconstruct the ipn string ready for the post
		
		$postipn = 'cmd=_notify-validate'; // Notify validate
		
		foreach ($_POST as $ipnkey => $ipnval) {
		
				if (get_magic_quotes_gpc())
				   $ipnval = stripslashes ($ipnval); // Fix issue with magic quotes
				
				if (!eregi("^[_0-9a-z-]{1,30}$",$ipnkey) || !strcasecmp ($ipnkey, 'cmd')) { 
				    // ^ Antidote to potential variable injection and poisoning
				    unset ($ipnkey); unset ($ipnval); } // Eliminate the above
				    if (@$ipnkey != '') { // Remove empty keys (not values)
				  	   @$_PAYPAL[$ipnkey] = $ipnval; // Assign data to new global array
				       unset ($_POST); // Destroy the original ipn post array, sniff...
			
					   $postipn.='&'.@$ipnkey.'='.urlencode(@$ipnval); }} // Notify string
					   $error=0; // No errors let's hope it's going to stays like this!

                    // IPN validation mode 1: Live Via PayPal Network

					if ($postmode == 1)	{
						$domain = "www.paypal.com"; 
					}

                    // IPN validation mode 2: Test Via EliteWeaver UK

	                elseif ($postmode == 2) {
		                   $domain = "www.eliteweaver.co.uk"; 
		            } 
				   
				    // IPN validation mode 3: Test Via Paypal Sandbox
					
					elseif ($postmode == 3) {
				           $domain = "www.sandbox.paypal.com";
				    }

				   // IPN validation mode was not set to 1 or 2
					
				   else {
					  $error=1;
					  $bmode=1;
				   if ($debugger) debugInfo(); }

@set_time_limit(60); // Attempt to double default time limit incase we switch to Get

// Post back the reconstructed instant payment notification

		$socket = @fsockopen($domain,80,$errno,$errstr,30);
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header.= "User-Agent: PHP/".phpversion()."\r\n";
		$header.= "Referer: ".$_SERVER['HTTP_HOST'].
		$_SERVER['PHP_SELF'].@$_SERVER['QUERY_STRING']."\r\n";
		$header.= "Server: ".$_SERVER['SERVER_SOFTWARE']."\r\n";
		$header.= "Host: ".$domain.":80\r\n";
		$header.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header.= "Content-Length: ".strlen($postipn)."\r\n";
		$header.= "Accept: */*\r\n\r\n";

// Problem: Now is this your firewall or your ports?

            if (!$socket && !$error) {

// Switch to a Get request for a last ditch attempt!

		$getrq=1;

	if (phpversion() >= '4.3.0'	&& function_exists('file_get_contents')) 
	{} // Checking for a new function
	else { // No? We'll create it instead

	   function file_get_contents($ipnget) {
			    $ipnget = @file($ipnget);
	  	        return $ipnget[0];
	   }
	}
	
		$response = @file_get_contents('http://'.$domain.':80/cgi-bin/webscr?'.$postipn);
	
		if (!$response)	{
			$error=1;
			$getrq=0;
	
			if ($debugger) debugInfo(); /* If this is as far as you get then you need a new web host! */ }
	    }



// If no problems have occured then we proceed with the processing

	else {
		@fputs ($socket,$header.$postipn."\r\n\r\n"); // Required on some environments
	while (!feof($socket)) {
		$response = fgets ($socket,1024); }}
		$response = trim ($response); // Also required on some environments

// uncomment '#' to assign posted variables to local variables
#extract($_PAYPAL); // if globals is on they are already local

// and/or >>>

// refer to each ipn variable by reference (recommended)
// $_PAYPAL['receiver_id']; etc... (see: ipnvars.txt)



// IPN was confirmed as both genuine and VERIFIED

	if (!strcmp ($response, "VERIFIED"))  {
	   require("../includes/DB_ConnectionString.php");
	   if (!($InsertQuery = mysql_query("INSERT INTO PayPal(Username,first_name,last_name,payer_business_name,address_street,address_city," .
	         "address_state,address_zip,address_country,address_status,receiver_id,payer_email,payer_id,payer_status,payment_date,payment_type," .
			 "payment_status,pending_reason,reason_code,mc_gross,mc_fee,mc_curreny,mc_handling,mc_shipping,settle_amount,exchange_rate,verify_sign," .
             "notify_version,txn_id) VALUES('" . $_PAYPAL['custom'] . "','" . $_PAYPAL['first_name'] . "','" . $_PAYPAL['last_name'] . "','" . 
			 $_PAYPAL['payer_business_name'] . "','" . $_PAYPAL['address_street'] . "','" . $_PAYPAL['address_city'] . "','" . 
			 $_PAYPAL['address_state'] . "','" . $_PAYPAL['address_zip'] . "','" . $_PAYPAL['address_country'] . "','" . $_PAYPAL['address_status'] . "','" .
			 $_PAYPAL['receiver_id'] . "','" . $_PAYPAL['payer_email'] . "','" . $_PAYPAL['payer_id'] . "','" . $_PAYPAL['payer_status'] . "','" . 
			 $_PAYPAL['payment_date'] . "','" . $_PAYPAL['payment_type'] . "','" . $_PAYPAL['payment_status'] . "','" . $_PAYPAL['pending_reason'] . "','" .
			 $_PAYPAL['reason_code'] . "','" . $_PAYPAL['mc_gross'] . "','" . $_PAYPAL['mc_fee'] . "','" . $_PAYPAL['mc_currency'] . "','" .
			 $_PAYPAL['mc_handling'] . "','" . $_PAYPAL['mc_shipping'] . "','" . $_PAYPAL['settle_amount'] . "','" . $_PAYPAL['exchange_rate'] . "','" .
			 $_PAYPAL['verify_sign'] . "','" . $_PAYPAL['notify_version'] . "','" . $_PAYPAL['txn_id'] . "')"))) {
			 
		   echo "MYSQL INSERT FAILED.";
      }
   }

// Check that the "payment_status" variable is: Completed
// If it is Pending you may want to inform your customer?
// Check your db to ensure this "txn_id" is not a duplicate
// You may want to check "payment_gross" or "mc_gross" matches listed prices?
// You definately want to check the "receiver_email", "receiver_id" or "business" is yours
// Update your db and process this payment accordingly

//***************************************************************//
//* Tip: Use the internal auditing function to do some of this! *//
//* **************************************************************************************//
//* Help: if(variableAudit('mc_gross','0.01') &&					 *//
//* 	     variableAudit('receiver_email','paypal@domain.com') && 			 *//
//* 	     variableAudit('payment_status','Completed')){ $do_this; } else { do_that; } *//
//****************************************************************************************//




// IPN was not validated as genuine and is INVALID
	elseif (!strcmp ($response, "INVALID"))	{
// Check your code for any post back validation problems
// Investigate the fact that this could be a spoofed IPN
// If updating your db, ensure this "txn_id" is not a duplicate
	} else {
	// Just incase something serious should happen!
	}
}

	if ($debugger) debugInfo();
#########################################################
#     Inernal Functions : variableAudit & debugInfo     #
#########################################################
// Function: variableAudit
// Easy LOCAL to IPN variable comparison 
// Returns 1 for match or 0 for mismatch
function variableAudit($v,$c)
{
	global  $_PAYPAL;
    if (!strcasecmp($_PAYPAL[$v],$c)) 
    { return 1; } else { return 0; } 
} 
// Function: debugInfo
// Displays debug info 
// Set $debugger to 1
function debugInfo()
{
	global  $_PAYPAL,
		$postmode,
		$socket,
		$error,
		$postipn,
		$getrq,
		$response;

		$ipnc = strlen($postipn)-21;
		$ipnv = count($_PAYPAL)+1;

	@flush();
	@header('Cache-control: private'."\r\n");
	@header('Content-Type: text/plain'."\r\n");
	@header('Content-Disposition: inline; filename=debug.txt'."\r\n");
	@header('Content-transfer-encoding: ascii'."\r\n");
	@header('Pragma: no-cache'."\r\n");
	@header('Expires: 0'."\r\n\r\n");
	echo '#########################################################'."\r\n";
	echo '#    Copyright © EliteWeaver UK All rights reserved.    #'."\r\n";
	echo '#########################################################'."\r\n";
	echo '#              END USER LICENCE AGREEMENT               #'."\r\n";
	echo '# Redistribution and  use in source and/or binary forms #'."\r\n";
	echo '# with or without  modification, are permitted provided #'."\r\n";
	echo '# that the above copyright notice is  reproduced in the #'."\r\n";
	echo '# script, documentation and/or any other materials that #'."\r\n";
	echo '# may  have been provided in the original distribution. #'."\r\n";
	echo '#########################################################'."\r\n";
	echo '# <-- PayPal IPN Variable Output & Status Debugger! --> #'."\r\n";
	echo '#########################################################'."\r\n\r\n";
	if (phpversion() >= '4.3.0' && $socket)
	{
	echo 'Socket Status: '."\r\n\r\n";
	print_r (socket_get_status($socket));
	echo "\r\n\r\n"; }
	echo 'PayPal IPN: '."\r\n\r\n";
	print_r($_PAYPAL);
	echo "\r\n\r\n".'Validation String: '."\r\n\r\n".wordwrap($postipn, 64, "\r\n", 1);
	echo "\r\n\r\n\r\n".'Validation Info: '."\r\n";
	echo "\r\n\t".'PayPal IPN String Length Incoming => '.$ipnc."\r\n";
	echo "\t".'PayPal IPN String Length Outgoing => '.strlen($postipn)."\r\n";
	echo "\t".'PayPal IPN Variable Count Incoming => ';
	print_r(count($_PAYPAL));
	echo "\r\n\t".'PayPal IPN Variable Count Outgoing => '.$ipnv."\r\n";
	if ($postmode == 1) { 
	    echo "\r\n\t".'IPN Validation Mode => Live -> PayPal, Inc.'; 
	} elseif ($postmode == 2) {
	    echo "\r\n\t".'IPN Validation Mode => Test -> EliteWeaver.'; 
	} elseif ($postmode == 3) {
	    echo "\r\n\t".'IPN Validation Mode => Test -> PayPal Sandbox.'; 
	} else {
	    echo "\r\n\t".'IPN Validation Mode => Incorrect Mode Set!'; 
	}
    echo "\r\n\r\n\t\t".'IPN Validate Response => '.$response;
	if (!$getrq && !$error)
	{
	echo "\r\n\t\t".'IPN Validate Method => POST (success)'."\r\n\r\n"; }
	elseif ($getrq && !$error)
	{
	echo "\r\n\t\t".'IPN Validate Method => GET (success)'."\r\n\r\n"; }
	elseif ($bmode)
	{
	echo "\r\n\t\t".'IPN Validate Method => NONE (stupid)'."\r\n\r\n"; }
	elseif ($error)
	{
	echo "\r\n\t\t".'IPN Validate Method => BOTH (failed)'."\r\n\r\n"; }
	else
	{
	echo "\r\n\t\t".'IPN Validate Method => BOTH (unknown)'."\r\n\r\n"; }
	echo '#########################################################'."\r\n";
	echo '#    THIS SCRIPT IS FREEWARE AND IS NOT FOR RE-SALE!    #'."\r\n";
	echo '#########################################################'."\r\n\r\n";
	@flush();
}
// Terminate the socket connection (if open) and exit
	@fclose ($socket); exit;
?>