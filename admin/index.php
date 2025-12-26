<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

// --- 1. Fetch Dashboard Statistics ---

// Calculate Total Revenue (only from orders marked as 'Delivered')
$revenue_stmt = $conn->query("SELECT SUM(total_amount) as total_revenue FROM orders WHERE order_status = 'Delivered'");
$total_revenue = $revenue_stmt->fetch_assoc()['total_revenue'] ?? 0;

// Count Total Orders
$orders_stmt = $conn->query("SELECT COUNT(id) as total_orders FROM orders");
$total_orders = $orders_stmt->fetch_assoc()['total_orders'] ?? 0;

// Count Total Customers (excluding admins)
$customers_stmt = $conn->query("SELECT COUNT(id) as total_customers FROM users WHERE is_admin = 0");
$total_customers = $customers_stmt->fetch_assoc()['total_customers'] ?? 0;


// --- 2. Fetch Recent Orders ---
$recent_orders_sql = "SELECT o.id, u.full_name, o.total_amount, o.created_at, o.order_status
                      FROM orders o
                      JOIN users u ON o.user_id = u.id
                      ORDER BY o.created_at DESC
                      LIMIT 5";
$recent_orders_result = $conn->query($recent_orders_sql);

$low_stock_sql = "SELECT id, name, stock_quantity FROM products 
                  WHERE stock_quantity <= 5 
                  ORDER BY stock_quantity ASC 
                  LIMIT 10";
$low_stock_result = $conn->query($low_stock_sql);


include_once '../includes/header.php';
?>

<div class="admin-dashboard">
    <h2>Admin Dashboard</h2>

    <div class="dashboard-stats">
        <div class="stat-card">
            <h4>Total Revenue</h4>
            <p>$<?php echo number_format($total_revenue, 2); ?></p>
        </div>
        <div class="stat-card">
            <h4>Total Orders</h4>
            <p><?php echo $total_orders; ?></p>
        </div>
        <div class="stat-card">
            <h4>Total Customers</h4>
            <p><?php echo $total_customers; ?></p>
        </div>
    </div>

    <div class="dashboard-main-3col">
    <div class="quick-links">
        <h3>Quick Links</h3>
    <ul>
        <li><a href="product_form.php" class="btn">Add New Product</a></li>
        <li><a href="manage_products.php" class="btn">Manage Products</a></li>
        <li><a href="category_form.php" class="btn">Add New Category</a></li>
        <li><a href="manage_categories.php" class="btn">Manage Categories</a></li>
        <li><a href="view_orders.php" class="btn">View All Orders</a></li>
        <li><a href="manage_showcase.php" class="btn">Manage Showcase</a></li>
        <li><a href="manage_coupons.php" class="btn">Manage Coupons</a></li>
        <!-- ADD THIS LINE VVV -->
        <li><a href="manage_users.php" class="btn">Manage Users</a></li>
         <li><a href="reports.php" class="btn">Sales Reports</a></li>
    </ul>

        </div>
        <!-- NEW: LOW STOCK WIDGET -->
    <div class="low-stock-widget">
        <h3>Stock Alerts (5 or less)</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Left</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($low_stock_result->num_rows > 0): ?>
                    <?php while($item = $low_stock_result->fetch_assoc()): ?>
                    <tr class="<?php echo ($item['stock_quantity'] == 0) ? 'stock-empty' : 'stock-low'; ?>">
                        <td><a href="product_form.php?edit_id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></td>
                        <td><strong><?php echo $item['stock_quantity']; ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2">All stock levels are healthy!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
        <div class="recent-orders">
        <h3>Recent Orders</h3>
        <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_orders_result->num_rows > 0): ?>
                        <?php while($order = $recent_orders_result->fetch_assoc()): ?>
                        <tr>
                            <td><a href="order_details.php?id=<?php echo $order['id']; ?>">#<?php echo $order['id']; ?></a></td>
                            <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status status-<?php echo strtolower(htmlspecialchars($order['order_status'])); ?>">
                                    <?php echo htmlspecialchars($order['order_status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No recent orders.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>