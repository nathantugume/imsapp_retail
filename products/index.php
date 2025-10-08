<?php

require_once("../init/init.php");

if(isset($_POST['page'])){
	$page = $_POST['page'];
}else{
	$page =1;
}
$record_per_page = 10;
$starting_point= ($page-1)*$record_per_page;

$table = '';
$product = new Product();
$rows = $product->fetch_all_products($starting_point,$record_per_page);
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

$total_records = $product->pagination_link();
$total_pages = ceil($total_records/$record_per_page);


    $table      .='<div class="row">
                        <div class="col-md-6">
                            <p class="text-muted">Showing '.($starting_point + 1).' to '.min($starting_point + $record_per_page, $total_records).' of '.$total_records.' products</p>
                        </div>
                        <div class="col-md-6">
                            <ul class="pagination pagination-sm pull-right" style="margin: 0;">';

                    if($page > 1){
                        $previous = ($page -1);
    $table      .=    '<li class="page-item prev" prev-id="'.$previous.'">
                            <a class="page-link" style="cursor:pointer; color: #007bff;">
                                <i class="fa fa-chevron-left"></i> Previous
                            </a>
                        </li>';
                    }

                for($x=1; $x<=$total_pages; $x++){
                    if($x==$page){
    $table      .=   '<li class="page-item active">
                            <a class="page-link" style="background-color: #007bff; border-color: #007bff; color: white;">'.$x.'</a>
                        </li>';
                    }else{
    $table      .=   '<li class="page-item page_no" id='.$x.'>
                            <a class="page-link" href="#" style="color: #007bff;">'.$x.'</a>
                        </li>';
                    }
                }
                    if($total_pages > $page){
                        $next = ($page +1);
    $table      .='<li class="page-item next" next-id="'.$next.'">
                            <a class="page-link" style="cursor:pointer; color: #007bff;">
                                Next <i class="fa fa-chevron-right"></i>
                            </a>
                        </li>';
                    }

    $table      .='</ul>
                        </div>
                    </div>
                </div>';

    echo $table;

}else{

    echo '<table id="product" class="table table-bordered table-striped"><td>No Data Found</td></table>';
      exit();
}