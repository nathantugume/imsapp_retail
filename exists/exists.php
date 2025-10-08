<?php

Trait Exists{
    private $dbcon;
    private $e_mail;
    private $_name_;
    
    public function __construct(){
    	$this->dbcon = getDB();
    }

    public function UsernameCheck($name){
    	$this->_name_ = $name;
		$sql = "SELECT * FROM users WHERE name=?";
		$stmt = $this->dbcon->connect()->prepare($sql);
		if(is_object($stmt)){
			$stmt->bindParam(1, $this->_name_, PDO::PARAM_STR);
		    $stmt->execute();
		    if($stmt->rowCount() > 0){
		    	return 1;
		    }
		    return 0;

		}
	}

    public function EmailCheck($email){
    	$this->e_mail = $email;
        $sql = "SELECT * FROM users WHERE email=?";
      	$stmt =$this->dbcon->connect()->prepare($sql);

      	if(is_object($stmt)){
            $stmt->bindParam(1, $this->e_mail, PDO::PARAM_STR);
            $stmt->execute();

            if($stmt->rowCount() >0){
                return 1;
            }
      	}
      	return 0;   
    }
    public function category_check($category){
        $sql="SELECT cat_id FROM categories WHERE category_name=?";
        $stmt = $this->dbcon->connect()->prepare($sql);
        if(is_object($stmt)){
          $stmt->bindParam(1, $category, PDO::PARAM_STR);
          $stmt->execute();
            if($stmt->rowCount()>0){
              return 1;
            }
            //return 0;
        }
        return 0;
    }
    public function brand_check($brand){
        $sql="SELECT brand_id FROM brands WHERE brand_name=?";
        $stmt = $this->dbcon->connect()->prepare($sql);
        if(is_object($stmt)){
          $stmt->bindParam(1, $brand, PDO::PARAM_STR);
          $stmt->execute();
            if($stmt->rowCount()>0){
              return 1;
            }
            //return 0;
        }
        return 0;
    }
    public function product_check($product_name){
        $sql="SELECT pid FROM products WHERE product_name=?";
        $stmt = $this->dbcon->connect()->prepare($sql);
        if(is_object($stmt)){
          $stmt->bindParam(1, $product_name, PDO::PARAM_STR);
          $stmt->execute();
            if($stmt->rowCount()>0){
              return 1;
            }
            //return 0;
        }
        return 0;
    }
	

	

}

