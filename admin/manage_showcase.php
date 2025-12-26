<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

// Handle approve or delete actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE showcase SET is_approved = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    if ($action === 'delete') {
        // Optional: you might want to delete the image file from the server as well
        $stmt = $conn->prepare("DELETE FROM showcase WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_showcase.php"); // Redirect to refresh the page
    exit();
}

// Fetch all showcase items
$sql = "SELECT s.*, u.full_name FROM showcase s JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC";
$result = $conn->query($sql);

include_once '../includes/header.php';
?>

<h2>Manage Showcase Submissions</h2>

<table class="admin-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Submitted By</th>
            <th>Caption</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="../assets/showcase/<?php echo htmlspecialchars($row['image_url']); ?>" width="100"></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['caption']); ?></td>
            <td>
                <?php if($row['is_approved']): ?>
                    <span class="status status-delivered">Approved</span>
                <?php else: ?>
                    <span class="status status-pending">Pending</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if(!$row['is_approved']): ?>
                    <a href="manage_showcase.php?action=approve&id=<?php echo $row['id']; ?>" class="btn">Approve</a>
                <?php endif; ?>
                <a href="manage_showcase.php?action=delete&id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include_once '../includes/footer.php'; ?>