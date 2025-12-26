<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php'; // Ensures only admins can access

// Handle a request to delete a user
if (isset($_GET['delete_id'])) {
    $id_to_delete = intval($_GET['delete_id']);
    
    // Prepare a statement to delete the user.
    // IMPORTANT: We add "is_admin = 0" to the query as a security measure
    // to prevent an admin from accidentally deleting another admin account.
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        $message = "User account deleted successfully.";
        $message_type = "success";
    } else {
        $message = "Error: Could not delete user account.";
        $message_type = "danger";
    }
    $stmt->close();
}

// Fetch all users who are NOT administrators to display in the table
$result = $conn->query("SELECT id, full_name, email, created_at FROM users WHERE is_admin = 0 ORDER BY created_at DESC");

include_once '../includes/header.php';
?>

<h2>Manage Customer Accounts</h2>

<?php if (isset($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>User ID</th>
            <th>Full Name</th>
            <th>Email Address</th>
            <th>Registered On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
                <td>
                    <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No customer accounts found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include_once '../includes/footer.php'; ?>