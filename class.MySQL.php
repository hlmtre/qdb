<?php

class MySQL{
	// Define variables
	public $sError;

	private $sHostname = "localhost";
	private $sUsername = "pybot";
	private $sPassword = "pyb07";
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
			mysqli_close($this->sDBLink);
		}

		$this->sDBLink = mysqli_connect($this->sHostname, $this->sUsername, $this->sPassword);
		
		if (!$this->sDBLink){
			$this->sError = 'Could not connect to server: ' . mysqli_error($this->sDBLink);
			return false;
		}

		if (!$this->UseDB()){
			$this->sError = 'Could not connect to database: ' . mysqli_error($this->sDBLink);
			return false;
		}
		// for testing only
		return true;
	}

	function getDB(){
		$this->Connect();
		return $this->sDBLink;
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
		if (!mysqli_select_db($this->sDBLink, $this->sDatabase)) {
			$this->sError = 'Cannot select database: ' . mysqli_error($this->sDBLink);
			return false;
		}else{
			return true;
		}
	}

	// Executes MySQL query
	function ExecuteSQL($sSQLQuery){
		$this->sLastQuery = $sSQLQuery;
		if($this->aResult = mysqli_query($this->sDBLink, $sSQLQuery)){
			$this->lastInsertID = mysqli_insert_id($this->sDBLink);
			return true;
		}else{
			$this->sError = mysqli_error($this->sDBLink);
			return false;
		}
	}

	function updateQuoteById($id, $quote) {
		$query = "UPDATE qdb set quote = " . $quote . " WHERE id = " . $id ;
		$result = mysqli_query($query, $this->sDBLink) or die(mysqli_error($this->sDBLink));
		print_r($result);

	}

	function getLastInsertID() {
		if ($this->lastInsertID)
			return $this->lastInsertID;
		else return -1;
	}

	// Returns array of rows if exists, or FALSE on no rows
	function setRunBuild($sSQLQuery){
		$this->sLastQuery = $sSQLQuery;
		$result = mysqli_query($this->sDBLink, $sSQLQuery) or die(mysqli_error($this->sDBLink));

		$this->aResult = $result;


		if (mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_array($result)) {
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
		$result = mysqli_query($query, $this->sDBLink) or die(mysqli_error($this->sDBLink));
		if (mysqli_num_rows($result) == 1) {
			while ($row = mysqli_fetch_array($result)) {
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
		$result = mysqli_query($query, $this->sDBLink) or die(mysqli_error($this->sDBLink));
		if (mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_array($result)) {
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
		$result = mysqli_query($query, $this->sDBLink) or die(mysqli_error($this->sDBLink));
		if (mysqli_num_rows($result) > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	// Performs a 'mysqli_real_escape_string' on the entire array/string
	function SecureData($aData){
		if(is_array($aData)){
			foreach($aData as $iKey=>$sVal){
				if(!is_array($aData[$iKey])){
					$aData[$iKey] = mysqli_real_escape_string($aData[$iKey], $this->sDBLink);
				}
			}
		}else{
			$aData = mysqli_real_escape_string($aData, $this->sDBLink);
		}
		return $aData;
	}

}












