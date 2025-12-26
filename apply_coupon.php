<?php
include_once 'includes/db_connect.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['coupon_code'])) {
    $coupon_code = trim($_POST['coupon_code']);
    
    // Find the coupon in the database
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->bind_param("s", $coupon_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $coupon = $result->fetch_assoc();
        
        // Calculate original cart total
        $cart_items = $_SESSION['cart'] ?? [];
        $grand_total = 0;
        if (!empty($cart_items)) {
            // This logic can be simplified if you have total in session, but DB check is safer
            $product_ids_in_cart = array_column($cart_items, 'product_id');
            $product_details = [];
            $ids_string = implode(',', array_unique($product_ids_in_cart));
            $sql = "SELECT id, price FROM products WHERE id IN ($ids_string)";
            $products_result = $conn->query($sql);
            while ($p = $products_result->fetch_assoc()) {
                $product_details[$p['id']] = $p;
            }
            foreach ($cart_items as $cart_item) {
                $grand_total += $product_details[$cart_item['product_id']]['price'] * $cart_item['quantity'];
            }
        }

        // Calculate discount
        $discount_amount = 0;
        if ($coupon['type'] == 'percentage') {
            $discount_amount = $grand_total * ($coupon['value'] / 100);
        } else { // Fixed amount
            $discount_amount = $coupon['value'];
        }
        
        // Ensure discount isn't more than the total
        $discount_amount = min($grand_total, $discount_amount);
        $new_grand_total = $grand_total - $discount_amount;

        // Store coupon in session
        $_SESSION['coupon'] = [
            'code' => $coupon['code'],
            'type' => $coupon['type'],
            'value' => $coupon['value'],
            'discount_amount' => $discount_amount
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Coupon "' . htmlspecialchars($coupon['code']) . '" applied successfully!',
            'discount_amount' => number_format($discount_amount, 2),
            'new_grand_total' => number_format($new_grand_total, 2)
        ]);

    } else {
        echo json_encode(['status' => 'error', 'message' => 'This coupon is invalid or has expired.']);
    }
    $stmt->close();
    exit();
}
?>