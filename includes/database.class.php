<?php
/**
 *	Manages all MySQL requests.
 */
 
class MySQL {

	/**
	 *	For SELECT query.
	 */
	function select($tableName, $selectRows = "", $para = "", $extraArgs = ""){
		/**
		 *	We must have a table name to make a query.
		 *	If they didn't give us one give up on the request.
		 */
		if ($tableName == ""){
			return false;
		}
		
		/**
		 *	If the users has elected not to select any rows set it ourself.
		 */
		if ($selectRows == ""){
			$selectRows = "*";
		}
		
		/**
		 *	Used to hold the logical part of the SQL query.
		 */
		$queryString = "";
		
		/**
		 *	If it's an array we will generate the string outselves.
		 */
		if (is_array($para) == true){
		
			$arraySize = count($para);
			$loop = 0;
			
			foreach($para as $key => $value){
				if ($loop != 0 AND $loop != $arraySize){
					$queryString .= " AND ";
				}
				$loop++;
				$queryString .= mysql_real_escape_string($key) . " = '" . mysql_real_escape_string($value) . "'";				
			}
		}else{
			// The array function has been overwriten just outpit the para string.
			$queryString = $para;
		}
		
		$query = "SELECT " . mysql_real_escape_string($selectRows) . " FROM " . mysql_real_escape_string($tableName) . " ";
		
		if ($queryString != ""){
			$query .= " WHERE " . $queryString . " ";
		}
		
		$query .= mysql_real_escape_string($extraArgs);
		
		return mysql_query($query);
	}
	
	/**
	 *	UPDATE SQL query function.
	 */
	function update($tableName, $setPara, $wherePara = "", $extraArgs = "", $debug = 0) {
		if ($tableName == ""){
			return false;
		}
		
		$setQuery = "";
		if (is_array($setPara)){
			$arraySize = count($setPata);
			$loop = 0;
			
			foreach($setPara as $key => $value){
				if ($loop != 0 AND $loop != $arraySize){
					$setQuery .= ", ";
				}
				$loop++;
				$setQuery .= mysql_real_escape_string($key) . " = '" . mysql_real_escape_string($value) . "'";	
			}
		}else{
			$setQuery = mysql_real_escape_string($setPara);
		}
		
		$whereQuery = "";
		if (is_array($wherePara)){
			$arraySize = count($wherePata);
			$loop = 0;
			
			foreach($wherePara as $key => $value){
				if ($loop != 0 AND $loop != $arraySize){
					$whereQuery .= ", ";
				}
				$loop++;
				$whereQuery .= mysql_real_escape_string($key) . " = '" . mysql_real_escape_string($value) . "'";	
			}
		}else{
			$whereQuery = mysql_real_escape_string($wherePara);
		}
		
		$fullQuery = "UPDATE " . mysql_real_escape_string($tableName) . " SET " . $setQuery;
		
		if ($wherePara != ""){
			$fullQuery .= " WHERE " . $whereQuery . " " . $extraArgs;
		}else{
			$fullQuery .= $extraArgs;
		}
		
		if ($debug == 1){
			return $fullQuery;
		}
		return mysql_query($fullQuery);
	}
	
	/**
	 *	MySQL INSERT functionality.
	 */
	function insert($tableName, $values){
		$dec = "(";
		$val = "(";
		
		$loop = 0;
		
		if (is_array($values)){
			foreach($values as $key => $value){
				if ($loop != 0){
					$dec .= ", ";
					$val .= ", ";
				}
				$dec .= $key;
				$val .= "'" . mysql_real_escape_string($value) . "'";
				$loop++;
			}
			$dec .= ")";
			$val .= ")";
			
		}else{
			// We don't allow non-arrayed values.
			return false;
		}
		
		$query = "INSERT INTO " . mysql_real_escape_string($tableName . $dec) . " VALUES" . $val;
		$run = mysql_query($query) or die(mysql_error());

		return $run;
	}
	
	/**
	 *	MySQL DELETE functionality.
	 */
	function delete($tableName, $para, $extraArgs = ""){
		if ($tableName == ""){
			return false;
		}
		if (is_array($para)){
			// Loop and clean out the information.
			
			$arraySize = count($para);
			$loop = 0;
			
			foreach($para as $key => $value){
				if ($loop != 0 AND $loop != $arraySize){
					$delQuery .= ", ";
				}
				$loop++;
				$delQuery .= mysql_real_escape_string($key) . " = '" . mysql_real_escape_string($value) . "'";
			}
			$query = "DELETE FROM " . mysql_real_escape_string($tableName) . " WHERE " . $delQuery . " " . $extraArgs;
			return mysql_query($query);
		}
	}
	
	/**
	 *	Return the num rows of a query.
	 */
	function numRows($run = ""){
		if ($run == ""){
			return false;
		}	
		return mysql_num_rows($run);
	}
	
	/**
	 *	Return the mysql fetch assoc
	 */	
	function fetchAssoc($run = "") {
		if ($run == ""){
			return false;
		}
		if (mysql_num_rows($run) == ""){
			return false;
		}
		return mysql_fetch_assoc($run);
	}
	
	function fetchRow($run = ""){
		if ($run == ""){
			return false;
		}
		if (mysql_num_rows($run) == 0){
			return false;
		}
		return mysql_fetch_row($run);
	}
	
	function query($query){
		return mysql_query($query);
	}
	
}

?>