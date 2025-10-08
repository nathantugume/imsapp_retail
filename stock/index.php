<?php
require_once('../init/init.php');

$userRole = $_SESSION['LOGGEDIN']['role'];
$output = '';

// Get reconciliations based on user role
if($userRole === 'Master'){
    $reconciliations = $stockReconciliation->get_all_reconciliations();
} else {
    $reconciliations = $stockReconciliation->get_pending_reconciliations();
}

if(!empty($reconciliations)){
    $output .= '
        <table id="reconciliation_data" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>System Stock</th>
                    <th>Physical Count</th>
                    <th>Difference</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Created Date</th>';
    
    if($userRole === 'Master'){
        $output .= '<th>Approved By</th>
                    <th>Approved Date</th>
                    <th width="15%" class="text-center">Action</th>';
    }
    
    $output .= '</tr>
            </thead>
            <tbody>';
    
    foreach($reconciliations as $reconciliation){
        $difference = $reconciliation->difference;
        $difference_class = $difference > 0 ? 'text-success' : ($difference < 0 ? 'text-danger' : 'text-info');
        $difference_text = $difference > 0 ? '+' . $difference : $difference;
        
        $status_class = '';
        $status_text = '';
        switch($reconciliation->status){
            case 'pending':
                $status_class = 'warning';
                $status_text = 'Pending';
                break;
            case 'approved':
                $status_class = 'success';
                $status_text = 'Approved';
                break;
            case 'rejected':
                $status_class = 'danger';
                $status_text = 'Rejected';
                break;
        }
        
        $output .= '<tr>
            <td>' . $reconciliation->id . '</td>
            <td>' . $reconciliation->product_name . '</td>
            <td>' . $reconciliation->system_stock . '</td>
            <td>' . $reconciliation->physical_count . '</td>
            <td class="' . $difference_class . '"><strong>' . $difference_text . '</strong></td>
            <td><span class="label label-' . $status_class . '">' . $status_text . '</span></td>
            <td>' . $reconciliation->created_by_name . '</td>
            <td>' . date('Y-m-d H:i', strtotime($reconciliation->reconciliation_date)) . '</td>';
        
        if($userRole === 'Master'){
            $output .= '<td>' . ($reconciliation->approved_by_name ?? '-') . '</td>
                        <td>' . ($reconciliation->approved_at ? date('Y-m-d H:i', strtotime($reconciliation->approved_at)) : '-') . '</td>
                        <td align="center">';
            
            if($reconciliation->status === 'pending'){
                $output .= '<a href="#" reconciliation_id="' . $reconciliation->id . '" class="btn btn-warning btn-sm approve-btn" data-toggle="modal" data-target="#approvalModal">Review</a>';
            } else {
                $output .= '<span class="text-muted">Completed</span>';
            }
            
            $output .= '</td>';
        }
        
        $output .= '</tr>';
    }
    
    $output .= '</tbody>
        </table>';
    
    echo $output;
} else {
    echo '<div class="alert alert-info text-center">No stock reconciliations found.</div>';
}
?>







