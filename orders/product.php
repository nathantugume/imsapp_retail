<?php
public function create_customer_order($order){
		// debug($order);
		if($order['order_qty'] > $order['stock']){
			
			return ['stock'=>'Out_Of_Stock'];
		}else{
			$sql ="INSERT INTO orders(`customer_name`,`address`,`subtotal`,`gst`,`discount`,`net_total`,`paid`,`due`,`payment_method`,`order_date`) VALUES(?,?,?,?,?,?,?,?,?,?)";
			$stmt = $this->dbcon->connect()->prepare($sql);
			$stmt->bindParam(1, $order['customer_name'], PDO::PARAM_STR);
			$stmt->bindParam(2, $order['address'], PDO::PARAM_STR);
			$stmt->bindParam(3, $order['subtotal'], PDO::PARAM_INT);
			$stmt->bindParam(4, $order['gst'], PDO::PARAM_INT);
			$stmt->bindParam(5, $order['discount'], PDO::PARAM_INT);
			$stmt->bindParam(6, $order['net_total'], PDO::PARAM_INT);
			$stmt->bindParam(7, $order['paid'], PDO::PARAM_INT);
			$stmt->bindParam(8, $order['due'], PDO::PARAM_INT);
			$stmt->bindParam(9, $order['payment_method'], PDO::PARAM_STR);
			$stmt->bindParam(10,$order['order_date'], PDO::PARAM_STR);
			$stmt->execute() OR die($this->dbcon->connect()->error);

			// $invoice_id = $this->dbcon->connect()->lastInsertId();
			$sql="SELECT * FROM orders WHERE customer_name=?";
			$stmt = $this->dbcon->connect()->prepare($sql);
			$stmt->bindParam(1, $order['customer_name'], PDO::PARAM_STR);
			$stmt->execute() OR die($this->dbcon->connect()->error);
			
			$last_id = $stmt->fetch(PDO::FETCH_OBJ);
			$invoice_id = $last_id->invoice_no;
			// debug($invoice_no);
			if($invoice_id!= null){
				for($x=0; $x < count($order['price']); $x++) { 
					// debug($order['price'][$x]);

					$remaining_stock = $order['stock'][$x] - $order['order_qty'][$x];

					if($remaining_stock <= 0){
                        
					   return ['stock'=>'Out_Of_Stock'];
                   
					}else{

						$sql ="UPDATE products SET stock=? WHERE product_name=?";
						$stmt = $this->dbcon->connect()->prepare($sql);
						$stmt->bindParam(1, $remaining_stock, PDO::PARAM_INT);
						$stmt->bindParam(2, $order['product_name'][$x], PDO::PARAM_STR);
						$stmt->execute() OR die($this->dbcon->connect()->error);
					}

					$sql ="INSERT INTO `invoices`(`invoice_no`,`product_name`,`order_qty`,`price_per_item`) VALUES(?,?,?,?)";
					$stmt = $this->dbcon->connect()->prepare($sql);
					$stmt->bindParam(1, $invoice_id, PDO::PARAM_INT);
					$stmt->bindParam(2, $order['product_name'][$x], PDO::PARAM_STR);
					$stmt->bindParam(3, $order['order_qty'][$x], PDO::PARAM_INT);
					$stmt->bindParam(4, $order['price'][$x], PDO::PARAM_INT);
					$stmt->execute()  OR die($this->dbcon->connect()->error);

				}
					return ['Added'=>'Order_Added','id'=>$invoice_id];
			}

		}	
	}