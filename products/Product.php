<?php

class Product{
	use Exists;
	    private $dbcon;
		private $b_table="brands";
		private $p_table="products";

		public function __construct(){

			$this->dbcon = getDB();
		}
		
		public function fetch_all_brands(){
			$brand = new Brand();
			$brands = $brand->get_all_brands();		
			if(!empty($brands)){
			  	return $brands;
			}else{
				return false;
			}
		}

		public function add_product($product){
			//debug($product);
			if(empty($product['product_name']) || empty($product['stock']) || empty($product['price']) || empty($product['description'])){
				return "Required";

			}else if(!preg_match("/^[A-Za-z-0-9 ]{3,50}$/", $product['product_name'])){
            	return "Invalid_Name";

        	}else if($this->product_check($product['product_name'])){
				return "Product_Exists";

			}else{
				$sql="INSERT INTO `products`(`cat_id`,`brand_id`,`product_name`,`stock`,`price`,`buying_price`,`description`,`expiry_date`) VALUES(?,?,?,?,?,?,?,?)";
				$stmt = $this->dbcon->connect()->prepare($sql);
				if(is_object($stmt)){
					$stmt->bindValue(1, $product['category_id'], PDO::PARAM_INT);
					$stmt->bindValue(2, $product['brand_id'], PDO::PARAM_INT);
					$stmt->bindValue(3, $product['product_name'], PDO::PARAM_STR);
					$stmt->bindValue(4, $product['stock'], PDO::PARAM_INT);
					$stmt->bindValue(5, $product['price'], PDO::PARAM_STR);
                                        $stmt->bindValue(6, $product['buying_price'], PDO::PARAM_STR);
					$stmt->bindValue(7, $product['description'], PDO::PARAM_STR);
					$stmt->bindValue(8, $product['expiry_date'], PDO::PARAM_STR);
					$data = $stmt->execute();
					// debug($data);
					if($data){
						return "Product_Added";
					}else{
						return "Not_Added";
					}
				}
				return false;
			}
		}

