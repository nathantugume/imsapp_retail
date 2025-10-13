<?php
require_once("../init/init.php");

// Remove pagination - DataTables will handle it
$pagination ='';

$rows = $brand->fetch_all_brands($_POST, 0, 10000); // Fetch all brands
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
            </table>';

// No custom pagination - DataTables handles everything
echo $pagination;

}else{

      echo '<table id="category_data" class="table table-bordered table-striped"><td>No Data Found</td></table>';
      exit();
}
