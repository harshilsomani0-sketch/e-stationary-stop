<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Basic validation
    if (isset($_POST['order_id'], $_POST['new_status'])) {
        
        $order_id = intval($_POST['order_id']);
        $new_status = $_POST['new_status'];
        
        // A good practice to validate the status against an allowed list
        $allowed_statuses = ['Pending', 'Shipped', 'Delivered', 'Canceled'];
        
        if (in_array($new_status, $allowed_statuses)) {
            
            // Prepare and execute the UPDATE statement
            $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $order_id);
            
            if ($stmt->execute()) {
                // Redirect back to the order details page with a success message
                header("Location: order_details.php?id=" . $order_id . "&status=updated");
                exit();
            } else {
                // Handle error
                echo "Error updating record: " . $conn->error;
            }
            $stmt->close();
            
        } else {
            echo "Invalid status value provided.";
        }
        
    } else {
        echo "Missing required data.";
    }
    
} else {
    // If not a POST request, redirect to the main orders page
    header("Location: view_orders.php");
    exit();
}
?>