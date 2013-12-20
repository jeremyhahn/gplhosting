<?php

$username = "local_apache";
$password = "guests_007";
$hostname = "localhost";
$db_name = "GPL_HOSTING";

if (!($db = mysql_connect($hostname, $username, $password))) {
	or die("Unable to connect to the database server. Please report this problem to support@gplhosting.org.");
	
	
	$selectedDB = mysql_select_db($db_name,$db) 
		or die("Could not find the database on the server.");
?>