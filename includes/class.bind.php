<?php
# ----------------------------------------------->
# CLASS NAME = PGPL_BIND
# VERSION    = v1.0 BETA
# COPYRIGHT  = Jeremy Hahn
# ----------------------------------------------->

class PGPL_BIND extends PGPL_HOSTING {
	
  var $Conf;
  var $DataDir;
  var $RNDC;
  
	function PGPL_BIND($NAMED_CONF="",$DATA_DIR="",$RNDC="") {
	         
			 if (strlen($NAMED_CONF) < 1) {
			    return "<b>INTERNAL ERROR:</b><br>There is an invalid file pointer to the BIND configuration file in your BIND settings. Please check your settings.";
			    exit();
			 }
			 if (strlen($DATA_DIR) < 1) {
			    return "<b>INTERNAL ERROR:</b><br>There is an invalid file pointer to your BIND data directory in your BIND configurations. Please check your settings.";
			    exit();
			 }
			 if (strlen($RNDC) < 1) {
			    return "<b>INTERNAL ERROR:</b><br>There is an invalid file pointer to your BIND DNS servers RNDC utility in your BIND configurations. Please check your settings.";
			    exit();
			 }
			 $this->Conf = $NAMED_CONF;
			 $this->DataDir = $DATA_DIR;
			 $this->RNDC = $RNDC;	
			 // ------------------------->
			 //  GLOBAL SOA RECORD VALUES
			 // ------------------------->
			 $this->REFRESH = 3600;
			 $this->RETRY = 3600;
             $this->EXPIRE = 604800;
			 $this->TTL = 60; // Something friendly for newcomer dynamic DNS users
			 $this->PS = "ns1.gplhosting.org.";
			 $this->RP = "webmaster.gplhosting.org.";
			 $this->NS1 = "ns1.gplhosting.org.";
			 $this->NS2 = "ns2.gplhosting.org.";

	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function ReadConfigs() {
	
			 if (!($ConfigFile = fopen($this->Conf,"r"))) {
			    parent::InternalError("An error occurred while attempting to open the BIND configuration file. Verify the existance of your configured setting of '$NamedConf', and that your permissions are correctly configured.");
			    return false;			   
			 }
			 while (!feof($ConfigFile)) {
			   $ThisEntry = fgets($ConfigFile);
			   $Configs .= $ThisEntry;
			 }
			 fclose($ConfigFile);
			 return $Configs;
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function WriteConfigs($Configs) {
	
			 if (!($ConfigFile = fopen($this->Conf,"w"))) {
			    parent::InternalError("An error occurred while attempting to open the BIND configuration file. Verify the existance of your configured setting of '$NamedConf', and that your permissions are correctly configured.");
			    return false;
			 }
			 if (!fwrite($ConfigFile,trim($Configs) . "\n")) {
					fclose($NewConfigFile);
					parent::InternalError("An error occurred while attempting to write the new zone to the BIND configuration file. There may be a permission problem.");
			        return false;
			 }
			 fclose($ConfigFile);
			 return true;
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function WriteMasterZone($Zone) {
	
			 $strZone = "\nzone \"" . $Zone . "\" {\n\ttype master;\n\tfile  \"" . $Zone . ".zone\";\n}; // End $Zone";
			 $Configs = $this->ReadConfigs();
			 $Start = strpos($Configs,"zone \"$Zone\"");
			 $End = strpos($Configs,"// End $Zone");	
			 if ($Start === false) {			 
				if (!is_writable($this->Conf)) {
				   parent::InternalError("An error occurred while attempting to open the BIND configuration file. Verify the existance of your configured setting of '$NamedConf', and that your permissions are correctly configured.");
			       return false;
				}
				if (!$this->WriteConfigs(trim($Configs . $strZone))) {
				   return false;
				}
				return true;				  
			 } else {
			   parent::InternalError("An error occurred while attempting to add $Zone to the BIND configuration file. $Zone already exists!");
			   return false;
			 }
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function WriteSlaveZone($Zone,$ArrMasterIP) {
	        
			 $MasterIPs = str_replace(",","; ",$ArrMasterIP);
	         $strZone = "\nzone \"" . $Zone . "\" {\n\ttype slave;\n\tfile  \"" . $Zone . ".zone\";\n\tmasters { " . $MasterIPs . 
			           " };\n}; // End $Zone";		
		     $Configs = $this->ReadConfigs();			
			 $Start = strpos($Configs,"zone \"$Zone\"");
			 $End = strpos($Configs,"// End $Zone");
			 if ($Start === false) {
				 if (!is_writable($this->Conf)) {
					parent::InternalError("An error occurred while attempting to write to the BIND configuration file. The file is not writable.");
				    return false;
				 }
				 if (!$this->WriteConfigs(trim($Configs . $strZone))) {
				    return false;		
			     }
				 return true;
			 } else {
			   parent::InternalError("An error occurred while attempting to write to the BIND configuration file. $Zone already exists!");
			   return false;
			 }
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function DeleteZone($Zone) {
	
			 if (!$Configs = $this->ReadConfigs()) {
			    parent::InternalError("An error occurred while attempting to read in the BIND configuration file.");
				return false;
			 }
			 $Start = strpos($Configs,"\nzone \"$Zone\"");	 
			 $End = strpos($Configs,"}; // End $Zone");			
			 $Length = $End - $Start;
			 if ($Start === false) {
				parent::InternalError("An error occurred while attempting to delete the zone $Zone. The specified zone could not be found within the BIND configuration file.");
				return false;
			 }
			 if ($End === false) {
				parent::InternalError("An error occurred while attempting to delete the zone $Zone. The specified zone could not be found within the BIND configuration file.");
				return false;
			 } 
			 if (!($NewConfigs = substr_replace($Configs,"",$Start,$Length+(strlen($Zone)+10)))) {
				parent::InternalError("An error occurred while attempting to delete the zone $Zone. No file marker found.");
				return false;
			 }
			 if (!is_writable($this->Conf)) {
			    parent::InternalError("An error occurred while attempting to write to the BIND configuration file. The file is not writable.");
				return false;
			 }
			 if (!$this->WriteConfigs($NewConfigs)) {
				return false;		
			 }
		     return true;	
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function RebuildZone($ZoneID) {
	
			 if (!($SOA_RECORD = mysql_query("SELECT * FROM DNS_Zones WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $ZoneID . "'"))) {
			    echo "An error occurred while attempting to rebuild the requested zone.<br><b>MySQL says:</b><br>" . mysql_error();
			    parent::InternalError("An error occurred while attempting to rebuild the requested zone.");
				return false;
			 }
			 $DB_SOA = mysql_fetch_array($SOA_RECORD);
			 if (!($SOA_Update = mysql_query("UPDATE DNS_Zones Set Serial='" . ($DB_SOA['Serial'] + 1) . "' WHERE Username='" . 
					$_SESSION['Username'] . "' AND ZoneID='" . $ZoneID . "'"))) {
				 echo "Could not increment SOA serial!<br><b>MySQL Said:</b><br>" . mysql_error();
				 parent::InternalError("An error occurred while attempting to increment the SOA serial number for the updated zone.");
				 return false;
			 }
			 if (file_exists($this->DataDir . $DB_SOA['Zone'] . ".zone")) {
			    if (!$this->DeleteZoneFile($DB_SOA['Zone'])) {
			        return false;
				}
			 }
			 $SOA = $this->FormatSOA($DB_SOA['PS'],$DB_SOA['RP'],$DB_SOA['Serial']+1,$DB_SOA['Refresh'],$DB_SOA['Retry'],$DB_SOA['Expire'],$DB_SOA['TTL']);
			 // Get NS Records first
			 if (!($NSRecords = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $ZoneID . "' AND RecType='NS' ORDER BY Hostname"))) {
				echo "An error occurred while attempting select query on DNS_Records.<br><br><b>MySQL says:</b><br>" . mysql_error();
			    parent::InternalError("An error occurred while attempting get NS resource records for a zone rebuild.");
				return false;
			 }
			 if (!($ZoneRecords = mysql_query("SELECT * FROM DNS_Records WHERE Username='" . $_SESSION['Username'] . "' AND ZoneID='" . $ZoneID . "' AND RecType!='NS' ORDER BY RecType"))) {
				echo "An error occurred while attempting select query on DNS_Records.<br><br><b>MySQL says:</b><br>" . mysql_error();
			    parent::InternalError("An error occurred while attempting get general resource records for a zone rebuild.");
				return false;
			 } 					
			 // Format this zones resource NS records.
			 while ($DB_NS = mysql_fetch_array($NSRecords)) {
				$This_NS = $this->Format_RR($DB_NS['Hostname'],$DB_NS['RecType'],$DB_NS['Alias'],$DB_NS['RecData'],$DB_NS['MX_Pref']);
				$RRs_2_Write .= $This_NS;							 
			 }
			 // Format this zones resource records (RR) which were retrieved from the database.
			 while ($DB_RR = mysql_fetch_array($ZoneRecords)) {
				$This_RR = $this->Format_RR($DB_RR['Hostname'],$DB_RR['RecType'],$DB_RR['Alias'],$DB_RR['RecData'],$DB_RR['MX_Pref']);
				$RRs_2_Write .= $This_RR;							 
			 }
				$ZoneFile = $SOA . $RRs_2_Write . "\n";	
			 // Write the new zone file to the data directory.				
			 if (!$this->WriteZoneFile($ZoneFile,$DB_SOA['Zone'])) { 
				parent::InternalError("An error occurred while attempting to rebuild the requested zone. Failed to write new zone file after zone rebuild.");
				return false;
			 }						
			 // Reload the DNS server so that the new zone will load.
			 if (!$this->Reload()) {
			    parent::InternalError("An error occurred while attempting to reload the BIND DNS server.");
				return false;
			 }			
			 return true;
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function WriteZoneFile($ZoneFile,$Zone) {  
	
			 if (!$txtZoneFile = fopen($this->DataDir . $Zone . ".zone", 'w')) {
			    parent::InternalError("An error occurred while attempting to open the zone file located at " . $this->DataDir . $Zone . ".zone.");
				return false;
			 }
			 if (!fwrite($txtZoneFile, $ZoneFile)) {
				fclose($txtZoneFile);
				parent::InternalError("An error occurred while attempting to write to new zone file located at " . $this->DataDir . $Zone . ".zone.");
				return false;
				return "Could not write zone file to " . $this->DataDir;
			 }
			 fclose($txtZoneFile);
			 return true;					
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function DeleteZoneFile($Zone) {
	
	        if (file_exists($this->DataDir . $Zone . ".zone")) { 
			    unlink($this->DataDir . $Zone . ".zone");
				return true;
			} else {
			   parent::InternalError("An error occurred while attempting to delete the zone file for " . $Zone . ". The zone file does not exist!");
			   return false;
			}
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function Reload() {
		
	         passthru("sudo " . $this->RNDC . " reload",$Result);
			 if ($Result) {
			    parent::InternalError("An error occurred while attempting to reload the BIND DNS server.");
			    return false;
			 }
			 return true;
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function Format_RR($Hostname,$RecType,$Alias,$RecData,$MX_Pref) {
	
			 switch($RecType) {
			  ## NS Records Need to be written first!
			  case "NS":
			  $This_RR = "\n\t\tIN\t" . $RecType . "\t" . $RecData; 
			  break;
			  
			  case "A":
			  $This_RR = "\n" . $Hostname . "\tIN\t" . $RecType . "\t" . $RecData;  
			  break;
			  
			  case "MX":
			  $This_RR = "\n" . $Hostname . "\tIN\t" . $RecType . "\t" . $MX_Pref . "\t" . $RecData;
			  break;
			  
			  case "CNAME":
			  $This_RR = "\n" . $Alias . "\tIN\t" . $RecType . "\t" . $Hostname;  
			  break;
			 }				
		  return $This_RR;
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function FormatSOA($PS,$RP,$Serial,$Refresh,$Retry,$Expire,$TTL) {
	
			 $SOA = "\$TTL " . $TTL . "\n@\tIN\tSOA\t" . $PS . "\t" . $RP . "\t(\n\t\t\t";
			 $SOA .= $Serial . " ; Serial\n\t\t\t";
			 $SOA .= $Refresh . " ; Refresh\n\t\t\t";
			 $SOA .= $Retry . " ; Retry\n\t\t\t";
			 $SOA .= $Expire . " ; Serial\n\t\t\t";
			 $SOA .= $TTL . " ; TTL\n\t\t\t";
			 $SOA .= ")\n\n\n";
		  return $SOA;
	}	 			 
} # END BIND CLASS
// ------------------------------------------------------------------------------------------------------------------------------------->
$ArrBIND = explode(",",$BIND);
$NAMED_CONF = $ArrBIND[0]; 
$NAMED_DATA_DIR = $ArrBIND[1];
$RNDC = $ArrBIND[2];

$PGPL_BIND = new PGPL_BIND($NAMED_CONF,$NAMED_DATA_DIR,$RNDC);
?>