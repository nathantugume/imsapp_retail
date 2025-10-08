<?php

require_once('../init/init.php');

	$record_per_page = 4;
	$page = '';
	$output = ''; 

	if(isset($_POST['page'])){
	    $page = $_POST['page'];
	}else{
	    $page = 1;
	}

	$starting_point = ($page-1) * $record_per_page;
    $rows = $user->fetch_all_records($starting_point, $record_per_page); 
 if(!empty($rows)){
    $output .= '
		        <table id="user_data" class="table table-bordered table-striped">
	                <thead>
					<tr>
						<th>ID</th>
						<th>Email</th>
	                    <th>Name</th>
	                    <th>Role</th>
	                    <th>Country</th>
						<th>Verification Code</th>
						<th >Status</th>
						<th width="15%" class="text-center" colspan="2">Action</th>
					</tr>
	               </thead> 
		           <tbody id="user-data"> ';
    foreach($rows as $row){
	$output .= '<tr>
			        <td>'.$row->id.'</td>
			        <td>'.ucwords($row->name).'</td>
			        <td>'.$row->email.'</td>
			        <td>'.$row->role.'</td>
			        <td>'.ucfirst($row->country).'</td>
			        <td>'.$row->vcode.'</td> 
			        <td align="center">';
			        if($row->status== 1 ){ 
	$output .=        '<span style="color:darkgreen;">Active</span>';
			        }else{
	$output .=        '<span style="color:red;">Inactive</span>';	
			        }
	$output .=		'</td>
			        <td align="center">
			        	<a href="#" edit_id='.$row->id.' id="edit_user" class="btn btn-success btn-sm edit-btn" data-toggle="modal" data-target="#editModal">Edit</a>
			        </td>
			        <td align="center">
			        	<a href="#" del_id='.$row->id.' id="del_user" class="btn btn-danger btn-sm del-btn">Delete</a>
			        </td>
			     </tr>';
    }  
    $output .= '</tbody>
	           </table>
               </div>
               <div class="float-right">';
 
	            $total_records = $user->pagination();
				$total_pages = ceil($total_records/$record_per_page);
                
			    $output .= '<ul class="pagination pull-right" style="margin-top:-15px;">';
			 	if($page > 1 ){ 
			 		$previous =$page - 1;
			        $output .= '<li class="page-item prev-link" prev='.$previous.'><a class="page-link  btn-info" style="color:white; cursor:pointer;"> Previous</a></li>';
			    }
			    for($x=1; $x<=$total_pages; $x++){ 
			    	 if($x==$page){
			    	 	$output .= '<li class="page-item"><a class"page-link" style="background:darkgray; color:white;">'.$x.'</a></li>';	
			    	       
			    	 }else{
			    	 	$output .= '<li class="page-item pagination-link" id="'.$x.'"><a class"page-link text-white" style="cursor:pointer;">'.$x.'</a></li>';	
			    	 }
			    }
			    if($total_pages > $page ){
	              $next = $page + 1;
	              $output .= '<li class="page-item next-link" next='.$next.'><a class="page-link  btn-info" style="color:white; cursor:pointer;"> Next</a></li>';
	            }
			    
 $output .= '</ul> 
             </div>
            ';

    echo $output;

}else{
	
	echo '<table id="category_data" class="table table-bordered table-striped"><td>No Data Found</td></table>';
}



?>