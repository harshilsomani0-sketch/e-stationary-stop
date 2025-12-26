<?php
include_once 'includes/header.php';

// 1. Check if the user is logged in. If not, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Get the user's ID from the session.
$user_id = $_SESSION['user_id'];

// 3. Fetch the user's details from the database.
$stmt = $conn->prepare("SELECT full_name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// 4. --- NEW --- Fetch the user's order history.
$order_stmt = $conn->prepare("SELECT id, total_amount, order_status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

?>

<div class="profile-container">
    <div class="profile-header">
        <h1>My Profile</h1>
        <p>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
    </div>

    <div class="profile-details">
        <h3>Account Details</h3>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
        <p><strong>Email Address:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Member Since:</strong> <?php echo date("F d, Y", strtotime($user['created_at'])); ?></p>
    </div>

    <div class="profile-orders">
        <h3>Order History</h3>
        <?php if ($order_result->num_rows > 0): ?>
            <table class="order-history-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $order_result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo date("M d, Y", strtotime($order['created_at'])); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <span class="status status-<?php echo strtolower(htmlspecialchars($order['order_status'])); ?>">
                                <?php echo htmlspecialchars($order['order_status']); ?>
                            </span>
                        </td>
                        <td><a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn">View Details</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not placed any orders yet.</p>
        <?php endif; $order_stmt->close(); ?>
    </div>
    
    <div class="profile-actions">
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

</div>

<?php include_once 'includes/footer.php'; ?>