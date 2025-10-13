<?php

require_once("../init/init.php");

// Remove pagination - DataTables will handle it
$table = '';
$product = new Product();
$rows = $product->fetch_all_products(0, 10000); // Fetch all products for DataTables
// debug($rows);
// echo json_encode($rows);
if(!empty($rows)){
$table  .='<table id="product_data" class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Brand Name</th>
                        <th>Product Name</th>
                        <th>Stock</th>
                        <th>Buying Price</th>
                        <th>Selling Price</th>
                        <th>Status</th>
                        <th class="text-center" colspan="4" width="25%">Actions</th> 
                    </tr>
               </thead>
               <tbody>';
foreach($rows as $row){
$table             .='<tr>
                        <td><strong>'.$row->pid.'</strong></td>
                        <td><span class="badge badge-secondary">'.ucwords($row->category_name).'</span></td>
                        <td><strong>'.ucwords($row->brand_name).'</strong></td>
                        <td><strong>'.ucwords($row->product_name).'</strong></td>
                        <td><span class="badge badge-info">'.$row->stock.'</span></td>
                        <td><span class="text-muted">ugx'.number_format($row->buying_price, 2).'</span></td>
                        <td><span class="text-success"><strong>ugx'.number_format($row->price, 2).'</strong></span></td>
                        <td>';
                        if($row->p_status==1){
$table              .=  '<span class="badge badge-success">Active</span>';
                    	}else{
$table              .=  '<span class="badge badge-danger">Inactive</span>';              	
                    	}
$table              .=  '</td>
                        <td>
                        <button stock-id="'.$row->pid.'" class="btn btn-success btn-sm stock-btn" data-toggle="modal" data-target="#Stock-Modal" title="Add Stock">
                            <i class="fa fa-plus"></i> Stock
                        </button>
                        </td>
                        <td>
						<button view-id="'.$row->pid.'" class="btn btn-primary btn-sm view-btn" id="view" data-toggle="modal" data-target="#Product-View-Modal" title="View Details">
							<i class="fa fa-eye"></i> View
						</button>
						</td>
                        <td>
                            <button edit-id="'.$row->pid.'" class="btn btn-info btn-sm edit-btn" id="edit" data-toggle="modal" data-target="#UpdateProductModal" title="Edit Product">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                        </td>
                        <td>
                        	<button del-id="'.$row->pid.'" class="btn btn-danger btn-sm del-btn" id="del" title="Delete Product">
                                <i class="fa fa-trash"></i> Delete
                        	</button>
                        </td>
                    </tr>';
}
$table      .='</tbody>
            </table>';

// No custom pagination - DataTables handles everything
echo $table;

}else{

    echo '<table id="product" class="table table-bordered table-striped"><td>No Data Found</td></table>';
      exit();
}