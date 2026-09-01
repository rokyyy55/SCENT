<?php
// app/views/order/confirmation.php - Debug version
?>
<div class="container my-5">
    <div class="card">
        <div class="card-body">
            <h1>Order Confirmation</h1>
            <p>Order ID: <?= isset($order) ? $order->order_id : 'Not available' ?></p>
            
            <!-- Debug information -->
            <div class="alert alert-info">
                <p>Debug info:</p>
                <pre><?php 
                    echo "Order data: ";
                    var_dump(isset($order) ? $order : 'No order data');
                    
                    echo "\nOrder items: ";
                    var_dump(isset($order_items) ? count($order_items) : 'No items data');
                ?></pre>
            </div>
        </div>
    </div>
</div>