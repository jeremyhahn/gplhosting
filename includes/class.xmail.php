<?php
require("GlobalConfigs.php");
#
# XMailCTRL Class 1.08
# (GPL)2004 Harald Schneider
#
# XMail CTRL Object, based on class.xmail.php
# by Paul Heijman/SOGware
#
# Returned lists are formatted as an array of arrays
# with fieldnames prepended:
#
# $data[0]->record[0][1][2] ... // field names
# $data[1]->record[0][1][2] ... // 1st record
# $data[2]->record[0][1][2] ... // 2nd record
# ...
#
# File contents are returned raw.
#
# Missed something? Just drop me a mail:
# h_schneider@marketmix.com
#
# TODO:
# - Implement CTRL changes of XMail >=1.19
#
# CHANGES:
#
# 1.08
#	- Fixed a bug which affected all methods with argument lists,
#   e.g. custdomset()
# 1.07
#	- Added test_ascii()
# 1.06
# - Modified cfgfileset() to process a string instead
#   of an array.
# - Added psynctrigger()
# - added auto_reconnect, renamed pwd_cleartext to pwd_is_cleartext,
#   changed pwd_md5_encoded from boolean type to string (it now
#   holds the encoded pwd). This was necessary to handle relogins.
# - ctrlcmd() handles lost connections now:
#   E.g. when trying to process german Umlaut characters,
#   XMail drops the CTRL connection. ctrlcmd returns FALSE and does
#   a relogin by default. Thx to Achim Schmidt!
# 1.05
# - login() uses MD5 secured authentication now!
# - Added keepalive() as an alias of noop()
# - Added all missing fieldname headers
# - Added quit(), etrn(), filelist()
# - Removed custdomset_org()
# 1.04
# - Moved pwd decoding to login(). This allows to create
#   a barebone instance, setting up all things before
#   login().
# 1.03
# - Removed broken quit()
# - Added get_ctrlaccount() and pwd_decrypt()
# - Added $pwd_is_cleartext argument to XMailCTRL():
#   If it is FALSE, then an encoded password is awaited.
# - Added $mailroot argument to XMailCTRL():
#   If the CTRL admin password is empty and $mailroot exists,
#   then the first account is fetched from ctrlaccounts.tab.
#   This is handy when your app runs on the same machine.
# 1.02
# - Submitting a tabbed sting to usersetmproc() and 
#   custdomset() works with or without appending 
#   "\n.\n", now.
# 1.01
# - Fixed: When the server returned a single line,
#   it was not correctly pushed into the result array.
# 1.00
# - Using a single, persistent connection, now.
#   Added therefore login() and logout().
# - Added auto generation of fieldname lists for easier
#   processing of returned data.
# - Stripped non class related stuff
#

class XMailCTRL {
	var $server_version;	// Server's version
	var $address;					// Server's IP
	var $port;						// Server's TCP port
	var $usr;							// Server's CTRL user
	var $pwd;							// Server's CTRL password
	var $pwd_is_cleartext;// Is $pwd stored in cleartext ?
	var $pwd_md5_encoded;	// The MD5 encoded password string
	var $mailroot;				// Server's mailroot folder without ending '/'
	var $error;						// Server's last error message
	var $last_cmd;				// Last processed command
	var $auto_reconnect;	// Reconnect on lost connections
	var $_fp;							// Socket file pointer
	var $_fldnames;				// Array of field names
	
	function XMailCtrl($address="",$port="",$usr="",$pwd="",$pwd_is_cleartext=TRUE, $mailroot="") {
		// If the CTRL admin password is empty and mailroot exists,
		// then the first CTRL account is fetched from ctrlaccounts.tab
		
		$this->address = $address;
		$this->port = $port;
		$this->usr = $usr;
		$this->pwd = $pwd;
		$this->pwd_is_cleartext = $pwd_is_cleartext;
		$this->pwd_md5_encoded = '';
		$this->mailroot = $mailroot;
		$this->auto_reconnect = TRUE;
		$this->_fldnames = array();
	}

// ---------------------------------------------------------------------------
// --- Login and server communication handling ...
// ---------------------------------------------------------------------------

