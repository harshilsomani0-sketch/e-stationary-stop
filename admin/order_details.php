<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

if (isset($_GET['status']) && $_GET['status'] == 'updated') {
    echo "<div class='alert alert-success'>Order status has been updated successfully.</div>";
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_orders.php");
    exit();
}

$order_id = intval($_GET['id']);

// --- Fetch main order and shipping details ---
$sql_order = "SELECT orders.*, users.full_name, users.email 
              FROM orders 
              JOIN users ON orders.user_id = users.id 
              WHERE orders.id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
if ($result_order->num_rows === 0) {
    echo "Order not found.";
    exit();
}
$order = $result_order->fetch_assoc();
$stmt_order->close();

// --- CORRECTED QUERY TO FETCH ITEMS IN THE ORDER ---
$sql_items = "SELECT oi.quantity, oi.price, oi.custom_text, p.name AS product_name
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

include_once '../includes/header.php';
?>

<div class="order-details-container">
    <h2>Order Details: #<?php echo $order['id']; ?></h2>
    
    <div class="order-details-grid">
        <div class="customer-info-box">
            <h3>Customer & Shipping Information</h3>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Shipping Name:</strong> <?php echo htmlspecialchars($order['shipping_name']); ?></p>
            <p><strong>Shipping Address:</strong><br>
                <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?><br>
                <?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_state']); ?> - <?php echo htmlspecialchars($order['shipping_pincode']); ?>
            </p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
        </div>

        <div class="order-summary-box">
            <h3>Order Summary</h3>
            <p><strong>Order Date:</strong> <?php echo date("F d, Y, g:i a", strtotime($order['created_at'])); ?></p>
            <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
            <p><strong>Grand Total:</strong> <span class="price">$<?php echo number_format($order['total_amount'], 2); ?></span></p>

            <form action="update_order_status.php" method="POST" style="margin-top: 15px;">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <div class="form-group">
                    <label for="order_status"><strong>Update Order Status:</strong></label>
                    <select name="new_status" id="order_status">
                        <option value="Pending" <?php echo ($order['order_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Shipped" <?php echo ($order['order_status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                        <option value="Delivered" <?php echo ($order['order_status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                        <option value="Canceled" <?php echo ($order['order_status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>

    <h3>Items Ordered</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price per item</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $result_items->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php echo htmlspecialchars($item['product_name']); ?>
                    <?php if (!empty($item['custom_text'])): ?>
                        <br><small style="color: #555;"><em>Personalization: "<?php echo htmlspecialchars($item['custom_text']); ?>"</em></small>
                    <?php endif; ?>
                </td>
                <td><?php echo $item['quantity']; ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
            </tr>
            <?php endwhile; $stmt_items->close(); ?>
        </tbody>
    </table>
    
    <a href="view_orders.php" class="btn" style="margin-top: 20px;">&laquo; Back to All Orders</a>
</div>

<?php include_once '../includes/footer.php'; ?>
```eof

After replacing the code in your `admin/order_details.php` file, save it and refresh the page in your browser. The error will be gone and you will see the full order details as intended.