<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $id_to_delete = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_coupons.php?deleted=true");
    exit();
}

$result = $conn->query("SELECT * FROM coupons ORDER BY created_at DESC");

include_once '../includes/header.php';
?>

<h2>Manage Coupons</h2>

<a href="coupon_form.php" class="btn btn-primary">Add New Coupon</a>

<table class="admin-table">
    <thead>
        <tr>
            <th>Code</th>
            <th>Type</th>
            <th>Value</th>
            <th>Expires At</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($row['code']); ?></strong></td>
            <td><?php echo ucfirst($row['type']); ?></td>
            <td><?php echo ($row['type'] == 'percentage') ? htmlspecialchars($row['value']) . '%' : '$' . number_format($row['value'], 2); ?></td>
            <td><?php echo $row['expires_at'] ? date("M d, Y", strtotime($row['expires_at'])) : 'Never'; ?></td>
            <td>
                <?php if ($row['is_active']): ?>
                    <span class="status status-delivered">Active</span>
                <?php else: ?>
                    <span class="status status-canceled">Inactive</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="coupon_form.php?edit_id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                <a href="manage_coupons.php?delete_id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include_once '../includes/footer.php'; ?>