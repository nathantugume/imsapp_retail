<?php

require_once("../init/init.php");

$record_per_page = 5;


if (isset($_POST['page'])) {
    $page = $_POST['page'];
}else{
    $page = 1;
}
$starting_point = ($page -1)*$record_per_page;
$rows = $order->fetch_all_the_orders($starting_point,$record_per_page);
// debug($rows);
// echo json_encode($rows);
$table= '';
if(!empty($rows)){

	$table  .='<table id="product_data" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Total Amount</th>
                        <th>Paid Amount</th>
                        <th>Payment</th>
                        <th>Order Date</th>
                        <th class="text-center" colspan="3" width="1%">Action</th> 
                    </tr>
               </thead>
               <tbody>';
foreach($rows as $row){
    $table          .='<tr>
                        <td>'.$row->invoice_no.'</td>
                        <td>'.ucwords($row->customer_name).'</td>
                        <td class="text-right">'.$row->net_total.'.00</td>
                        <td class="text-right">'.$row->paid.'.00</td>
                        <td class="text-center">'.$row->payment_method.'</td>
                        <td class="text-center">'.$row->order_date.'</td>
                        <td class="text-center">
						    <button pdf-id="'.$row->invoice_no.'" name="'.$row->customer_name.'" class="btn btn-danger btn-sm pdf-btn" title="Generate Invoice PDF">
						        <i class="fas fa-file-pdf"></i> Invoice
						    </button>
						</td>
                        <td class="text-center">
						    <button view-id="'.$row->invoice_no.'" class="btn btn-primary btn-sm view-btn" data-toggle="modal" data-target="#Order-View-Modal" title="View Order Details">
						        <i class="fas fa-eye"></i> View
						    </button>
						</td>
                        <td class="text-center">
						    <button del-id="'.$row->invoice_no.'" id="del" class="btn btn-danger btn-sm del-order-btn" title="Delete Order">
						        <i class="fas fa-trash"></i> Delete
						    </button>
						</td>
                    </tr>';
}
	$table .='</tbody>
          </table>
          </div>';

    $toatal_records = $order->pagination_link();
    $total_pages = ceil($toatal_records/$record_per_page);

    $table       .='<ul class="pagination pull-right" style="margin-top:-15px;">';

                    if($page > 1){
                        $previous = ($page -1);
    $table       .=    '<li class="page-item prev" prev-id="'.$previous.'"><a class="page-link btn-info" style="cursor:pointer; color:white;">Previous</a></li>';

                    }


                for($x=1; $x<=$total_pages; $x++){
                    if($x==$page){

    $table      .=   '<li class="page-item " ><a class="page-link" style="background:darkgray; color:white;">'.$x.'</a></li>';

                    }else{
    $table      .=   '<li class="page-item page_no" id='.$x.'><a class="page-link" href="#">'.$x.'</a></li>';

                    }
                }
                    if($total_pages > $page){
                        $next = ($page +1);
    $table      .=  '<li class="page-item next" next-id="'.$next.'"><a class="page-link btn-info" style="cursor:pointer; color:white;">Next</a></li>';
                    }

    $table      .='</ul>
                </div>';

    echo $table;



}else{



	echo '<table id="product" class="table table-bordered table-striped"><td>No Data Found</td></table>';
    
    exit();
}

// <td>
//     <button del-id="'.$row->invoice_no.'" class="btn btn-danger btn-sm del-btn" id="del">Delete</button>
// </td>