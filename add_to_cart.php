<?php
include_once 'includes/db_connect.php';

// Set the response header to indicate JSON content
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $custom_text = isset($_POST['custom_text']) ? trim($_POST['custom_text']) : null;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $cart_item_id = uniqid();

    $_SESSION['cart'][$cart_item_id] = [
        'product_id'  => $product_id,
        'quantity'    => $quantity,
        'custom_text' => $custom_text
    ];

    // Calculate the new total number of items in the cart
    $item_count = count($_SESSION['cart']);

    // Send back a success response with the new item count
    echo json_encode(['status' => 'success', 'message' => 'Item added to cart!', 'item_count' => $item_count]);
    exit();

} else {
    // Send back an error response
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit();
}
?>