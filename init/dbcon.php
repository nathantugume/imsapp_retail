<?php

require_once __DIR__.'/../config/config.php';


class Database extends Config{
	
	private static $instance = null;
	private $connection = null;

	// Private constructor to prevent direct instantiation
	private function __construct(){
		// Constructor is private to prevent direct instantiation
	}

	// Get the singleton instance
	public static function getInstance(){
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	// Get the database connection (singleton pattern for connection too)
	public function connect(){
		
		if ($this->connection === null) {
			try {
				$connect_obj = new Config();
				//$dbcon = new PDO("mysql:host=".DBHOST."; dbname=".DBNAME.";",DBUSER,DBPASS);
				$this->connection = new PDO("mysql:host=".$connect_obj->host_connect() . ";dbname=" .$connect_obj->dbname_connect(),$connect_obj->user_connect(),$connect_obj->pass_connect());
				$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
				$this->connection->setAttribute(PDO::ATTR_TIMEOUT, 30);
				//echo "Connected..................!!!";
			}catch(PDOException $e) {
				// Log detailed error information
				error_log("Database Connection Error: " . $e->getMessage());
				error_log("Connection Details - Host: " . $connect_obj->host_connect() . ", Database: " . $connect_obj->dbname_connect() . ", User: " . $connect_obj->user_connect());
				
				// Throw a more descriptive exception instead of dying
				throw new Exception("Database connection failed: " . $e->getMessage());
			}
		}
		
		return $this->connection;
	}

	// Prevent cloning of the instance
	private function __clone() {}

	// Prevent unserialization of the instance
	public function __wakeup() {
		throw new Exception("Cannot unserialize singleton");
	}

}

// Global function to get database instance
function getDB() {
	return Database::getInstance();
}




