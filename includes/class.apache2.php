<?php
# ----------------------------------------------->
# CLASS NAME = PGPL_Apache_2.0
# VERSION    = v1.0 BETA
# COPYRIGHT  = Jeremy Hahn
# ----------------------------------------------->

class PGPL_Apache2 extends PGPL_HOSTING {

  var $CP_Root;
  var $LogRoot;
  var $Conf;
  var $NameVhost;
  var $Reload;
  var $RotateLogPath;
  var $RotateLogArg;
    
    function PGPL_Apache2($Conf="",$NameVhost="",$Reload="",$RotateLogPath="",$RotateLogArg="") {
	         
			 if (strlen($Conf) < 1) {
			    parent::InternalError("There is an invalid file pointer to the Apache configuration file in your Apache settings.");
			    exit();
			 }
			 if (strlen($NameVhost) < 1) {
			    parent::InternalError("There is an invalid Apache configuration for the 'Name Virtual Host' directive in the site configurations.");
			    exit();
			 }
			 if (strlen($Reload) < 1) {
			    parent::InternalError("There is an invalid file pointer to Apache's RNDC utility in your Apache configurations.");
			    exit();
			 }
			 $this->Conf = $Conf;
			 $this->NameVhost = $NameVhost;
			 $this->Reload = $Reload;
			 $this->RotateLogPath = $RotateLogPath;
			 $this->RotateLogArg = $RotateLogArg;			 
			 
	}
	#--------------------------------------------------------------------------------------------------------------------------------->
	function ReadConfigs() {
		
		     if (!$fp = fopen($this->Conf,"r")) {
			    return false;
		     }
		     while (!feof($fp)) {
			    $Buffer = fgets($fp,2048);
			    $FullBuffer .= $Buffer;
		     }
		     fclose($fp);
		     return $FullBuffer;	   
	 }
	 #--------------------------------------------------------------------------------------------------------------------------------->
	 function WriteConfigs($Configs) {
	 	 
	          if (!$fp = fopen($this->Conf,"w")) {
			     parent::InternalError("Could not open the Apache configuration file located at " . $this->Conf);
			     return false;
		      }
		      if (!fwrite($fp,$Configs)) {
		         parent::InternalError("Could not write updated settings to the Apache configuration file.");
			     return false;
		      }
		      fclose($fp);
		      return true;
	}
	#--------------------------------------------------------------------------------------------------------------------------------->
	function FormatVhost($Domain,$NameVhost,$ServerName,$ServerAlias,$DocumentRoot,$ServerAdmin,$CustDirectives,$LogDir) {
	
	         if (!$ServerName) { return false; }
		     $strVirtualHostContainer = "\n# Start $ServerName\n<VirtualHost " . $NameVhost . ">";
		     $strVirtualHostContainer .= "\n\tServerName " . $ServerName;
		     $strVirtualHostContainer .= "\n\tServerAlias " . $ServerAlias;
		     $strVirtualHostContainer .= "\n\tDocumentRoot " . $DocumentRoot;
		     $strVirtualHostContainer .= "\n\tServerAdmin " . $ServerAdmin;
		     $strVirtualHostContainer .= "\n\t" . $CustDirectives;
		     if (strlen($this->RotateLogPath) > 1) {
		        $strVirtualHostContainer .= "\n\tCustomLog \"|" . $this->RotateLogPath . " " . $LogDir . "access_log " . $this->RotateLogArg . "\" combined";
			    $strVirtualHostContainer .= "\n\tErrorLog \"|" . $this->RotateLogPath . " " . $LogDir . "error_log " . $this->RotateLogArg . "\"";
		     } else {
		        $strVirtualHostContainer .= "\n\tCustomLog \"" . $LogDir . "/access_log\" combined";
		        $strVirtualHostContainer .= "\n\tErrorLog \"" . $LogDir . "error_log\"";
		     }
		     $strVirtualHostContainer .= "\n</VirtualHost>\n# End $ServerName"; 			 
	  return $strVirtualHostContainer;
	} 
	// --------------------------------------------------------------------------------------------------------------------------------->
	function AddVhost($ServerName,$FormattedString) {
	
			 if (substr_count($this->ReadConfigs(),$ServerName) > 1) {
				parent::InternalError("$ServerName already exists in the web server configuration file.");
				return false;
			 }	  
			 if (!$CurrentConfigs = $this->ReadConfigs()) {
				parent::InternalError("An error occurred while trying to read in the Apache web server configuraiton file.");
				return false;
			 }
			 $NewConfigs = trim($CurrentConfigs . $FormattedString);
			 if (!is_writable($this->Conf)) {
			    parent::InternalError("There was a permission problem while trying to update the Apache configuration file.");
				return false;
			 }
			 if (!$this->WriteConfigs($NewConfigs)) {
			    parent::InternalError("An error occurred while trying to write the new configurations to the Apache web server configuration file.");
			    return false;
			 } else { 
			    return true; 
			 }		 
	}
	// --------------------------------------------------------------------------------------------------------------------------------->
	function DelVhost($ServerName,$Vhost) {	     
	     
			 if (!$Configs = $this->ReadConfigs()) {
				parent::InternalError("An error occurred while trying to read in the Apache web server configuraiton file.");
				return false;
			 }
			 $Start = strpos($Configs,"\n# Start $ServerName\n<VirtualHost " . $Vhost . ">");	 
			 $End = strpos($Configs,"\n# End $ServerName");			
			 $Length = $End - $Start;
			 if ($Start === false) {
				parent::InternalError("The specified virtual host could not be found within the Apache configuration file.");
				return false;
			 }
			 if ($End === false) {
				parent::InternalError("The specified virtual host could not be found within the Apache configuration file.");
				return false;				
			 } 
			 if (!($NewConfigs = substr_replace($Configs,"",$Start,$Length+(strlen($ServerName)+7)))) {
				parent::InternalError("The Apache configuration file syntax does not acknowledge the requested action.");
				return false;
			 }
			 if (!is_writable($this->Conf)) {
				parent::InternalError("There was a permission problem while trying to write to update Apache configuration file.");
			    return false;
			 }		 
			 if (!$this->WriteConfigs($NewConfigs)) {
				   parent::InternalError("An error occurred while trying to write the new configurations to the Apache web server configuration file.");
				   return false;
			 } else { 
				   return true; 
			 }
	}
	
	// --------------------------------------------------------------------------------------------------------------------------------->
	#########################################################
	###                 General Functions                 ###
	#########################################################
	// --------------------------------------------------------------------------------------------------------------------------------->
	function ApacheGraceful() {
	         
			 passthru("sudo " . $this->Reload,$Result);
			 if ($Result) {
			    parent::InternalError("Could not reload the Apache web server using the command " . $this->Reload . ".");
			    return false;
			 }
			 return true;
    }
	// --------------------------------------------------------------------------------------------------------------------------------->
	function DiskUsage($Dir) {
	        
			 $CmdDiskUsage = exec("du -cb " . $Dir);
	         $DiskUsage = explode("\t",$CmdDiskUsage);
	         return $DiskUsage[0];
	}
}
// --------------------------------------------------------------------------------------------------------------------------------->
$ArrApache = explode(",",$Apache2);
$PGPL_Apache2 = new PGPL_Apache2($ArrApache[2],$ArrApache[3],$ArrApache[4],$ArrApache[5],$ArrApache[6]);
?>