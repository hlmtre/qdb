<?php

class MySQL{
	// Define variables
	public $sError;

	private $sHostname = "localhost";
	private $sUsername = "pybot";
	private $sPassword = "1q2w3e4r";
	private $sDatabase = "pybot";

	public $sDBLink;
	public $aResult;
	public $sLastQuery;
	public $lastInsertID;

	// Class Constructor
	// Assigning values to variables
	function MySQL(){
		$this->Connect();
	}

	// Connects class to DB
	function Connect(){
		if ($this->sDBLink){
			mysql_close($this->sDBLink);
		}

		$this->sDBLink = mysql_connect($this->sHostname, $this->sUsername, $this->sPassword);
		
		if (!$this->sDBLink){
			$this->sError = 'Could not connect to server: ' . mysql_error($this->sDBLink);
			return false;
		}

		if (!$this->UseDB()){
			$this->sError = 'Could not connect to database: ' . mysql_error($this->sDBLink);
			return false;
		}
		// for testing only
		return true;
	}

	function isConnected() {
		if ($this->sDBLink) return true;
		return false;
	}

	function getError() {
		return $this->sError;
	}

	// Selects database to use
	function UseDB(){
		if (!mysql_select_db($this->sDatabase, $this->sDBLink)) {
			$this->sError = 'Cannot select database: ' . mysql_error($this->sDBLink);
			return false;
		}else{
			return true;
		}
	}

	// Executes MySQL query
	function ExecuteSQL($sSQLQuery){
		$this->sLastQuery = $sSQLQuery;
		if($this->aResult = mysql_query($sSQLQuery, $this->sDBLink)){
			$this->lastInsertID = mysql_insert_id();
			return true;
		}else{
			$this->sError = mysql_error($this->sDBLink);
			return false;
		}
	}

	function getLastInsertID() {
		if ($this->lastInsertID)
			return $this->lastInsertID;
		else return -1;
	}

	// Returns array of rows if exists, or FALSE on no rows
	function setRunBuild($sSQLQuery){
		$this->sLastQuery = $sSQLQuery;
		$result = mysql_query($sSQLQuery, $this->sDBLink) or die(mysql_error($this->sDBLink));

		$this->aResult = $result;


		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$array[] = $row;
			}
			return $array;
		}else{
			return FALSE;
		} 
	}

	// TLDR don't be a nubtard
	/*
		function getUserIdEfficiently($username, $password) {
			$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
			$arr = $this->setRunBuild($query);
			if ($arr.length() != 1)
				return FALSE;
			else return $arr['userID'];
		}
	*/

		
	// Returns userID given username and password
	function getUserID($username, $password){
		$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
		$this->sLastQuery = $query;
		$result = mysql_query($query, $this->sDBLink) or die(mysql_error($this->sDBLink));
		if (mysql_num_rows($result) == 1) {
			while ($row = mysql_fetch_array($result)) {
				$a[] = $row;
			}
			foreach ($a as $data) {
				$UserID = $data['ID'];
			}
			return $UserID;
		}else{
			return FALSE;
		} 
	}

	// Returns array of user info given userID
	function getUserInfo($UserID){
		$query = "SELECT * FROM users WHERE ID = '$UserID'";
		$result = mysql_query($query, $this->sDBLink) or die(mysql_error($this->sDBLink));
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$array[] = $row;
			}
			return $array;
		}else{
			return FALSE;
		}
	}

	// Returns true if username is taken
	function checkTaken($username){
		$query = "SELECT * FROM users WHERE username = '".$username."'";
		$result = mysql_query($query, $this->sDBLink) or die(mysql_error($this->sDBLink));
		if (mysql_num_rows($result) > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	// Returns true if gallery name is taken
	function checkGal($galurl){
		$query = "SELECT * FROM galleries WHERE gal_URL = '".$galurl."'";
		$result = mysql_query($query, $this->sDBLink) or die(mysql_error($this->sDBLink));

		if (mysql_num_rows($result) > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	// Performs a 'mysql_real_escape_string' on the entire array/string
	function SecureData($aData){
		if(is_array($aData)){
			foreach($aData as $iKey=>$sVal){
				if(!is_array($aData[$iKey])){
					$aData[$iKey] = mysql_real_escape_string($aData[$iKey], $this->sDBLink);
				}
			}
		}else{
			$aData = mysql_real_escape_string($aData, $this->sDBLink);
		}
		return $aData;
	}

}












