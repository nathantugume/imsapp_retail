<?php

// Start output buffering to prevent any warnings from breaking JSON
ob_start();

require_once("../init/init.php");
include("../fpdf/fpdf.php");

// Error reporting - suppress deprecation warnings for FPDF
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 0);

// Clear any output buffer before JSON response
ob_clean();

try {
    if(!isset($_POST['pdf_id'])){
        throw new Exception("No PDF ID provided");
    }
    
    $id = $_POST['pdf_id'];
    $row = $order->generate_invoice($id);
    
    if(!$row || empty($row) || !isset($row[0]['invoice_no'])){
        throw new Exception("No invoice data found for ID: " . $id);
    }
    
    // Validate that we have order data
    if(empty($row[0]['customer_name'])){
        throw new Exception("Invalid order data for ID: " . $id);
    }
    
    $pdf = new FPDF();
    $pdf->AddPage();	
    $pdf->Rect(5, 5, 200, 287, 'D'); //For A4

    $pdf->SetFont("Arial","B", 16);
    $pdf->Cell(190,15,"Mini Price Hardware",1,1,"C");

    $pdf->SetFont("Arial",null,12);
    $pdf->SetFont("Arial","B", 12);
    $pdf->Cell(35,8,"Customer Name : ",0,0);
    $pdf->SetFont("Arial","", 12);
    $pdf->Cell(52,8,ucwords($row[0]['customer_name']),0,1);
    $pdf->Cell(35,8,"Address: ".ucwords($row[0]['address']),0,1);
    $pdf->Cell(35,8,"GST No: _________________",0,0);
    $pdf->SetY(25);
    $pdf->Cell(168,9,"Order Date  :",0,0,"R");
    $pdf->Cell(23,9,$row[0]['order_date'],0,1,"R");
    // $pdf->Cell(140);
    $pdf->Cell(168,9,"Invoice No. :",0,0,"R");

    $pdf->Cell(12,9,"SIN/".$row[0]['invoice_no'],0,1,"R");

    $pdf->SetY(49);
    $pdf->SetFont("Arial","B", 12);
    $pdf->Cell(50,8,"",0,1);
    $pdf->Cell(10,8," S.N. ",1,0,"C");
    $pdf->Cell(100,8,"Services/Product Name",1,0,"C");
    $pdf->Cell(25,8,"Quantity",1,0,"C");
    $pdf->Cell(25,8,"Price",1,0,"C");
    $pdf->Cell(30,8,"Total (UGX)",1,1,"C");

    $pdf->SetFont("Arial","", 12);
    
    // Check if there are invoice items
    $hasItems = false;
    foreach($row as $item) {
        if (!empty($item['product_name'])) {
            $hasItems = true;
            break;
        }
    }
    
    if($hasItems) {
        for($i=0; $i < count($row); $i++){ 
            if (!empty($row[$i]['product_name'])) {
                $pdf->Cell(10,7,($i+1),1,0,"C");
                $pdf->Cell(100,7,$row[$i]['product_name'],1,0,"L");
                $pdf->Cell(25,7, $row[$i]['order_qty'],1,0,"C");
                $pdf->Cell(25,7, $row[$i]['price_per_item'].".00",1,0,"R");
                $pdf->Cell(30,7, ($row[$i]['order_qty'] * $row[$i]['price_per_item']).".00",1,1,"R");
            }
        }
    } else {
        // No items in invoice, show placeholder
        $pdf->Cell(190,7,"No items found for this order",1,1,"C");
    }
    
    $pdf->SetY(65);  //box
    $pdf->Cell(160,145," ",1,0);
    $pdf->Cell(30,145," ",1,0);
    $pdf->SetY(202);
    $pdf->SetFont("Arial","B", 12);
    $pdf->Cell(160,8,"Sub Total",1,0,"R");
    $pdf->Cell(30,8,$row[0]['subtotal'].".00",1,0,"R");
    
    $pdf->SetY(210);
    $pdf->Cell(110);

    $pdf->SetFont("Arial","", 12);
    $pdf->Cell(50,8,"GST Tax ",1,0,"R");
    $pdf->SetFont("Arial","B", 12);
    $pdf->Cell(30,8,$row[0]['gst'].".00",1,1,"R");
    $pdf->Cell(110);
    $pdf->SetFont("Arial","", 12);
    $pdf->Cell(50,8,"Discount ",1,0,"R");
    $pdf->SetFont("Arial","B", 12);
    $pdf->Cell(30,8,$row[0]['discount'].".00",1,1,"R");
    $pdf->Cell(110);
    $pdf->SetFont("Arial","", 12);
    $pdf->Cell(50,8,"Net Total ",1,0,"R");
    $pdf->SetFont("Arial","B", 12);
    $pdf->Cell(30,8,$row[0]['net_total'].".00",1,1,"R");
    $pdf->Cell(110);
    $pdf->SetFont("Arial","", 12);
    $pdf->Cell(50,8,"Paid Amount ",1,0,"R");
    $pdf->SetFont("Arial","B", 12);
    $pdf->Cell(30,8,$row[0]['paid'].".00",1,1,"R");
    $pdf->Cell(110);
    $pdf->SetFont("Arial","", 12);
    $pdf->Cell(50,8,"Due Amount ",1,0,"R");
    $pdf->SetFont("Arial","B", 12);
    $pdf->Cell(30,8,$row[0]['due'].".00",1,1,"R");
    $pdf->Cell(110);
    $pdf->SetFont("Arial","", 12);
    $pdf->Cell(50,8,"Payment Method ",1,0,"R");
    $pdf->SetFont("Arial","B", 12);
    $pdf->Cell(30,8,$row[0]['payment_method'],1,1,"R");

    $pdf->SetY(255);
    $pdf->Cell(175,15," Mini Price Hardware",0,1,"R");
    $pdf->SetFont("Arial","", 12);
    $pdf->SetY(270);
    $pdf->Cell(180,5,"------------------------------------------",0,1,"R");
    $pdf->Cell(175,1,"Authorized Signature",0,1,"R");

    // Ensure Invoices directory exists
    $invoiceDir = "../Invoices/";
    if (!file_exists($invoiceDir)) {
        mkdir($invoiceDir, 0755, true);
    }
    
    $filename = "invoice_".$row[0]['customer_name'].".pdf";
    $filepath = $invoiceDir . $filename;
    
    $pdf->Output($filepath, "F");
    
    // Clear output buffer and return clean JSON
    ob_clean();
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Return success response
    echo json_encode([
        'success' => true, 
        'message' => 'PDF generated successfully',
        'filename' => $filename,
        'url' => 'Invoices/' . $filename
    ]);
    
} catch (Exception $e) {
    // Clear output buffer
    ob_clean();
    
    // Set JSON header
    header('Content-Type: application/json');
    http_response_code(500);
    
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}

// End output buffering
ob_end_flush();