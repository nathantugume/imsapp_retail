<?php

require_once("../init/init.php");

if(isset($_POST['add_row'])){ 

  $rows = $order->fetch_all_products();  ?>
      <tr>
          <td class="form-control form-control-sm"><b class="serial_no">1</b></td>
          <td>
              <select name="pid[]" class="form-control form-control-sm pid" id="product">
                <option value="">Select Product</option> 
                <?php foreach($rows as $row) {  ?>
                    <option value="<?php echo $row->pid;  ?>"><?php echo ucwords($row->product_name); ?></option> 
                <?php  } ?>
              </select>
          </td>
          <td><input type="text" name="stock[]" class="form-control form-control-sm text-center stock" readonly></td>
          <td><input type="text" name="order_qty[]" class="form-control form-control-sm text-center order_qty"></td>
          <td><input type="text" name="price[]" class="form-control form-control-sm text-right  price" readonly></td>
          <td><input type="hidden" name="product_name[]" class="form-control form-control-sm product_name"></td>
          <td class="form-control form-control-sm text-right"><span class="total_amount">0</span>.00</td>
      </tr>
      <span id="q-error"></span>


<?php 	   
	exit;

}



       