	function login($MD5=TRUE) {

		// -- Connect to CTRL port

		$this->error = '';
		$this->_fp = fsockopen($this->address, $this->port, $errno, $this->error, 10);
		if (!$this->_fp) {
			return(FALSE);
			exit;
		}

		// --- Parse server's welcome banner

		$ret = explode(' ', fgets($this->_fp, 1024)); // read server info
		$this->server_version = $ret[3]; // get server version

		// --- Prepare password ...

		// Fetch login data from ctrlaccounts.tab, if no pwd given
		if( ($this->pwd == '') && (file_exists($this->mailroot)) ) {
			$account = $this->get_ctrlaccount($this->mailroot);
			$this->usr = $account[0];
			$this->pwd = $account[1];
			$pwd = $this->pwd;
		}
		// Decrypt password if encrypted
		if( ($this->pwd <> '') && ($this->pwd_is_cleartext == FALSE)) {
			$this->pwd = $this->pwd_decrypt($this->pwd);
			$pwd = $this->pwd;
		}
		// Do MD5 encoding if requested
		if($MD5 == TRUE ) {
			$this->pwd_md5_encoded = '#'.md5($ret[1].$this->pwd);
			$pwd = $this->pwd_md5_encoded;
		}		

		// --- Login

		fputs($this->_fp, "$this->usr\t$pwd\n"); // send login info
		$ret = fgets($this->_fp, 2048); // read login info

		if ($ret[0] == '-') { // not logged in
			$this->error = $ret; // get error msg
			return FALSE;
			exit;
		}		
	}

	function logout() {
		$this->pwd_md5_encoded = '';
		$cmd = "quit";
		return ($this->ctrlcmd($cmd, 0));		
	}
	
	function quit() {
		$this->logout();
	}
	
	function keepalive() {
		$this->noop();
	}

	function noop() {
		$cmd = "noop";
		return ($this->ctrlcmd($cmd, 0));
	} 

	function ctrlcmd($cmd, $ret_type, $vars='') {
		if($this->error <> '') {
			return(FALSE);
			exit;
		}
		$this->error = '';
		$this->last_cmd = $cmd;
		$this->last_vars = $vars;
		
		fputs($this->_fp, "$cmd\n"); //send command
		$ret = fgets($this->_fp, 2048); //read ret info
		if ($ret[0] == '-') { //error exec command
			$this->error = $ret; // get error msg
			return FALSE;
			exit;
		}
		if (!$ret[0]) { //Connection lost
			if($this->auto_reconnect == TRUE) {
				$this->login();
			}
			else {
				$this->error = 'CONNECTION LOST!';
			}
			return FALSE;
			exit;
		}

		switch ($ret_type) {
			case 0: //no more data expected from server
				return TRUE;
				break;

			case 1: //read data from server
				$ret = array();
				do { //read command output
					$tmp = fgetcsv($this->_fp, 2048, "\t");
						array_push($ret, $tmp); //use all elements
				} while ((substr($tmp[0], 0, 1) != '.'));

				array_pop($ret); // remove trailing dot
				sort($ret);
				// Prepend fieldnames ...
				if(sizeof($this->_fldnames) > 0) {
					array_unshift($ret,$this->_fldnames);
				}
				return($ret);
				break;

			case 2: //send vars to server
				if ($vars == "") {
					$vars = ".\n";
				}
				else {
					if(!preg_match('/\n\.\n$/',$vars)) {
						$vars = $vars."\n.\n";
						$vars = str_replace("\n\n.\n","\n.\n",$vars);
					}		
				}				
				fputs($this->_fp, "$vars"); //send command
				$ret = fgets($this->_fp, 2048); //read ret info
				if ($ret[0] == '-') { //error exec command
					$this->error = $ret; // get error msg
					return FALSE;
				}
				return($ret);
				break;

			default:
					$this->error = "ctrlcmd() - Unknown return type: $ret_type";
					return FALSE;
		}
	}
// ---------------------------------------------------------------------------
// --- Some helper methods ...
// ---------------------------------------------------------------------------

