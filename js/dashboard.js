$(document).ready(function(){

	count_records()
	load_expiry_warnings()
	function count_records(){
		$.ajax({
			url:"dashboard/dash.php",
			method:"POST",
			dataType:"JSON",
			data:{user:1},
			success:function(data){
				if(data.users){
				
				 $(".total_user").html('<h1>'+data.users+'</h1>');

				}else{
				  $(".total_user").html('<h1>00</h1>');

				}
				if(data.cat){
				
				 	$(".total_category").html('<h1>'+data.cat+'</h1>');

				}else{
				  $(".total_category").html('<h1>00</h1>');

				}
				if(data.brand){
				
				 	$(".total_brand").html('<h1>'+data.brand+'</h1>');

				}else{

				  	$(".total_brand").html('<h1>00</h1>');
				}
				if(data.item){
				
				 	$(".total_item").html('<h1>'+data.item+'</h1>');

				}else{
				  	$(".total_item").html('<h1>00</h1>');

				}
				if(data.order_value){
				
				 	$(".total_order_value").html('<h1>'+data.order_value+'</h1>');

				}else{
				  	$(".total_order_value").html('<h1>00</h1>');

				}
				if(data.cash_value){
				
				 	$(".cash_value").html('<h1>ugx '+parseFloat(data.cash_value).toLocaleString()+'</h1>');

				}else{
				  	$(".cash_value").html('<h1>ugx 0</h1>');

				}
				if(data.credit_card){
				
				 	$(".credit_value").html('<h1>ugx '+parseFloat(data.credit_card).toLocaleString()+'</h1>');

				}else{
				  	$(".credit_value").html('<h1>ugx 0</h1>');

				}
				// $(".total_category").html('<h1>'+data.cat+'</h1>');
				// $(".total_brand").html('<h1>'+data.brand+'</h1>');
				// $(".total_item").html('<h1>'+data.item+'</h1>');
				// $(".total_order_value").html('<h1>'+data.order_value+'</h1>');
				// $(".cash_value").html('<h1>'+data.cash_value+'</h1>');
				// $(".credit_value").html('<h1>'+data.credit_card+'</h1>');
				// console.log(data.credit_card);
			}
		})
	}
	
	function load_expiry_warnings(){
		$.ajax({
			url: "dashboard/get-expiry-warnings.php",
			method: "POST",
			success: function(data){
				$("#expiry-warnings").html(data);
			}
		});
	}
})