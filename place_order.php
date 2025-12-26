<?php
include_once 'includes/db_connect.php';

// (Security checks are the same as before)
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = $_SESSION['cart'];
$shipping_name = filter_var($_POST['shipping_name'], FILTER_SANITIZE_STRING);
// (... all other shipping filter_var lines ...)
$shipping_address = filter_var($_POST['shipping_address'], FILTER_SANITIZE_STRING);
$shipping_city = filter_var($_POST['shipping_city'], FILTER_SANITIZE_STRING);
$shipping_state = filter_var($_POST['shipping_state'], FILTER_SANITIZE_STRING);
$shipping_pincode = filter_var($_POST['shipping_pincode'], FILTER_SANITIZE_STRING);
$shipping_phone = filter_var($_POST['shipping_phone'], FILTER_SANITIZE_STRING);


// --- RECALCULATE FINAL TOTAL WITH DISCOUNT ---
$subtotal = 0;
$product_ids = array_column($cart_items, 'product_id');
$products = [];
if (!empty($product_ids)) {
    // Fetch product details
    $ids_string = implode(',', array_unique($product_ids));
    $sql = "SELECT id, price FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    while ($p = $result->fetch_assoc()) { $products[$p['id']] = $p; }
    // Calculate subtotal
    foreach ($cart_items as $item) {
        if (isset($products[$item['product_id']])) {
            $subtotal += $products[$item['product_id']]['price'] * $item['quantity'];
        }
    }
}

// Apply coupon if it exists in the session
$coupon_code = $_SESSION['coupon']['code'] ?? null;
$discount_amount = $_SESSION['coupon']['discount_amount'] ?? 0;
$total_amount = $subtotal - $discount_amount;


// --- Database Transaction ---
$conn->begin_transaction();
try {
    // 1. Insert into the `orders` table (NOW WITH DISCOUNT INFO)
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, coupon_code, discount_amount, shipping_name, shipping_address, shipping_city, shipping_state, shipping_pincode, shipping_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_order->bind_param("idssdsssss", $user_id, $total_amount, $coupon_code, $discount_amount, $shipping_name, $shipping_address, $shipping_city, $shipping_state, $shipping_pincode, $shipping_phone);
    $stmt_order->execute();
    $order_id = $conn->insert_id;
    $stmt_order->close();

    // 2. Insert order items (logic is the same as before)
    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, custom_text) VALUES (?, ?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $custom_text = $item['custom_text'];
        if (isset($products[$product_id])) {
            $price = $products[$product_id]['price'];
            $stmt_items->bind_param("iiids", $order_id, $product_id, $quantity, $price, $custom_text);
            $stmt_items->execute();
        }
    }
    $stmt_items->close();

    $conn->commit();
    unset($_SESSION['cart']);
    unset($_SESSION['coupon']); // Also clear the coupon from session
    header("Location: order_success.php");
    exit();

} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    error_log($exception->getMessage()); // Log error for debugging
    header("Location: checkout.php?error=1");
    exit();
}
?>