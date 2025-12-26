<?php
include_once 'includes/db_connect.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id'], $_POST['quantity'])) {
    $item_id = $_POST['item_id'];
    $quantity = intval($_POST['quantity']);

    // Validate quantity
    if ($quantity < 1) {
        $quantity = 1;
    }

    // Update the quantity in the session cart
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]['quantity'] = $quantity;

        // Recalculate totals to send back to the frontend
        $product_id = $_SESSION['cart'][$item_id]['product_id'];
        $product_stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $product_stmt->bind_param("i", $product_id);
        $product_stmt->execute();
        $product = $product_stmt->get_result()->fetch_assoc();
        $price = $product['price'];

        $item_subtotal = $price * $quantity;
        
        // Recalculate grand total
        $grand_total = 0;
        $product_ids_in_cart = array_column($_SESSION['cart'], 'product_id');
        $product_details = [];
        if(!empty($product_ids_in_cart)){
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
            'item_subtotal' => number_format($item_subtotal, 2),
            'grand_total' => number_format($grand_total, 2)
        ]);
        exit();
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
?>