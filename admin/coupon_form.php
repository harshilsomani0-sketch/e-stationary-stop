<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

$is_edit = false;
$coupon = ['id' => '', 'code' => '', 'type' => 'percentage', 'value' => '', 'expires_at' => '', 'is_active' => 1];

if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $edit_id = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $coupon = $result->fetch_assoc();
        // Format date for the datetime-local input
        $coupon['expires_at'] = $coupon['expires_at'] ? date('Y-m-d\TH:i', strtotime($coupon['expires_at'])) : '';
    }
    $stmt->close();
}

include_once '../includes/header.php';
?>

<h2><?php echo $is_edit ? 'Edit' : 'Add New'; ?> Coupon</h2>

<form action="coupon_handler.php" method="post" class="form-container">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?php echo $coupon['id']; ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="code">Coupon Code (e.g., SUMMER20)</label>
        <input type="text" name="code" id="code" value="<?php echo htmlspecialchars($coupon['code']); ?>" required>
    </div>
    <div class="form-group">
        <label for="type">Discount Type</label>
        <select name="type" id="type">
            <option value="percentage" <?php if($coupon['type'] == 'percentage') echo 'selected'; ?>>Percentage (%)</option>
            <option value="fixed" <?php if($coupon['type'] == 'fixed') echo 'selected'; ?>>Fixed Amount ($)</option>
        </select>
    </div>
    <div class="form-group">
        <label for="value">Value (e.g., 20 for 20% or 10.50 for $10.50)</label>
        <input type="number" name="value" id="value" step="0.01" value="<?php echo htmlspecialchars($coupon['value']); ?>" required>
    </div>
    <div class="form-group">
        <label for="expires_at">Expires At (Optional)</label>
        <input type="datetime-local" name="expires_at" id="expires_at" value="<?php echo htmlspecialchars($coupon['expires_at']); ?>">
    </div>
    <div class="form-group">
        <label>
            <input type="checkbox" name="is_active" value="1" <?php if($coupon['is_active']) echo 'checked'; ?>>
            Is Active?
        </label>
    </div>
    
    <button type="submit" name="save_coupon" class="btn btn-primary"><?php echo $is_edit ? 'Update' : 'Save'; ?> Coupon</button>
</form>

<?php include_once '../includes/footer.php'; ?>