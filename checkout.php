<?php
session_start();
include 'includes/db_connect.php';

// 1. Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$total_amount = 0;
$order_items = [];

// --- 2. PREPARE CART DATA FROM DATABASE ---
$product_ids = array_keys($_SESSION['cart']);
$clean_ids = array_map('intval', $product_ids);
$clean_ids = array_filter($clean_ids);

if (!empty($clean_ids)) {
    $ids_string = implode(',', $clean_ids);
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids_string)");

    while ($product = $result->fetch_assoc()) {
        $qty = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $qty;
        $total_amount += $subtotal;

        // Store item details for later processing
        $order_items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}

// --- 3. HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method'];

    // A. Insert the MAIN ORDER
    // We default 'user_id' to 0 if not logged in (Guest Checkout)
    $uid_to_save = $user_id ? $user_id : 0; 
    
    // Note: Ensure your 'orders' table has the 'status' column!
    $sql = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'Pending', NOW())";
    
    $stmt = $conn->prepare($sql);
    // 'i' = integer (user_id), 'd' = double (total_amount)
    $stmt->bind_param("id", $uid_to_save, $total_amount);
    
    if ($stmt->execute()) {
        // Get the new Order ID
        $order_id = $conn->insert_id;

        // B. Insert ORDER ITEMS (The Loop Fix)
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($order_items as $item) {
            // FIX: We must use $item_stmt (not $stmt) and pull data from $item array
            // Types: i (order_id), i (product_id), i (quantity), d (price)
            $item_stmt->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['price']);
            $item_stmt->execute();
        }

        // C. Success! Clear Cart and Redirect
        unset($_SESSION['cart']);
        header("Location: order_success.php?id=" . $order_id);
        exit();

    } else {
        $error = "Error placing order: " . $conn->error;
    }
}

include 'includes/header.php';
?>

<div class="container" style="margin-top: 40px; margin-bottom: 40px; max-width: 1000px;">
    <h1>Checkout</h1>
    
    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form action="" method="POST">
        <div class="checkout-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
            
            <div class="shipping-info">
                <h3>Shipping Details</h3>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="row" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Zip Code</label>
                        <input type="text" name="zip" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 15px; margin-top: 15px;">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <h3>Payment Method</h3>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="radio" name="payment_method" value="cod" checked> Cash on Delivery (COD)
                    </label>
                    <label style="display: block; color: #999;">
                        <input type="radio" name="payment_method" value="card" disabled> Credit Card (Coming Soon)
                    </label>
                </div>
            </div>

            <div class="order-summary" style="background: #f9f9f9; padding: 30px; border-radius: 10px; height: fit-content;">
                <h3 style="margin-top: 0;">Order Summary</h3>
                <hr style="border: 0; border-top: 1px solid #ddd;">
                
                <div class="items-list" style="margin-bottom: 20px;">
                    <?php foreach ($order_items as $item): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.95rem;">
                            <span>
                                <?php echo htmlspecialchars($item['name']); ?> 
                                <span style="color: #888;">x<?php echo $item['qty']; ?></span>
                            </span>
                            <span style="font-weight: 600;">$<?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr style="border: 0; border-top: 1px solid #ddd;">
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 1.2rem; font-weight: bold;">
                    <span>Total</span>
                    <span style="color: var(--primary-color);">$<?php echo number_format($total_amount, 2); ?></span>
                </div>

                <button type="submit" name="place_order" class="btn btn-primary btn-block" style="width: 100%; padding: 15px;">Place Order</button>
                <a href="cart.php" style="display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #666;">Return to Cart</a>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>