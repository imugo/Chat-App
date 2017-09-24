<?php
class DatabaseHandler {
	//Hold an instance of the PDO class
	private static $_mHandler;
	
	//private function to prevent direct creation of object
	private function __construct() {
	}
	
	//Return an initialised database handler
	private static function GetHandler() {
		//Create a database connection only if one doesn't exist
		if (!isset(self::$_mHandler)) {
			//Execute code catching potential exceptions
			try {
				//Create a new PDO class instance
				self::$_mHandler = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT=>DB_PERSISTENCY));
				
				//Configure PDO to throw exceptions
				self::$_mHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
			}
			catch (PDOException $e) {
				//close the database handler and trigger an error
				self::Close();
				die(json_encode(array('error'=>$e->getMessage())));
			}
		}
		return self::$_mHandler;
	}
	
	//Close the PDO instance
	public static function Close() {
		self::$_mHandler = null;	
	}
	
	//Wrapper method for PDOStatement::Execute()
	public static function Execute($sqlQuery, $params = null) {
		//Try to execute an sql query or a stored procedure
		try {
			//Get the database handler
			$database_handler = self::GetHandler();
			
			//Prepare the query for execution
			$statement_handler = $database_handler->prepare($sqlQuery);
			
			//Execute query
			$statement_handler->execute($params);	
		}
		catch (PDOException $e) {
			self::Close();
			die(json_encode(array('error'=>$e->getMessage())));	
		}
	}
	
	//Wrapper method for PDOStatement::fetchAll()
	public static function GetAll($sqlQuery, $params = null, $fetchStyle = PDO::FETCH_ASSOC) {
		//initialize the return value to null
		$result = null;
		
		////Try to execute an sql query or a stored procedure
		try {
			//Get the database handler
			$database_handler = self::GetHandler();
			
			//Prepare the query for execution
			$statement_handler = $database_handler->prepare($sqlQuery);	
			
			//Execute query
			$statement_handler->execute($params);
			
			//Fetch result
			$result = $statement_handler->fetchAll($fetchStyle);
		}
		catch (PDOException $e) {
			self::Close();
			die(json_encode(array('error'=>$e->getMessage())));	
		}
		
		//Returm the query results
		return $result;
	}
	
	//Wrapper method for PDOStatement::fetch()
	public static function GetRow($sqlQuery, $params = null, $fetchStyle = PDO::FETCH_ASSOC) {
		//initialize the return value to null
		$result = null;
		
		////Try to execute an sql query or a stored procedure
		try {
			//Get the database handler
			$database_handler = self::GetHandler();
			
			//Prepare the query for execution
			$statement_handler = $database_handler->prepare($sqlQuery);	
			
			//Execute query
			$statement_handler->execute($params);
			
			//Fetch result
			$result = $statement_handler->fetch($fetchStyle);
		}
		catch (PDOException $e) {
			self::Close();
			die(json_encode(array('error'=>$e->getMessage())));	
		}
		
		//Return the query results
		return $result;
	}
	
	//Return the first column from a row
	public static function GetOne($sqlQuery, $params = null) {
		//initialize the return value to null
		$result = null;
		
		////Try to execute an sql query or a stored procedure
		try {
			//Get the database handler
			$database_handler = self::GetHandler();
			
			//Prepare the query for execution
			$statement_handler = $database_handler->prepare($sqlQuery);	
			
			//Execute query
			$statement_handler->execute($params);
			
			//Fetch result
			$result = $statement_handler->fetch(PDO::FETCH_NUM);
			
			//Save the first value of the result set (first column of the first row) to $result
			$result = $result[0];
		}
		catch (PDOException $e) {
			self::Close();
			die(json_encode(array('error'=>$e->getMessage())));	
		}
		
		//Returm the query results
		return $result;
	}
}
?>