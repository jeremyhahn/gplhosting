<?php
$username = "local_apache";
$password = "guests_007";
$hostname = "localhost";
$db_name = "GPL_HOSTING";

if (!($db = mysql_connect($hostname, $username, $password))) {
	print "Unable to connect to the database server.\n";
}
if (!($selectedDB = mysql_select_db($db_name,$db))) {
   print "Could not find the database on the server.";
}

function LogError($MSG) {
  if (!($ErrorLog = fopen("Billing_Errors.log","w"))) {
     print "Could not open error log.";
  }
  fwrite($ErrorLog,$MSG);
  fclose($ErrorLog);
}
// Retrieve billing cycle type (1=monthly,2=yearly)
if (!($MemberQuery = mysql_query("SELECT * FROM Globals WHERE Variable='MemberPlans'"))) {
 LogError ("An error occurred while attempting to perform an SQL select query on the 'Globals' database table.\n");
}
$MemberPlans = mysql_fetch_array($MemberQuery);	
// Retrieve a list of all the clients in the database
if (!($ClientInfo = mysql_query("SELECT * FROM Clients"))) {
   LogError ("An error occurred while attempting to perform an SQL select query on the 'Clients' database table.\n");
}
while ($Client = mysql_fetch_array($ClientInfo)) {

	  $Member = explode("\r\n",$MemberPlans['Value']);
	  foreach ($Member as $Value) { 
		      $ThisMember = explode(",",$Value);
		      if ($ThisMember[0] == $Client['Plan']) {
			     $ThisPlan = $ThisMember[0];
			     $Fee = $ThisMember[12];
			     $BillingType = $ThisMember[13];
		      }
	 }
     # print $Client['Username'] . "," . $Fee . "," . $BillingType . "\n";
     if (!($ServiceQuery = mysql_query("SELECT * FROM BillingInfo WHERE Username='" . $Client['Username'] . "'"))) {
	    LogError ("An error occurred while attempting to perform an SQL select query on the 'BillingInfo' database table.\n");
	 }
	 $BillingInfo = mysql_query($ServiceQuery);
	 print $BillingInfo['Services'] . "\n";
}
?>