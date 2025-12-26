<?php
include_once 'includes/db_connect.php';
header('Content-Type: application/json');

if (isset($_POST['item_id'])) {
    $item_id_to_remove = $_POST['item_id'];

    if (isset($_SESSION['cart'][$item_id_to_remove])) {
        unset($_SESSION['cart'][$item_id_to_remove]);
    }
    
    // Recalculate grand total after removal
    $grand_total = 0;
    if (!empty($_SESSION['cart'])) {
        $product_ids_in_cart = array_column($_SESSION['cart'], 'product_id');
        $product_details = [];
        $ids_string = implode(',', array_unique($product_ids_in_cart));
        $sql = "SELECT id, price FROM products WHERE id IN ($ids_string)";
        $result = $conn->query($sql);
        while ($p = $result->fetch_assoc()) {
            $product_details[$p['id']] = $p;
        }
        foreach ($_SESSION['cart'] as $cart_item) {
            $grand_total += $product_details[$cart_item['product_id']]['price'] * $cart_item['quantity'];
        }
    }

    echo json_encode([
        'status' => 'success',
        'item_count' => count($_SESSION['cart']),
        'grand_total' => number_format($grand_total, 2)
    ]);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
?>