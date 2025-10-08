<?php
	/*define("DBHOST", "localhost");
	define("DBUSER", "root");
	define("DBPASS", "");
	define("DBNAME", "imsapp");*/



class Config{
		private const H_DBHOST = "localhost"; 
		private const U_DBUSER = "root";
		private const P_DBPASS = "your_password_here"; 
		private const N_DBNAME = "imsapp";
		private $host;
		private $user;
		private $pass;
		private $db;
		private static function db_host(){
			return self::H_DBHOST;
		}
		private static function db_user(){
			return self::U_DBUSER;
		}
		private static function db_pass(){
			return self::P_DBPASS;
		}
		private static function db_name(){
			return self::N_DBNAME;
		}
		public function host_connect(){
				$this->host = Config::db_host();
				return $this->host;
		}
		public function user_connect(){
				$this->user = Config::db_user();
				return $this->user;
		}
		public function pass_connect(){
				$this->pass = Config::db_pass();
				return $this->pass;
		}
		public function dbname_connect(){
				$this->db = Config::db_name();
				return $this->db;
		}

	
}



