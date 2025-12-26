<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'includes/db_connect.php';

// 1. Check if User is Logged In
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Validate Order ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// 3. Fetch Order Details (Securely ensure it belongs to the logged-in user)
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    // Order not found or doesn't belong to user
    header("Location: profile.php");
    exit();
}

$order = $order_result->fetch_assoc();
$stmt->close();

// 4. Fetch Order Items
$items_stmt = $conn->prepare("SELECT oi.*, p.name, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$items_stmt->close();

// --- VISUAL TRACKING LOGIC ---
$steps = ['Pending', 'Processing', 'Shipped', 'Delivered'];
$current_status = $order['order_status'];

// Find the index (0, 1, 2, or 3)
$current_step_index = array_search($current_status, $steps);

// Handle 'Cancelled' or unknown statuses
if ($current_step_index === false) {
    $current_step_index = -1;
}

// Calculate progress percentage for the bar width
$progress_percent = 0;
if ($current_step_index >= 0) {
    $progress_percent = ($current_step_index / (count($steps) - 1)) * 100;
}

include_once 'includes/header.php';
?>

<div class="container main-content">
    
    <div style="margin-bottom: 20px;">
        <a href="profile.php" class="btn">&larr; Back to My Orders</a>
    </div>

    <h1>Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h1>
    <p style="color: var(--medium-grey);">Placed on <?php echo date("F j, Y", strtotime($order['created_at'])); ?></p>

    <?php if ($order['order_status'] != 'Cancelled'): ?>
    <div class="track-order-container">
        <h3 style="margin-top:0;">Order Status</h3>
        
        <div class="track-steps">
            <div class="track-progress-bar" style="width: <?php echo $progress_percent; ?>%;"></div>
            
            <?php foreach($steps as $index => $step_name): ?>
                <?php 
                    $class = '';
                    $icon = $index + 1; // Default number
                    
                    if ($index < $current_step_index) {
                        $class = 'completed'; // Past step
                        $icon = '✓';
                    } elseif ($index == $current_step_index) {
                        $class = 'active'; // Current step
                    }
                ?>
                <div class="step-item <?php echo $class; ?>">
                    <div class="step-circle"><?php echo $icon; ?></div>
                    <div class="step-text"><?php echo $step_name; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-danger" style="text-align: center; font-weight: bold; margin-bottom: 30px;">
            This order has been Cancelled.
        </div>
    <?php endif; ?>
    <div class="order-details-container">
        <div class="order-details-grid">
            <div class="customer-info-box">
                <h3>Shipping Address</h3>
                <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
            </div>
            
            <div class="order-summary-box">
                <h3>Payment Summary</h3>
                <p><strong>Payment Method:</strong> Cash on Delivery</p>
                <p><strong>Order Total:</strong> $<?php echo htmlspecialchars($order['total_amount']); ?></p>
                <?php if($order['discount_amount'] > 0): ?>
                    <p style="color: var(--success-color);"><strong>Discount Applied:</strong> -$<?php echo htmlspecialchars($order['discount_amount']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <h3>Items Ordered</h3>
        <div style="overflow-x: auto;">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="/assets/images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                        <?php if (!empty($item['custom_text'])): ?>
                                            <br><small style="color: var(--medium-grey);">Personalization: "<?php echo htmlspecialchars($item['custom_text']); ?>"</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total:</td>
                        <td style="font-weight: bold; color: var(--primary-color);">$<?php echo htmlspecialchars($order['total_amount']); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<?php include_once 'includes/footer.php'; ?>