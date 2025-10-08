<?php


class User {
    use Exists;
    private $dbcon;
    private $table ='users';
    private $_edit_id;

    public function __construct(){
      
      $this->dbcon  = getDB();

    }

  	public function User_Login($user){
        //debug($user['email']);
        if(empty($user['email']) || empty($user['password'])){

        	return "Required_Fields";

        }elseif(!preg_match("/^[A-Za-z0-9._-]{3,}@[A_Za-z]{3,}[.]{1}[A-Za-z.]{2,6}$/", $user['email'])){

        	return "Invalid_Email";

        }else{
          //debug($user);
        	$sql = "SELECT * FROM ".$this->table." WHERE email=?";
          // debug($sql);
          $stmt = $this->dbcon->connect()->prepare($sql);
          //debug($stmt);
          	if(is_object($stmt)){
          		$stmt->bindParam(1, $user['email'], PDO::PARAM_STR);
              $stmt->execute();
          		if($stmt->rowCount() >0){
                $row = $stmt->fetch(PDO::FETCH_OBJ);
          			// debug("working");
          			// Check if password matches (handle both hashed and plain text passwords)
          			$passwordMatch = false;
          			
          			// First try password_verify for hashed passwords
          			if (password_verify($user['password'], $row->password)) {
          				$passwordMatch = true;
          			}
          			// Fallback for plain text passwords (for backward compatibility)
          			elseif ($user['password'] === $row->password) {
          				$passwordMatch = true;
          			}
          			
          			if($passwordMatch){
          				// Check if user is active
          				if($row->status == '0'){
          					return "Account_Disabled";
          				}
          				
          				$_SESSION['LOGGEDIN']=[
          					'id'   => $row->id,
          					'name' => $row->name,
          					'email'=> $row->email,
                    'role' => $row->role,
                    'vcode'=> $row->vcode,
                    'status' => $row->status
          				];
          				return "success";

          			}else{

          				return "Not_Matched";
          			}

          		}else{

                return "Not_Exists";
              }

          	}
        	
        }

  	}

    
  	public function UserRegister($user){
            
            //debug($user['name']);
            //debug($this->EmailCheck($user['email']));
            if(empty($user['name']) || empty($user['email']) || empty($user['country']) || empty($user['password']) || empty($user['cpassword'])){
               return "Required";

            }else if(!preg_match("/^[A-Za-z ]{3,50}$/", $user['name'])){

                return "Invalid_Name";

            }else if($this->UsernameCheck($user['name'])){

               return 'Name_Exists';

            }else if($this->EmailCheck($user['email'])){

               return 'Email_Exists';

            }else if(strlen($user['password']) < 6 ){

               return 'Weak_Pass';

            }else if($user['password'] !== $user['cpassword']){

               return 'Mismatch_Pass';

            }else if(!preg_match("/^[A-Za-z0-9._-]{3,}@[A_Za-z]{3,}[.]{1}[A-Za-z.]{2,6}$/", $user['email'])){

               return "Invalid_Email";

            }else{

                $role = 'User';
                $vcode = generateCode();
                $pass_hashed = password_hash($user['password'], PASSWORD_DEFAULT);
                $sql = 'INSERT INTO `users`(`name`,`email`,`password`,`role`,`vcode`,`country`) VALUES(?,?,?,?,?,?)';
                $stmt = $this->dbcon->connect()->prepare($sql);
                // debug($stmt);
                if(is_object($stmt)){
                      $stmt->bindParam(1, $user['name'], PDO::PARAM_STR);
                      $stmt->bindParam(2, $user['email'], PDO::PARAM_STR);
                      $stmt->bindParam(3, $pass_hashed, PDO::PARAM_STR);
                      $stmt->bindParam(4, $role, PDO::PARAM_STR);
                      $stmt->bindParam(5, $vcode, PDO::PARAM_STR);
                      $stmt->bindParam(6, $user['country'], PDO::PARAM_STR);
                      $stmt->execute();
                      // debug("working");
                      if($stmt->rowCount()){
                             
                            return 'success';

                      }else{

                        return "error";
                      }
                }
                return 'error';
            }
  	}
    
    public function fetch_all_records($starting_point, $record_per_page){
          //$data = null;
          //$sql = "SELECT * FROM ".$table." LIMIT $starting_point, $record_per_page";
          $sql = "SELECT * FROM ".$this->table." WHERE role='User' LIMIT $starting_point, $record_per_page";
          $stmt = $this->dbcon->connect()->prepare($sql);
          $stmt->execute();
          if($stmt->rowCount() >0){
              $data = $stmt->fetchAll(PDO::FETCH_OBJ);
              if(!empty($data)) {

                 return  $data;

              }else{

                return false;
              } 
             
          }
            return false;      
    }
    public function pagination(){
        $this->dbcon = getDB();
        $sql="SELECT * FROM ".$this->table." ORDER BY id DESC";
        $stmt= $this->dbcon->connect()->prepare($sql);
        $stmt->execute();
        $total_records = $stmt->rowCount();
        if(!empty($total_records)){
          //debug($total_records);
           return $total_records;
        }else{

           return false;
        }
        
    }

    public function Delete_User($user){
        // debug( $user['id']);
        $sql = "DELETE FROM ".$this->table." WHERE  role='User' AND id=?";
        $stmt= $this->dbcon->connect()->prepare($sql);
        $stmt->bindParam(1, $user['id'], PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()){
            return "Deleted";
        }else{
          return "Not_Deleted";
        }
    }
    public function fetch_single_record($edit_id){
        $this->_edit_id= $edit_id;
        $sql = "SELECT * FROM ".$this->table." WHERE id=?";
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->bindParam(1, $this->_edit_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($rows)){

          return $rows;
        }
        return false;
    }

    public function Update_User($user){
        //debug($user);
        if(empty($user['edit-name']) || empty($user['edit-email']) || empty($user['edit-country'])){
            
            return "Required";
        }else{

            $sql = "UPDATE ".$this->table." SET name=?, email=?, country=? WHERE id=?";
            $stmt = $this->dbcon->connect()->prepare($sql);
            $stmt->bindParam(1, $user['edit-name'], PDO::PARAM_STR);
            $stmt->bindParam(2, $user['edit-email'], PDO::PARAM_STR);
            $stmt->bindParam(3, $user['edit-country'], PDO::PARAM_STR);
            $stmt->bindParam(4, $user['edit_id'], PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                // debug("working");
                return "updated";
            }else{

                return "user_exists";
            } 
        }
      
    }

    public function reset_password($user){
          // debug($user);
          if(empty($user['oldpassword']) || empty($user['newpassword']) || empty($user['cpassword'])){
            return "Required";
          }else{
            $sql = "SELECT * FROM `users` WHERE id=?";
            $stmt = $this->dbcon->connect()->prepare($sql);
            $stmt->bindParam(1, $user['reset_id'], PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()>0){
                $row = $stmt->fetch(PDO::FETCH_OBJ);
                if(password_verify($user['oldpassword'], $row->password)){
                    $new_pass =password_hash($user['newpassword'], PASSWORD_DEFAULT);   
                    $sql="UPDATE `users` SET `password`=? WHERE id=?";
                    $stmt = $this->dbcon->connect()->prepare($sql);
                    $stmt->bindParam(1, $new_pass, PDO::PARAM_STR);
                    $stmt->bindParam(2, $user['reset_id'], PDO::PARAM_INT);
                    $update= $stmt->execute();
                    if($update){
                      return "Pass_Updated";
                    }else{
                      return "Not_Updated";
                    }
                }else{

                    return "Not_Matched";
                      
                }
            }   
        }
    }
        

}


$user = new User();

