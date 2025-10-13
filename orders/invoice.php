<?php

// session_start();
require_once("../init/init.php");
require_once("../config/branding.php");
include("../fpdf/fpdf.php");

// Suppress deprecation warnings for FPDF
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// Get branding settings
$businessName = Branding::getBusinessName();
$businessAddress = Branding::get('business_address', 'Kampala, Uganda');
$businessPhone = Branding::get('business_phone', '+256 XXX XXXXXX');
$currencySymbol = strtoupper(Branding::get('currency_symbol', 'UGX'));

// debug($_POST);

if(isset($_POST['order_date'])){
	$pdf = new FPDF();
	$pdf->AddPage();

	$pdf->Rect(5, 5, 200, 287, 'D'); //For A4

	$pdf->SetFont("Arial","B", 16);
	$pdf->Cell(190,10,$businessName,1,1,"C");
	
	// Add business contact info
	$pdf->SetFont("Arial","", 10);
	$pdf->Cell(190,5,$businessAddress . " | Tel: " . $businessPhone,1,1,"C");

		$pdf->SetFont("Arial",null,12);
		$pdf->SetFont("Arial","B", 12);
		$pdf->Cell(35,8,"Customer Name : ",0,0);
		$pdf->SetFont("Arial","", 12);
		$pdf->Cell(52,8,ucwords($_POST['customer_name']),0,1);
		$pdf->Cell(35,8,"Address: ".ucwords($_POST['address']),0,1);
		$pdf->Cell(35,8,"GST No: _________________",0,0);
		$pdf->SetY(25);
		$pdf->Cell(168,9,"Order Date  :",0,0,"R");
		$pdf->Cell(23,9,$_POST['order_date'],0,1,"R");
		// $pdf->Cell(140);
		$pdf->Cell(168,9,"Invoice No. :",0,0,"R");

		$pdf->Cell(12,9,"SIN/".$_POST['id'],0,1,"R");

		$pdf->SetY(49);
		$pdf->SetFont("Arial","B", 12);
		$pdf->Cell(50,8,"",0,1);
	$pdf->Cell(10,8," S.N. ",1,0,"C");
	$pdf->Cell(100,8,"Services/Product Name",1,0,"C");
	$pdf->Cell(25,8,"Quantity",1,0,"C");
	$pdf->Cell(25,8,"Price",1,0,"C");
	$pdf->Cell(30,8,"Total (".$currencySymbol.")",1,1,"C");

		$pdf->SetFont("Arial","", 12);
		for($i=0; $i < count($_POST['price']); $i++){ 
		  	$pdf->Cell(10,7,($i+1),1,0,"C");
		  	$pdf->Cell(100,7,$_POST['product_name'][$i],1,0,"L");
		  	$pdf->Cell(25,7, $_POST['order_qty'][$i],1,0,"C");
		  	$pdf->Cell(25,7, $_POST['price'][$i].".00",1,0,"R");
		  	$pdf->Cell(30,7, $_POST['order_qty'][$i] * $_POST['price'][$i].".00",1,1,"R");
		  	
		}
		$pdf->SetY(65);  //box
		$pdf->Cell(160,145," ",1,0);
		$pdf->Cell(30,145," ",1,0);
		$pdf->SetY(202);
		$pdf->SetFont("Arial","B", 12);
		$pdf->Cell(160,8,"Sub Total",1,0,"R");
		$pdf->Cell(30,8,$_POST['subtotal'].".00",1,0,"R");
		
		$pdf->SetY(210);
		$pdf->Cell(110);

		$pdf->SetFont("Arial","", 12);
		$pdf->Cell(50,8,"GST Tax ",1,0,"R");
		$pdf->SetFont("Arial","B", 12);
		$pdf->Cell(30,8,$_POST['gst'].".00",1,1,"R");
		$pdf->Cell(110);
		$pdf->SetFont("Arial","", 12);
		$pdf->Cell(50,8,"Discount ",1,0,"R");
		$pdf->SetFont("Arial","B", 12);
		$pdf->Cell(30,8,$_POST['discount'].".00",1,1,"R");
		$pdf->Cell(110);
		$pdf->SetFont("Arial","", 12);
		$pdf->Cell(50,8,"Net Total ",1,0,"R");
		$pdf->SetFont("Arial","B", 12);
		$pdf->Cell(30,8,$_POST['net_total'].".00",1,1,"R");
		$pdf->Cell(110);
		$pdf->SetFont("Arial","", 12);
		$pdf->Cell(50,8,"Paid Amount ",1,0,"R");
		$pdf->SetFont("Arial","B", 12);
		$pdf->Cell(30,8,$_POST['paid'].".00",1,1,"R");
	  	$pdf->Cell(110);
	  	$pdf->SetFont("Arial","", 12);
		$pdf->Cell(50,8,"Due Amount ",1,0,"R");
		$pdf->SetFont("Arial","B", 12);
		$pdf->Cell(30,8,$_POST['due'].".00",1,1,"R");
		$pdf->Cell(110);
		$pdf->SetFont("Arial","", 12);
		$pdf->Cell(50,8,"Payment Method ",1,0,"R");
		$pdf->SetFont("Arial","B", 12);
		$pdf->Cell(30,8,$_POST['payment_method'],1,1,"R");

	$pdf->SetY(255);
	$pdf->Cell(175,15," ".$businessName,0,1,"R");
		$pdf->SetFont("Arial","", 12);
		$pdf->SetY(270);
		$pdf->Cell(180,5,"------------------------------------------",0,1,"R");
		$pdf->Cell(175,1,"Authorized Signature",0,1,"R");

		$pdf->Output("../Invoices/invoice_".$_POST['customer_name'].".pdf","F");

		$bill = $pdf->Output();

		echo $bill;

}