		public function fetch_all_products($starting_point,$record_per_page){
			$sql="SELECT * FROM products p, brands b, categories c WHERE p.brand_id=b.brand_id AND p.cat_id=c.cat_id LIMIT $starting_point,$record_per_page ";
			$stmt = $this->dbcon->connect()->prepare($sql);
			if(is_object($stmt)){
				$stmt->execute();
				if($stmt->rowCount()>0){
					$data = $stmt->fetchAll(PDO::FETCH_OBJ);
					// debug($data);
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
			$sql ="SELECT * FROM products";
			$stmt = $this->dbcon->connect()->prepare($sql);
			if(is_object($stmt)){
				$stmt->execute();
				if($stmt->rowCount()>0){
					$total_records = $stmt->rowCount();
					//debug($total_records);
					return $total_records;

				}
				return false;
			}

		}
		public function fetch_single_product($pid){
			// debug($pid);
			//$sql = "SELECT * FROM products,categories,brands WHERE products.brand_id=brands.bid, products.cat_id=categories.cat_id AND pid=?";
			$sql="SELECT * FROM `products`,`brands`, `categories`  WHERE `products`.`brand_id`=`brands`.`brand_id` AND `products`.`cat_id`=`categories`.`cat_id` AND `products`.`pid`=?";

			//$sql = "SELECT * FROM products,categories,brands WHERE  pid=?";
			$stmt = $this->dbcon->connect()->prepare($sql);
			if(is_object($stmt)){
				$stmt->bindValue(1, $pid, PDO::PARAM_INT);
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
	public function update_product($product){
		// debug($product);
		// exit;
		
		try {
			// Only validate essential fields - product name, stock, and price
			if(empty($product['update_product_name']) || empty($product['update_stock']) || empty($product['update_price'])){
				return "Required";

			}else if(!preg_match("/^[A-Za-z-0-9 ]{3,50}$/", $product['update_product_name'])){
	        	return "Invalid_Name";

	    	}else{
	    		// Use a simpler approach - always include all fields but use existing values if not provided
	    		$sql = "UPDATE products SET cat_id=?, brand_id=?, product_name=?, stock=?, price=?, buying_price=?, description=?, p_status=?, expiry_date=? WHERE pid=?";
	    		
				$stmt = $this->dbcon->connect()->prepare($sql);
				if(is_object($stmt)){
					// Get current product data to preserve existing values for optional fields
					$currentProduct = $this->fetch_single_product($product['upid']);
					
					// Use provided values or keep existing ones (with fallbacks)
					$buyingPrice = !empty($product['update_buying_price']) ? $product['update_buying_price'] : ($currentProduct ? $currentProduct->buying_price : '0');
					$expiryDate = !empty($product['update_expiry_date']) ? $product['update_expiry_date'] : ($currentProduct ? $currentProduct->expiry_date : null);
					
					$stmt->bindValue(1, $product['update_category_id'], PDO::PARAM_INT);
					$stmt->bindValue(2, $product['update_brand_id'], PDO::PARAM_INT);
					$stmt->bindValue(3, $product['update_product_name'], PDO::PARAM_STR);
					$stmt->bindValue(4, $product['update_stock'], PDO::PARAM_INT);
					$stmt->bindValue(5, $product['update_price'], PDO::PARAM_STR);
					$stmt->bindValue(6, $buyingPrice, PDO::PARAM_STR);
					$stmt->bindValue(7, $product['update_desc'] ?? ($currentProduct ? $currentProduct->description : ''), PDO::PARAM_STR);
					$stmt->bindValue(8, $product['update_status'] ?? ($currentProduct ? $currentProduct->p_status : 1), PDO::PARAM_INT);
					$stmt->bindValue(9, $expiryDate, PDO::PARAM_STR);
					$stmt->bindValue(10, $product['upid'], PDO::PARAM_INT);
					
					$data = $stmt->execute();
					
					if($data){
						return "Product_Updated";
					}else{
						return "Not_Updated";
					}
				}else{
					return "Not_Updated";
				}
	    	}
		} catch (Exception $e) {
			// Log the error for debugging
			error_log("Product Update Error: " . $e->getMessage());
			error_log("Product Update Data: " . json_encode($product));
			
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
		public function delete_product($data){
			$sql = "DELETE FROM products WHERE pid=?";
			$stmt = $this->dbcon->connect()->prepare($sql);
			if(is_object($stmt)){
				$stmt->bindValue(1, $data['pid'], PDO::PARAM_INT);
				$data=$stmt->execute();
				if($data){
					return "Deleted_Product";
				}else{
					return "Not_Deleted";
				}
				
        	}
        	return false;
		}
		public function add_stock($stock){
			// debug($stock);
			// exit;
			if(empty($stock['stock'])){
				return "Required";

			}else{
				// Calculate new stock: current stock + new stock
				$new_stock = $stock['inventory'] + $stock['stock'];
        		$sql="UPDATE products SET stock=? WHERE pid=? ";
				$stmt = $this->dbcon->connect()->prepare($sql);
				if(is_object($stmt)){
					$stmt->bindValue(1, $new_stock, PDO::PARAM_INT);
					$stmt->bindValue(2, $stock['sid'], PDO::PARAM_INT);
					$data=$stmt->execute();

					if($data){
						return "Stock_Added";
					}else{
						return "Not_Added";
					}
					return false;
				}
        	}

		}

		public function get_expiring_products($days_ahead = 30){
			$sql = "SELECT p.*, b.brand_name, c.category_name 
					FROM products p 
					JOIN brands b ON p.brand_id = b.brand_id 
					JOIN categories c ON p.cat_id = c.cat_id 
					WHERE p.expiry_date IS NOT NULL 
					AND p.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
					AND p.p_status = '1' 
					ORDER BY p.expiry_date ASC";
			
			$stmt = $this->dbcon->connect()->prepare($sql);
			$stmt->bindValue(1, $days_ahead, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_OBJ);
		}

		public function get_expired_products(){
			$sql = "SELECT p.*, b.brand_name, c.category_name 
					FROM products p 
					JOIN brands b ON p.brand_id = b.brand_id 
					JOIN categories c ON p.cat_id = c.cat_id 
					WHERE p.expiry_date IS NOT NULL 
					AND p.expiry_date < CURDATE() 
					AND p.p_status = '1' 
					ORDER BY p.expiry_date ASC";
			
			$stmt = $this->dbcon->connect()->prepare($sql);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_OBJ);
		}


}

$product =  new Product();

