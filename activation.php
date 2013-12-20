<?php
require("includes/DB_ConnectionString.php");
require("includes/GlobalConfigs.php");

   if (!($PendingQuery = mysql_query("SELECT * FROM Pending_Activations WHERE UniqueID='" . $_GET['GUID'] . "'"))) {
     $Result = "Could not execute SQL select statement.<br><b>MySQL Said:</b><br>" . mysql_error();
   } else {
     $Record = mysql_fetch_array($PendingQuery);
	 
	    $Username = $Record['Username'];		
		$Email = $Record['Email'];
		$Password = $Record['Password'];
		$SecretPhrase = $Record['SecretPhrase'];
		$SignupIP = $Record['IP'];
		
		
		if (!($InsertQuery = mysql_query("INSERT INTO Clients(Username,Email,Plan,HomeServer,Created,Password,SecretPhrase,SignupIP) VALUES('" . 
		                                 $Username . "','" . $Email . "','Registered User','" . $HomeServer . "','" . date("Y-m-d") . "','" . 
										 $Password . "','" . $SecretPhrase . "','" . $_SERVER['REMOTE_ADDR'] . "')"))) {
		  $Result = "Could not execute SQL insert statement for new client.<br><b>MySQL Said:</b><br>" . mysql_error();
		} else {
		  mysql_query("DELETE FROM Pending_Activations WHERE UniqueID='" . $_GET['GUID'] . "'");
		  echo "<script language=\"JavaScript\">alert('Welcome to GPL Hosting, " . $Username . ". Your account has been successfully activated. Please log in now to access your account!');location.href = 'manage.php'</script>";
		}
   }
   echo $Result;
?>