	// Returns an array of the first entry in ctrlaccounts.tab
	//
	function get_ctrlaccount($mailroot,$decrypt=TRUE) {
		$FILE = fopen($mailroot.'/ctrlaccounts.tab',"r");
		$account = fgetcsv($FILE,1024,"\t");
		fclose($FILE);
		if($decrypt == TRUE) {
			$account[1] = $this->pwd_decrypt($account[1]);
		}
		return($account);
	}	

	// Decrypts a xmcrypt password
	//
	function pwd_decrypt($pwd) {
		$a = explode("\r\n",chunk_split($pwd,2));
		$out = '';
		for($i=0; $i<sizeof($a)-1;$i++) {
			$out = $out.sprintf("%c", hexdec($a[$i]) ^ 101 & 255);
		}
		return($out);		
	}
	
	// Returns FALSE, if given string contains special chars
	// like e.g. german Umlauts or french accents ...
	//
	function test_ascii($s){
		for($i=0; $i<strlen($s); $i++){
			if( (ord($s[$i]) < 32) || (ord($s[$i]) > 127) ){
				return(FALSE);
			}
		}
		return(TRUE);
	}	
	
// ---------------------------------------------------------------------------
// --- CTRL: Domain methods ...
// ---------------------------------------------------------------------------

// List handled domains
	function domainlist() {
		$this->_fldnames = array("Domain");		
		$cmd = "domainlist";
		$ret = $this->ctrlcmd($cmd, 1);
		#sort($ret);
		return $ret;
	} 

// Add a domain
	function domainadd($l_domain) {
		$cmd = "domainadd\t$l_domain";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Delete a domain
	function domaindel($l_domain) {
		$cmd = "domaindel\t$l_domain";
		return ($this->ctrlcmd($cmd, 0));
	} 

// List handled domain aliases
	function aliasdomainlist() {
		$this->_fldnames = array("Alias","Domain");		
		$cmd = "aliasdomainlist";
		$ret = $this->ctrlcmd($cmd, 1);
		#sort($ret);
		return $ret;
	} 

// Add a domain alias
	function aliasdomainadd($l_domain, $l_aliasdomain) {
		$cmd = "aliasdomainadd\t$l_domain\t$l_aliasdomain";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Delete a domain alias
	function aliasdomaindel($l_domain) {
		$cmd = "aliasdomaindel\t$l_domain";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Get custom domain file
	function custdomget($l_domain) {
		$cmd = "custdomget\t$l_domain";
		return ($this->ctrlcmd($cmd, 1));
	} 

#// Set custom domain file
#	function custdomset_org($l_domain, $l_vars) {
#		$cmd = "custdomset\t$l_domain";
#		$tmp = '';
#		foreach($l_vars as $var) {
#      if (is_array($var))
#  			$tmp .= implode("\t", $var) . "\n";
#      else
#  			$tmp .= $var . "\n";
#		}
#    if ($tmp == "")
#    	$tmp = "WAIT\t0\n.\n";
#		return ($this->ctrlcmd($cmd, 2, $tmp));
#	} 

// Set custom domain file
	function custdomset($l_domain, $l_vars='') {
		$cmd = "custdomset\t$l_domain";
		return ($this->ctrlcmd($cmd, 2, $l_vars));
	} 

// List custom domains
	function custdomlist() {
		$this->_fldnames = array("Domain");				
		$cmd = "custdomlist";
		$ret = $this->ctrlcmd($cmd, 1);
		#sort($ret);
		return $ret;
	} 

// ---------------------------------------------------------------------------
// --- CTRL: User methods ...
// ---------------------------------------------------------------------------

// List handled users
	function userlist($l_domain="", $l_username="") {
		$this->_fldnames = array("Domain","Username","Password","Type");
		$cmd = "userlist\t$l_domain\t$l_username";
		$ret = $this->ctrlcmd($cmd, 1);
		#sort($ret);
		return $ret;
	} 

// Add user / mailinglist
	function useradd($l_domain, $l_username, $l_password, $l_usertype) {
		$cmd = "useradd\t$l_domain\t$l_username\t$l_password\t$l_usertype";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Delete a user / mailinglist
	function userdel($l_domain, $l_username) {
		$cmd = "userdel\t$l_domain\t$l_username";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Change a user password
	function userpasswd($l_domain, $l_username, $l_password) {
		$cmd = "userpasswd\t$l_domain\t$l_username\t$l_password";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Authenticate a user
	function userauth($l_domain, $l_username, $l_password) {
		$cmd = "userpasswd\t$l_domain\t$l_username\t$l_password";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Get user vars
	function uservars($l_domain, $l_username) {
		$this->_fldnames = array("Variable","Value");
		$cmd = "uservars\t$l_domain\t$l_username";
		$ret = $this->ctrlcmd($cmd, 1);
		foreach ($ret as $item) {
			$uservars["$item[0]"] = $item[1];
		}
		return $uservars;
	}
	
// Set user vars
	function uservarsset($l_domain, $l_username, $l_vars) {
		$cmd = "uservarsset\t$l_domain\t$l_username\t$l_vars";
		return ($this->ctrlcmd($cmd, 0));
	}
	
// Get user stats
	function userstat($l_domain, $l_username) {
		$this->_fldnames = array("Variable","Value");
		$cmd = "userstat\t$l_domain\t$l_username";
		$ret = $this->ctrlcmd($cmd, 1);
		if ($ret) {
			foreach ($ret as $item) {
				$userstats["$item[0]"] = $item[1];
			}
		} else {
			$userstats["stats"] = 'no data available';
		}
		return $userstats;
	}
	
// Get mailproc.tab
	function usergetmproc($l_domain, $l_username) {
		$cmd = "usergetmproc\t$l_domain\t$l_username";
		return ($this->ctrlcmd($cmd, 1));
	} 

// Set mailproc.tab
	function usersetmproc($l_domain, $l_username, $l_vars) {
		$cmd = "usersetmproc\t$l_domain\t$l_username";
		return ($this->ctrlcmd($cmd, 2, $l_vars));
	} 

// Add user alias
	function aliasadd($l_domain, $l_alias, $l_username) {
		$cmd = "aliasadd\t$l_domain\t$l_alias\t$l_username";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Delete a user alias
	function aliasdel($l_domain, $l_alias) {
		$cmd = "aliasdel\t$l_domain\t$l_alias";
		return ($this->ctrlcmd($cmd, 0));
	} 

// List handled aliases
	function aliaslist($l_domain="", $l_alias="*", $l_username="") {
		$this->_fldnames = array("Domain","Alias","Username");		
		$cmd = "aliaslist\t$l_domain\t$l_alias\t$l_username";
		$ret = $this->ctrlcmd($cmd, 1);
		#sort($ret);
		return $ret;
	} 

// ---------------------------------------------------------------------------
// --- Mailing list methods ...
// ---------------------------------------------------------------------------

// Add address to mailing list
	function mluseradd($l_domain, $mlname, $mailaddress, $perms='RW') {
		$cmd = "mluseradd\t$l_domain\t$mlname\t$mailaddress\t$perms";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Remove address from mailing list
	function mluserdel($l_domain, $mlname, $mailaddress) {
		$cmd = "mluserdel\t$l_domain\t$mlname\t$mailaddress";
		return ($this->ctrlcmd($cmd, 0));
	} 

// List mailing list addresses
	function mluserlist($l_domain, $mlname) {
		$this->_fldnames = array("Username");		
		$cmd = "mluserlist\t$l_domain\t$mlname";
		$ret = $this->ctrlcmd($cmd, 1);
		#sort($ret);
		return $ret;
	} 

// ---------------------------------------------------------------------------
// --- CTRL: POP3Link methods ...
// ---------------------------------------------------------------------------

// Add a POP3 external link
	function poplnkadd($l_domain, $l_username, $r_domain, $r_username, $r_password, $authtype) {
		$cmd = "poplnkadd\t$l_domain\t$l_username\t$r_domain\t$r_username\t$r_password\t$authtype";
		return ($this->ctrlcmd($cmd, 0));
	} 

	function poplnkdel($l_domain, $l_username, $r_domain, $r_username) {
		$cmd = "poplnkdel\t$l_domain\t$l_username\t$r_domain\t$r_username";
		return ($this->ctrlcmd($cmd, 0));
	} 

	function poplnklist($l_domain='', $l_username='') {
		$this->_fldnames = array("Local Domain", "Local User", "External Domain", "External User", "External Password", "Auth.Type","Enabled");				
		$cmd = "poplnklist\t$l_domain\t$l_username";
		$ret = $this->ctrlcmd($cmd, 1);
		#sort($ret);
		return $ret;
	} 

	function poplnkenable($enable, $l_domain, $l_username, $r_domain, $r_username='') {
		$cmd = "poplnkenable\t$enable\t$l_domain\t$l_username\t$r_domain\t$r_username";
		return ($this->ctrlcmd($cmd, 0));
	}
	
	function psynctrigger() {
		$this->cfgfileset('.psync-trigger','Pulling the trigger ...');	
	}

// ---------------------------------------------------------------------------
// --- CTRL: Spool management methods ...
// ---------------------------------------------------------------------------

// List frozen msgs
	function frozlist() {
		$this->_fldnames = array("Msgfile","Level_0","Level_1","From","To","Time","Size");				
		$cmd = "frozlist";
		return ($this->ctrlcmd($cmd, 1));
	} 

// Resubmit a frozen msg
	function frozsubmit($msgfile, $lev0, $lev1) {
		$cmd = "frozsubmit\t$lev0\t$lev1\t$msgfile";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Delete a frozen msg
	function frozdel($msgfile, $lev0, $lev1) {
		$cmd = "frozdel\t$lev0\t$lev1\t$msgfile";
		return ($this->ctrlcmd($cmd, 0));
	} 

// Get frozen msg log file
	function frozgetlog($msgfile, $lev0, $lev1) {
		$cmd = "frozgetlog\t$lev0\t$lev1\t$msgfile";
		return ($this->ctrlcmd($cmd, 1));
	} 

// Get frozen msg
	function frozgetmsg($msgfile, $lev0, $lev1) {
		$cmd = "frozgetmsg\t$lev0\t$lev1\t$msgfile";
		return ($this->ctrlcmd($cmd, 1));
	} 

// Flushing the queue
	function etrn($array_of_domains) {
		$cmd = "etrn\t".implode("\t",$array_of_domains);
		return ($this->ctrlcmd($cmd, 0));
	} 

// ---------------------------------------------------------------------------
// --- CTRL: Filesystem methods ...
// ---------------------------------------------------------------------------

// Get a config file (relative to Mailroot)
	function cfgfileget($file) {
		$cmd = "cfgfileget\t$file";
		return ($this->ctrlcmd($cmd, 1));
	} 

// Save a config file (relative to Mailroot)
	function cfgfileset($filename,$content) {
		$cmd = "cfgfileset\t$filename";
		return ($this->ctrlcmd($cmd, 2, $content));
	} 	
	
// List files (relative to Mailroot)
	function filelist($dir,$pattern) {
		$cmd = "filelist\t$dir\t$pattern";
		return ($this->ctrlcmd($cmd, 1));
	} 	

}
// ---------------------------------------------------------------------------
// --- GPL Hosting Global Variables
// ---------------------------------------------------------------------------
$strArray = explode(",",$Xmail);
$ctrl = new XMailCTRL($strArray[0],$strArray[1],$strArray[2],$strArray[3],FALSE,$strArray[4]);
?>