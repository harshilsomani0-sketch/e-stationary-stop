<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php'; // Ensures only admins can access

// Fetch all orders, joining with the users table to get the customer's name
// Orders are sorted by the newest first
$sql = "SELECT orders.id, orders.total_amount, orders.order_status, orders.created_at, users.full_name 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        ORDER BY orders.created_at DESC";
$result = $conn->query($sql);

include_once '../includes/header.php';
?>

<h2>View All Orders</h2>

<table class="admin-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                <td>
    <span class="status status-<?php echo strtolower(htmlspecialchars($row['order_status'])); ?>">
        <?php echo htmlspecialchars($row['order_status']); ?>
    </span>
</td>
                <td><?php echo date("M d, Y, g:i a", strtotime($row['created_at'])); ?></td>
                <td>
                    <a href="order_details.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No orders found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include_once '../includes/footer.php'; ?>