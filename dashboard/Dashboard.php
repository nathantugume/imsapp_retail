<?php

class Dashboard{

	private $dbcon; 

	public function __construct(){

		$this->dbcon = getDB();

	}

	public function count_users(){

		$sql ="SELECT COUNT(*) AS total_users FROM users";
		$stmt = $this->dbcon->connect()->prepare($sql);
		$stmt->execute() OR die($this->dbcon->connect()->error);
		$data = $stmt->fetch(PDO::FETCH_BOTH);
		$total_users = $data['total_users'];
		if($total_users){
			return ['total_user'=>$total_users];
		}
		return false;
	}
	public function count_category(){
		$sql ="SELECT COUNT(*) AS total FROM categories";
		$stmt = $this->dbcon->connect()->prepare($sql);
		$stmt->execute() OR die($this->dbcon->connect()->error);
		$data = $stmt->fetch(PDO::FETCH_BOTH);
		$total_cat = $data['total'];
		if($total_cat){
			return ['cat'=>$total_cat];
		}
		return false;
		
	}
	public function count_brands(){
		$sql ="SELECT COUNT(*) AS total FROM brands";
		$stmt = $this->dbcon->connect()->prepare($sql);
		$stmt->execute() OR die($this->dbcon->connect()->error);
		$data = $stmt->fetch(PDO::FETCH_BOTH);
		$total_brands = $data['total'];
		if($total_brands){
			return ['brands'=>$total_brands];
		}
		return false;
		
	}
	public function count_products(){
		$sql ="SELECT COUNT(*) AS total FROM products";
		$stmt = $this->dbcon->connect()->prepare($sql);
		$stmt->execute() OR die($this->dbcon->connect()->error);
		$data = $stmt->fetch(PDO::FETCH_BOTH);
		$products = $data['total'];
		
		if($products){
			return ['products'=>$products];
		}
		return false;
		
	}
	public function total_order_value(){
		$sql ="SELECT SUM(net_total) AS order_value FROM orders";
		$stmt = $this->dbcon->connect()->prepare($sql);
		$stmt->execute() OR die($this->dbcon->connect()->error);
		$data = $stmt->fetch(PDO::FETCH_BOTH);
		$value = $data['order_value'];
		// debug($value);
		if($value){
			return ['value'=>$value];
		}
		return false;
		
		
	}
	public function cash_order_value(){
		// Calculate cash received as: Sum of all cash payments made
		$sql ="SELECT SUM(paid) AS cash_received FROM orders WHERE payment_method='Cash'";
		$stmt = $this->dbcon->connect()->prepare($sql);
		$stmt->execute() OR die($this->dbcon->connect()->error);
		$data = $stmt->fetch(PDO::FETCH_BOTH);
		$cash_value = $data['cash_received'];
		if($cash_value){
			return ['cash_value'=>$cash_value];
		}
		return ['cash_value'=>0];
		
	}
	public function credit_order_value(){
		// Calculate outstanding balance as: Sum of all due amounts
		$sql = "SELECT SUM(due) AS outstanding_balance FROM orders WHERE due > 0";
		$stmt = $this->dbcon->connect()->prepare($sql);
		$stmt->execute() OR die($this->dbcon->connect()->error);
		$data = $stmt->fetch(PDO::FETCH_BOTH);
		$credit_value = $data['outstanding_balance'];
		// debug($credit_value);
		if($credit_value){
			return ['credit_value'=>$credit_value];
		}
		return ['credit_value'=>0];
	}
	
	public function total_stock_value(){
		// Calculate total stock value (stock × buying_price)
		$sql = "SELECT SUM(stock * buying_price) AS total_value FROM products WHERE p_status = '1'";
		$stmt = $this->dbcon->connect()->prepare($sql);
		$stmt->execute() OR die($this->dbcon->connect()->error);
		$data = $stmt->fetch(PDO::FETCH_BOTH);
		$stock_value = $data['total_value'];
		if($stock_value){
			return ['stock_value'=>$stock_value];
		}
		return ['stock_value'=>0];
	}
	
	public function total_stock_units(){
		// Calculate total units in stock
		$sql = "SELECT SUM(stock) AS total_units FROM products WHERE p_status = '1'";
		$stmt = $this->dbcon->connect()->prepare($sql);
		$stmt->execute() OR die($this->dbcon->connect()->error);
		$data = $stmt->fetch(PDO::FETCH_BOTH);
		$total_units = $data['total_units'];
		if($total_units){
			return ['total_units'=>$total_units];
		}
		return ['total_units'=>0];
	}


}

$dashboard = new Dashboard();