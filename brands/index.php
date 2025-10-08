<?php
require_once("../init/init.php");



$record_per_page = 5;
//$starting_point = 0;
$pagination ='';

if (isset($_POST['page'])) {
	$page = $_POST['page'];
}else{
	$page = 1;
}
$starting_point = ($page -1) * $record_per_page;

$rows = $brand->fetch_all_brands($_POST,$starting_point,$record_per_page);
// debug($rows);
if($rows!=''){
$pagination .='<table id="brand_data" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Brand Name</th>
                        <th class="text-center">Status</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>';
foreach ($rows as $row) {
$pagination .=       '<tr>
	                    <td>'.$row->brand_id.'</td>
	                    <td>'.ucwords($row->brand_name).'</td>
	                    <td class="text-center">';
	            			if($row->b_status==1){
$pagination .=	        		'<span style="color:darkgreen;">Active</span>';
		               		}else{
$pagination .=   				'<span style="color:red;">Inactive</span>';
		               		}
$pagination .=          '</td>
           				<td class="text-center">
			        		<button edit_id='.$row->brand_id.' id="edit" class="btn btn-info btn-sm edit-brand-btn" data-toggle="modal" data-target="#UpdateBrandModal" >Edit</button> 
			        	</td>
			        	<td class="text-center">
			        		<button del_id='.$row->brand_id.' id="del" class="btn btn-danger btn-sm del-brand-btn">Delete</button>
			        	</td>
                    </tr>';
}
$pagination .= '</tbody>
            </table>
            </div>';

$total_records = $brand->pagination_link();
$total_pages = ceil($total_records/$record_per_page);
//debug($total_pages);
$pagination .=	'<ul class="pagination pull-right" style="margin-top:-15px;">';

					if($page >1){
						$previous = ($page-1);

$pagination .=		'<li class="page-item prev-link" prev-id="'.$previous.'"><a href="#" class="page-link btn-info" style="cursor:pointer;color:white;">Previous</a></li>';
					}
					for($x=1; $x<=$total_pages; $x++){
						if($x==$page){
$pagination .=				'<li class="page-item"><a class="page-link" style="background:darkgray; color:white;">'.$x.'</a></li>';
						}else{
$pagination .=				'<li class="page-item paging-link" id="'.$x.'"><a class="page-link text-white" style="cursor:pointer;">'.$x.'</a></li>';
						}
					}
					if($total_pages > $page){
						$next = ($page + 1);
$pagination .=			'<li class="page-item next-link" next-id="'.$next.'"><a  class="page-link btn-info" style="cursor:pointer; color:white;">Next</a></li>';
					}

$pagination .=	'</ul>
				</div>';

echo $pagination;

}else{

      echo '<table id="category_data" class="table table-bordered table-striped"><td>No Data Found</td></table>';
      exit();
}
