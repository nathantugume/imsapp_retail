// Efficient order details loading with preloaded data
$(document).ready(function(){
    
    // Method 1: Instant loading with preloaded data
    $(document).on("click", ".view-btn-efficient", function(){
        var invoice_no = $(this).data("invoice");
        var orderData = window.orderDetails[invoice_no];
        
        if(orderData) {
            // Clear previous data
            $("#order-products-tbody").empty();
            
            // Populate order details instantly (no AJAX call needed)
            $("#invcno").html(orderData.invoice_no);
            $("#cn").html('<span style="text-transform:capitalize;">' + orderData.customer_name + '</span>');
            $("#add").html('<span style="text-transform:capitalize;">' + orderData.address + '</span>');
            $("#sbt").html(orderData.subtotal + '.00');
            $("#dis").html(orderData.discount + '.00');
            $("#ntt").html(orderData.net_total + '.00');
            $("#pd").html(orderData.paid + '.00');
            $("#due").html(orderData.due + '.00');
            $("#pm").html(orderData.payment_method);
            $("#od").html(orderData.order_date);
            $(".total").html(orderData.net_total + '.00');
            
            // Build products table
            var productRows = '';
            orderData.products.forEach(function(product) {
                productRows += '<tr>' +
                    '<td>' + product.id + '</td>' +
                    '<td>' + product.product_name + '</td>' +
                    '<td class="text-center">' + product.order_qty + '</td>' +
                    '<td class="text-right">' + product.price_per_item + '.00</td>' +
                    '</tr>';
            });
            
            $("#order-products-tbody").html(productRows);
            $("#Order-View-Modal").modal("show");
        }
    });
    
    // Method 2: Cached AJAX with localStorage
    $(document).on("click", ".view-btn-cached", function(){
        var invoice_no = $(this).attr("view-id");
        var cacheKey = 'order_' + invoice_no;
        var cachedData = localStorage.getItem(cacheKey);
        
        if(cachedData) {
            // Use cached data
            displayOrderDetails(JSON.parse(cachedData));
            $("#Order-View-Modal").modal("show");
        } else {
            // Fetch and cache
            $.ajax({
                url: "orders/view.php",
                method: "POST",
                dataType: "JSON",
                data: {view_id: invoice_no},
                success: function(data) {
                    if(data && data.length > 0) {
                        // Cache for 5 minutes
                        var cacheData = {
                            data: data,
                            timestamp: Date.now()
                        };
                        localStorage.setItem(cacheKey, JSON.stringify(cacheData));
                        displayOrderDetails(data);
                        $("#Order-View-Modal").modal("show");
                    }
                }
            });
        }
    });
    
    // Method 3: Optimized single query approach
    $(document).on("click", ".view-btn-optimized", function(){
        var invoice_no = $(this).attr("view-id");
        
        $.ajax({
            url: "orders/view_optimized.php",
            method: "POST",
            dataType: "JSON",
            data: {invoice_no: invoice_no},
            success: function(response) {
                if(response.success) {
                    displayOptimizedOrderDetails(response.order, response.products);
                    $("#Order-View-Modal").modal("show");
                }
            }
        });
    });
    
    function displayOrderDetails(data) {
        $("#order-products-tbody").empty();
        $("#invcno, #cn, #add, #sbt, #dis, #ntt, #pd, #due, #pm, #od, .total").html('');
        
        if(data && data.length > 0) {
            // Set order details
            $("#invcno").html(data[0].invoice_no);
            $("#cn").html('<span style="text-transform:capitalize;">' + data[0].customer_name + '</span>');
            $("#add").html('<span style="text-transform:capitalize;">' + data[0].address + '</span>');
            $("#sbt").html(data[0].subtotal + '.00');
            $("#dis").html(data[0].discount + '.00');
            $("#ntt").html(data[0].net_total + '.00');
            $("#pd").html(data[0].paid + '.00');
            $("#due").html(data[0].due + '.00');
            $("#pm").html(data[0].payment_method);
            $("#od").html(data[0].order_date);
            $(".total").html(data[0].net_total + '.00');
            
            // Build product list
            var productRows = '';
            data.forEach(function(item) {
                if(item.id && item.product_name) {
                    productRows += '<tr>' +
                        '<td>' + item.id + '</td>' +
                        '<td>' + item.product_name + '</td>' +
                        '<td class="text-center">' + item.order_qty + '</td>' +
                        '<td class="text-right">' + item.price_per_item + '.00</td>' +
                        '</tr>';
                }
            });
            
            $("#order-products-tbody").html(productRows);
        }
    }
    
    function displayOptimizedOrderDetails(order, products) {
        $("#order-products-tbody").empty();
        
        // Set order details
        $("#invcno").html(order.invoice_no);
        $("#cn").html('<span style="text-transform:capitalize;">' + order.customer_name + '</span>');
        $("#add").html('<span style="text-transform:capitalize;">' + order.address + '</span>');
        $("#sbt").html(order.subtotal + '.00');
        $("#dis").html(order.discount + '.00');
        $("#ntt").html(order.net_total + '.00');
        $("#pd").html(order.paid + '.00');
        $("#due").html(order.due + '.00');
        $("#pm").html(order.payment_method);
        $("#od").html(order.order_date);
        $(".total").html(order.net_total + '.00');
        
        // Build product list
        var productRows = '';
        products.forEach(function(product) {
            productRows += '<tr>' +
                '<td>' + product.id + '</td>' +
                '<td>' + product.product_name + '</td>' +
                '<td class="text-center">' + product.order_qty + '</td>' +
                '<td class="text-right">' + product.price_per_item + '.00</td>' +
                '</tr>';
        });
        
        $("#order-products-tbody").html(productRows);
    }
    
    // Clear expired cache on page load
    clearExpiredCache();
    
    function clearExpiredCache() {
        var keys = Object.keys(localStorage);
        var fiveMinutes = 5 * 60 * 1000;
        
        keys.forEach(function(key) {
            if(key.startsWith('order_')) {
                var data = JSON.parse(localStorage.getItem(key));
                if(data && data.timestamp && (Date.now() - data.timestamp > fiveMinutes)) {
                    localStorage.removeItem(key);
                }
            }
        });
    }
});
