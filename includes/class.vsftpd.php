<?php
# ----------------------------------------------->
# CLASS NAME = PGPL_VSFTPD
# VERSION    = v1.0 BETA
# COPYRIGHT  = Jeremy Hahn
# ----------------------------------------------->
// IMPORTANT SECURITY TIP:
// -- VSFPTD class uses real system accounts to create a chroot() jail for FTP users (if configured to do so) --
// If SSH is enabled on this machine, be sure to add 'AllowUsers your_username' directive to sshd_config
// To guarantee that FTP users CAN NOT gain remote access to the file system using their FTP accounts!
// ALSO: You may want to set the UMASK to something like 003 on the FTP server so that users dont run into permission
//       problems with other users in their group, and at the same time, you can have some type of security by giving 
//       world 'READ-ONLY' access by default.
// ------------------------------------------------------------------------------------------------------------------>

class PGPL_VSFTPD extends PGPL_HOSTING {

  var $Username;
  var $Password;
  var $HomeDir;
       
      function PGPL_VSFTPD() {	  
	  }
  
	  function CreateUser($Vars) {
			 
			   if ($Vars == "0") {
			      passthru("sudo useradd " . $this->Username . " -d " . $this->HomeDir,$Result);
				  if ($Result) {
			   	      parent::InternalError("An error occurred while attempting to create " . $this->Username . " a new FTP account.");
					  return false;
			      }
			      return true; 
			   } 			  
			   if ($Vars == "1") {
			      if (!$this->GetGroupID()) {
				     passthru("sudo useradd " . $this->Username . " -d " . $this->HomeDir,$Result);
					 if ($Result) {
					    parent::InternalError("An error occurred while attempting to create " . $this->Username . " a new FTP account.");
					    return false;
				     }	
					 if ($this->ChangePasswd()) {
					   return true;
					 }
				  } else { 
				     $this->CreateUser(2); 
				  }
			   } 			  
			   if ($Vars == "2") { 
			      if (!$GID = $this->GetGroupID()) {
				     parent::InternalError("An error occurred while attempting to retrieve the local system group ID for user " . $_SESSION['Username']);
					 return false;
			      } else {
					 passthru("sudo useradd " . $this->Username . " -d " . $this->HomeDir . " -g " . $GID,$Result);
					 if ($Result) {
					    parent::InternalError("An error occurred while attempting to create " . $this->Username . " a new FTP account.");
					    return false;
					 }
					 if ($this->ChangePasswd()) {
					    return true;
				     }
			     }   
			  }	  
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
		function DeleteUser($Vars) {	
		
				 if ($Vars == 0) {
					passthru("sudo userdel " . $this->Username,$Result);
					if ($Result) {
					   parent::InternalError("An error occurred while attempting to remove the local system account for user " . $this->Username . ".");
					   return false;
					}	
					return true;
				 }
				 if ($Vars == 1) {
					if (!($this->RemoveHomeDir($this->HomeDir))) {
					   parent::InternalError("An error occurred while attempting to remove the home directory for " . $this->Username . " located at $Dir.");
					   return false;
					} else { 
					  passthru("sudo userdel " . $this->Username,$Result);
					  if ($Result) {
						  parent::InternalError("An error occurred while attempting to remove the local system account for user " . $this->Username . ".");
					      return false;
					  }
					  return true;
				    }
				 }
				 if ($Vars == "2") {
					passthru("sudo userdel " . $this->Username,$Result);
					if ($Result) {
					   parent::InternalError("An error occurred while attempting to remove the local system account for user " . $this->Username . ".");
					   return false;
					}
					if ($this->GetGroupID() > 0) {
						passthru("sudo groupdel " . $this->GetGroupID(),$Result);
						if ($Result) {
						   parent::InternalError("An error occurred while attempting to remove the local group account for user " . $this->Username . ".");
						   return false;
						}
						return true;
 				   } else {
				        return true;
				   }
				}
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
		function RemoveBashShell() {
		
			     if (file_exists($this->HomeDir . "/.bash_history")) { unlink($this->HomeDir . "/.bash_history"); }
			     if (file_exists($this->HomeDir . "/.bash_logout")) { unlink($this->HomeDir . "/.bash_logout"); }
			     if (file_exists($this->HomeDir . "/.bash_profile")) { unlink($this->HomeDir . "/.bash_profile"); }
			     if (file_exists($this->HomeDir . "/.bashrc")) { unlink($this->HomeDir . "/.bashrc"); }
			     if (file_exists($this->HomeDir . "/.gtkrc")) { unlink($this->HomeDir . "/.gtkrc"); }
			     if (file_exists($this->HomeDir . "/.kde/Autostart/Autorun.desktop")) { unlink($this->HomeDir . "/.kde/Autostart/Autorun.desktop"); }
			     if (file_exists($this->HomeDir . "/.kde/Autostart/.directory")) { unlink($this->HomeDir . "/.kde/Autostart/.directory"); }
			     if (is_dir($this->HomeDir . "/.kde/Autostart")) { rmdir($this->HomeDir . "/.kde/Autostart"); }
			     if (is_dir($this->HomeDir . "/.kde")) { rmdir($this->HomeDir . "/.kde"); }
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
		function RemoveHomeDir($Dir) {
		
		         passthru("sudo chmod -R 777 " . $Dir,$Result);
				 if ($Result) {
				     parent::InternalError("An error occurred while attempting to set permissions on " . $this->Username . "'s home directory located at " . $this->HomeDir . ".");
					 return false;
				 }
		         $this->RemoveBashShell();				 
		         if (is_dir($Dir)) {
		            $Dir_Handle = opendir($Dir); 
			            while (false !== ($file = readdir($Dir_Handle))) { 
				              if ($file != "." && $file != "..") { 
				                 if (!is_dir($Dir . "/" . $file)) {
								    unlink($Dir . "/" . $file);
								 } else { 
								    $this->RemoveHomeDir($Dir . "/" . $file); 
				                 }
							  }  
			            } 
		            closedir($Dir_Handle); 
		            rmdir($Dir); 
		         return true;
		         } 
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
		function RemoveDirs($Dir) {
		
			    passthru("sudo chmod -R 777 " . $Dir,$Result);
				if ($Result) {
				   parent::InternalError("An error occurred while attempting to set permissions on the home directory located at " . $this->HomeDir . ".");
				   return false;
				}
			    if (is_dir($Dir)) {
			       $Dir_Handle = opendir($Dir); 
				      while (false !== ($file = readdir($Dir_Handle))) { 
					        if ($file != "." && $file != "..") { 
					           if (!is_dir($Dir . "/" . $file)) {
								    unlink($Dir . "/" . $file);
								 } else { 
								    $this->RemoveDirs($Dir . "/" . $file); 
				                 }
					       } 
				      } 
			    closedir($Dir_Handle);
			    rmdir($Dir);
			    return true;
			   }
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
		function CopyContents($Location) {
		
			     if (is_dir($this->HomeDir)) {
				    $Dir_Handle = opendir($this->HomeDir); 
					    while ($file = readdir($Dir_Handle)) { 
						     if ($file != "." && $file != "..") { 
							    if (!is_dir($this->HomeDir . "/" . $file)) {
								   copy($this->HomeDir . "/" . $file,$Location . "/" . $file); 
								   chmod($Location . "/" . $file, 0777);         
							    } else { 
								   mkdir($Location . "/" . $file);
								   chmod($Location . "/" . $file, 0777);
								   $this->HomeDir = $this->HomeDir . "/" . $file;
								   CopyContents($Location . "/" . $file);          
							    }
						      } 
					   } 
				     closedir($Dir_Handle); 
				     return true;
			      }
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
		function ChangePasswd() {
		
				if (!(touch("New_PGPL_FTP_Password_" . $this->Username))) {
				   parent::InternalError("An error occurred while attempting to create a new password file for user " . $this->Username . ".");
				   return false;
				}
				if (!(chmod("New_PGPL_FTP_Password_" . $this->Username, 0777))) {
				   parent::InternalError("An error occurred while attempting to change the permissions on new password file for user " . $this->Username . ".");
				   return false;
				}	     
				if (is_writable("New_PGPL_FTP_Password_" . $this->Username)) {
				   if (!($PwdFile = fopen("New_PGPL_FTP_Password_" . $this->Username,"w"))) {
					  parent::InternalError("An error occurred while attempting to open new password file for user " . $this->Username . ".");
				      return false;
				   }			  
				   if (!fwrite($PwdFile,trim($this->Username . ":" . $this->Password) . "\n")) {
					  parent::InternalError("An error occurred while attempting to write criteria for new password file for user " . $this->Username . ".");
				      return false;
				   } else {	 	     
					  fclose($PwdFile);		 
					  passthru("sudo chpasswd < New_PGPL_FTP_Password_" . $this->Username,$Result);
					  if ($Result) {
					     parent::InternalError("An error occurred while attempting to change the permissions on new password file for user " . $this->Username . ".");
				         return false;
					 }	 
					 if (!unlink("New_PGPL_FTP_Password_" . $this->Username)) {
						parent::InternalError("An error occurred while attempting to delete your temporary password file for user " . $this->Username . " which was used to reset your password. THIS IS A HUGE SECURITY HOLE. Please report this error ASAP!");
				        return false;
					 }
					 return true;
				    }
				  } else {
				     parent::InternalError("An error occurred while attempting to write criteria to new password file for user " . $this->Username . ". The file is not writable.");
				     return false;
				  }
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
		function CreateDirectory($Perms) {
		
				 if (is_dir($this->HomeDir)) {
				    parent::InternalError("An error occurred while attempting to create a new directory located at " . $this->HomeDir . " for user " . $this->Username . ". The directory already exists.");
				    return false;
				 } 
				 if (!mkdir($this->HomeDir)) {
				    parent::InternalError("An error occurred while attempting to create a new directory located at " . $this->HomeDir . " for user " . $this->Username . ".");
				    return false; 
				 }
				 switch($Perms) {
				 
				  case "777":	  
					   if (!chmod($this->HomeDir, 0777)) { 
					      parent::InternalError("An error occurred while attempting to modify permissions for new directory located at " . $this->HomeDir . " for user " . $this->Username . ".");
				          return false;
					   }
					   return true;
				  break;
				  
				  case "744":
					   if (!chmod($this->HomeDir, 0744)) {
					      parent::InternalError("An error occurred while attempting to modify permissions for new directory located at " . $this->HomeDir . " for user " . $this->Username . ".");
				          return false; 
					   }
					   return true;
				  break;
					   
				  case "776":
				  
					  if (!chmod($this->HomeDir, 0776)) { 
						 parent::InternalError("An error occurred while attempting to modify permissions for new directory located at " . $this->HomeDir . " for user " . $this->Username . ".");
				         return false;
					  }
					  return true;
				  break;  
				  
				  
				 }
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
		function GetGroupID() {
		
			    $Result = shell_exec("cat /etc/group | grep " . $_SESSION['Username']);  
			    $UserEntry = explode(":",$Result);
			    if (!$UserEntry) {
			       return false;
			    } else {
			       return $UserEntry[2];
			    }
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
		function LocalSystemAccount($Username) {
		
			    $Result = shell_exec("cat /etc/passwd | grep $Username");  
			    $UserEntry = explode(":",$Result);
			    if (!$UserEntry) {
			       return false;
			    } else {
				   if (substr($UserEntry[0],0,strlen($UserEntry[0])) == $Username) {
			          return $UserEntry[0];
				   } else {
				      return false;
				   }
			   }
		}
		// ----------------------------------------------------------------------------------------------------------------------------->
}
$PGPL_FTP = new PGPL_VSFTPD();
?>