<?php
# ----------------------------------------------->
# CLASS NAME = PGPL_Main
# VERSION    = v1.0 BETA
# COPYRIGHT  = Jeremy Hahn
# ----------------------------------------------->

class PGPL_HOSTING {
   
   # Global variables needed by the Project GPL Hosting control panel.
   var $SudoPass;
   /*
     var $WAN;
     var $HomeServer;
     var $CoLoServers;
     var $UseSSL;
     var $MemberPlans;
   */

   function PGPL_HOSTING($SudoPwd="") {
             
		    /*
			 # Check to make sure all values which are needed are configured!
			 if (strlen($WAN) < 4) {
				echo "<b>INTERNAL ERROR:</b><br>There is no WAN IP address specified in the main class file.";
				exit();
			 }			 
			 if (strlen($HomeServer) < 1) {
				echo "<b>INTERNAL ERROR:</b><br>There is no home server specified in the main class file.";
				exit();
			 }
			 if (strlen($CoLoServers) < 1) {
				echo "<b>INTERNAL ERROR:</b><br>There is no home server specified in the main class file.";
				exit();
			 }
			 if (strlen($UseSSL) < 1) {
				$UseSSL = 0;
			 }
			 if (count($MemberPlans) < 1) {
				$MemberPlans = "Site Admin,0,1,0,20480,0,0,0,0,0,0,0,0.00\r\n";
				$MemberPlans .= "Guest,5,1,5,2048,5,5,5,5,5,10485760,104857600,10.00";
			 }		
		   */
		   if (strlen($SudoPwd) < 1) {
		  	  echo "<b>INTERNAL ERROR:</b><br>There is no sudo password specified in your site configurations." .
				   "<br><br><b>Please see the <a href=\"Admin\SampleSudo.php\">sample</a> SUDO file for details on the required configurations.";
			  exit();
		   }
		   $this->SudoPass = base64_decode($SudoPwd);				
	}
	// ------------------------------------------------------------------------------------------------------------------------------->
	function SudoLogin() {
	
			 passthru("echo " . $this->SudoPass . " | sudo -S -v",$Result);
			 if ($Result) {
				$this->InternalError("The control panel was not able to update the web servers SUDO timestamp.");
				exit();
			 }
	}
	// ------------------------------------------------------------------------------------------------------------------------------->
	function SudoLogout() {
	
	         system("sudo -k");
	}
	// ------------------------------------------------------------------------------------------------------------------------------->	
	function InternalError($msg) {
	         
			 echo "<script language=\"JavaScript\">alert('INTERNAL ERROR:\\r\\n" . $msg . "\\r\\n\\r\\nPlease contact your system administrator.');</script>";
	}
	// ------------------------------------------------------------------------------------------------------------------------------->	
	function LogError($Msg,$Type,$Mode) {
	         
			 switch ($Mode) {
			 
			         case "DB":
					      $CleanString = mysql_escape_string($Msg);
						  if (!($LogInsert = mysql_query("INSERT INTO Logs(Msg,Type) VALUES('" . $CleanString . "','" . $Type . "')"))) {
			     
				               $this->LogError("Failed to log the following message to the database. MySQL Said:\n" . mysql_error() . 
							                   "\nThe original message was: " . $Msg, $Type, "TXT");
			              }
					 break;
					 
					 case "TXT":
                         
						  if (!($ErrorLog = fopen("../Logs/" . $Type . "/" . date("m-d-Y") . ".log","a+"))) {
						     echo "An error occured which should be logged, however, the log file could not be opened for writing.";
						  }
						  if (fwrite($ErrorLog,"---- " . date("F j, Y, H:i:s a") . " -----\n" . $Msg . "\n") === false) {
						      echo "An error occured which should be logged, however, the system could not write the following message to the log file.<br><br><b>" .
							        $Msg . "</b>.";
						  }
						  fclose($ErrorLog);
					 
					 break;
			 }
			 
			 
	}
	// ------------------------------------------------------------------------------------------------------------------------------->
}
require("GlobalConfigs.php");
$PGPL = new PGPL_HOSTING($SudoPassword);
$PGPL->SudoLogin();
# END PGPL_HOSTING CLASS
?>