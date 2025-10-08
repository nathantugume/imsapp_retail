<?php


class Brand{
	use Exists;
		private $dbcon;
		private $b_table="brands";

		public function __construct(){

			$this->dbcon = getDB();
		}

		public function addBrand($data){
			if(empty($data['brand_name'])){
				return "Required";

			}else if(!preg_match("/^[A-Za-z- ]{2,50}$/", $data['brand_name'])){

				return "Invalid_Name";

			}else if($this->brand_check($data['brand_name'])){

				return "Brand_Exists";

			}else{

				$sql = "INSERT INTO `".$this->b_table."`(`brand_name`,`b_status`) VALUES(?,?)";
				$stmt = $this->dbcon->connect()->prepare($sql);
				if(is_object($stmt)){
					$stmt->bindValue(1,$data['brand_name'], PDO::PARAM_STR);
					$stmt->bindValue(2,$data['status'], PDO::PARAM_INT);
					$data =$stmt->execute();
					if($data){
						return "Brand_added";
					}else{
						return "Not_Added";
					}
				}
				return false;
			}
		 	
		}
		
		public function get_all_brands(){
			$sql = "SELECT * FROM ".$this->b_table;
			$stmt = $this->dbcon->connect()->prepare($sql);
			if(is_object($stmt)){
				$stmt->execute();
				if($stmt->rowCount()>0){
					$data = $stmt->fetchAll(PDO::FETCH_OBJ);
					if(!empty($data)){
					  return $data;
					}else{
						return false;
					}
				}
				return false;
			}
		

		}
		public function fetch_all_brands($data,$starting_point,$record_per_page){
			$sql = "SELECT * FROM ".$this->b_table." LIMIT $starting_point, $record_per_page";
			$stmt = $this->dbcon->connect()->prepare($sql);
			if(is_object($stmt)){
				$stmt->execute();
				if($stmt->rowCount()>0){
					$data = $stmt->fetchAll(PDO::FETCH_OBJ);
					if(!empty($data)){
					  return $data;
					}else{
						return false;
					}
				}
				return false;
			}
		

		}
		public function pagination_link(){
			$sql = "SELECT * FROM ".$this->b_table;
			$stmt = $this->dbcon->connect()->prepare($sql);
			if(is_object($stmt)){
				$stmt->execute();
				$total_records = $stmt->rowCount();
					
				//debug($total_records);
				return $total_records;

			}
			return false;
		}
		public function fetch_single_brand($brand){
			// debug($brand);
			$sql="SELECT * FROM ".$this->b_table." WHERE brand_id=?";
			$stmt = $this->dbcon->connect()->prepare($sql);
			if(is_object($stmt)){
				$stmt->bindValue(1,$brand['edit_bid'], PDO::PARAM_INT);
				$stmt->execute();
				if($stmt->rowCount()>0){
					$data = $stmt->fetch(PDO::FETCH_OBJ);
					if(!empty($data)){
						// debug($data);
						return $data;

					}else{
						return false;
					}
				}
				return false;
			}
		}
	public function update_brand($brand){
		// debug($brand);
		try {
			if(empty($brand['update_brand_name'])){
				return "Required";
			}else{
				$sql="UPDATE ".$this->b_table." SET `brand_name`=?, `b_status`=? WHERE `brand_id`=?";
				$stmt = $this->dbcon->connect()->prepare($sql);
				if(is_object($stmt)){
					$stmt->bindValue(1, $brand['update_brand_name'], PDO::PARAM_STR);
					$stmt->bindValue(2, $brand['update-status'], PDO::PARAM_INT);
					$stmt->bindValue(3, $brand['bid'], PDO::PARAM_INT);
					$data = $stmt->execute();
					if($data){
						return "Updated";
					}else{
						return "Not_Updated";
					}
				}
			}
		} catch (Exception $e) {
			// Log the error for debugging
			error_log("Brand Update Error: " . $e->getMessage());
			error_log("Brand Update Data: " . json_encode($brand));
			
			// Return a more specific error based on the exception type
			if (strpos($e->getMessage(), 'Connection') !== false) {
				return "Connection_Error";
			} elseif (strpos($e->getMessage(), 'Duplicate') !== false) {
				return "Duplicate_Entry";
			} else {
				return "Database_Error";
			}
		}
	}

	public function delete_brand($brand){
		try {
			$sql="DELETE FROM ".$this->b_table." WHERE `brand_id`=?";
			$stmt =  $this->dbcon->connect()->prepare($sql);
			if(is_object($stmt)){
			   $stmt->bindValue(1, $brand['del_id'], PDO::PARAM_INT);
			   $data= $stmt->execute();
			   if($data){
			   	 return "Deleted_Brand";
			   }else{
			   	 return "Not_Deleted";
			   }
			}
			return false;
		} catch (Exception $e) {
			// Log the error for debugging
			error_log("Brand Delete Error: " . $e->getMessage());
			error_log("Brand Delete Data: " . json_encode($brand));
			
			// Return a more specific error based on the exception type
			if (strpos($e->getMessage(), 'Connection') !== false) {
				return "Connection_Error";
			} else {
				return "Database_Error";
			}
		}
	}

}


$brand = new Brand();
