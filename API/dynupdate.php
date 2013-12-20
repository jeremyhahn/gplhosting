<?php
session_start();
require("../includes/DB_ConnectionString.php");
require("../includes/class.pgpl.php");
require("../includes/class.bind.php");
/*
DYNAMIC UPDATE RESPONSE CODES:
------------------------------
+200 = Successful Update
+201 = IP Current
------------------------------
-101 = Bad QueryString Syntax
-102 = Invalid Hostname
-103 = Invalid Domain
-403 = Invalid Credentials
-666 = Unexpected Error :-/
*/
//function VerifyIP($
// Variables needed for the entire script
$Username = strtolower($_GET['Username']);
$_SESSION['Username'] = $Username;
$Password = $_GET['Password'];
$Hostname = $_GET['Hostname'];
$Domain = $_GET['Domain'];
$FQDN = $Hostname . "." . $Domain;
// Functions  
function LogStat($ThisUser,$Response,$funcFQDN) {
 mysql_query("INSERT INTO DynDNS_Stats(Username,Date,FromIP,Response,FQDN,UniqueID) VALUES('" . $ThisUser . "','" . date("l F j, Y @ g:i a T") .
             "','" . $_SERVER['REMOTE_ADDR'] . "','" . mysql_escape_string($Response) . "','" . $funcFQDN . "','" . md5($ThisUser . mktime()) . "')");
}
function DynResult($Response) {
   session_unset();
   session_destroy();
  echo $Response;
}
// VALIDATION FIRST!
if (!(isset($Username) || $Username === "")) {
 LogStat($Username,"-101 Bad QueryString Syntax.",$FQDN);
 DynResult("-101");
 exit();
}
if (!(isset($Password) || $Password === "")) {
 LogStat($Username,"-101 Bad QueryString Syntax.",$FQDN);
 DynResult("-101");
 exit();
}
if (!(isset($Domain) || $Domain === "")) {
 LogStat($Username,"-101 Bad QueryString Syntax.",$FQDN);
 DynResult("-101");
 exit();
}
if (!(isset($Hostname) || $Hostname === "")) {
 LogStat($Username,"-101 Bad QueryString Syntax.",$FQDN);
 DynResult("-101");
 exit();
}
// Authenticate the user
 $UserAuth =  mysql_query("SELECT * FROM Clients WHERE Username='" . $Username . "' AND Password='" . $Password . "'");
   if (mysql_num_rows($UserAuth) == 0) {
    LogStat($Username,"INVALID CREDENTIALS",$FQDN);
	DynResult("-403");
	exit();
   }     
// Get this zone's zone id from the database
if (!($ZoneQuery = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $Username . "' AND Zone='" . $Domain . "'"))) {
LogStat($Username,"Update failed... MySQL Said: " . mysql_error(),$FQDN);
exit();
} else {
$ThisZone = mysql_fetch_array($ZoneQuery);
$ZoneID = $ThisZone['ZoneID'];
}
// Check to make sure that the domain exists
if (!($DomainChk = mysql_query("Select * FROM DNS_Zones WHERE Username='" . $Username . "' AND Zone='" . $Domain . "' AND ZoneID='" . $ZoneID . "'"))) {
 LogStat($Username,"Update Failed... MySQL Said: " . mysql_error(),$FQDN);
 exit();
} else {
  if (mysql_num_rows($DomainChk) == 0) {
   LogStat($Username,"Invalid Domain",$FQDN);
   DynResult("-103");
   exit();
  }
}
// Check to make sure that the hostname exists
if (!($HostChk = mysql_query("Select * FROM DNS_Records WHERE Username='" . $Username . "' AND Hostname='" . $Hostname . "' AND ZoneID='" . $ZoneID . "'"))) {
 LogStat($Username,"Update Failed... MySQL Said: " . mysql_error(),$FQDN);
 exit();
} else {
  if (mysql_num_rows($HostChk) == 0) {
   LogStat($Username,"Invalid Hostname",$FQDN);
   DynResult("-102");
   exit();
  }
}
// Check to make sure that the IP is not already up-to-date
$chkIP = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $Username . "' AND Hostname='" . $Hostname . 
                     "' AND ZoneID='" . $ZoneID . "' AND RecType='A' AND RecData='" . $_GET['IP'] . "'");
if (mysql_num_rows($chkIP) > 0) {
LogStat($Username,"IP Current",$FQDN);
DynResult("+201");
exit();
}
// Update the IP address
if (!($UpdateHost = mysql_query("Update DNS_Records Set RecData='" . $_GET['IP'] . "' WHERE Username='" . 
	 $Username . "' AND Hostname='" . $Hostname . "' AND ZoneID='" . $ZoneID . "' AND RecType='A'"))) {
     
	 LogStat($Username,"Update failed... MySQL Said: " . mysql_error(),$FQDN);
	 exit();
} else {
 $PGPL->SudoLogin();
 $PGPL_BIND->RebuildZone($ZoneID);
 $PGPL->SudoLogout();
 LogStat($Username,"IP successfully updated to: " . $_GET['IP'],$FQDN);
 $SuccessfulUpdate = 1;
 DynResult("+200");
 exit();
}	
// We should never get here
LogStat($Username,"-666 An unexpected error occurred.",$FQDN); 
exit();
?>