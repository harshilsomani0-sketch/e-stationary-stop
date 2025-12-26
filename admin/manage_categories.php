<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php'; // Ensures only admins can access

// --- Handle Delete Request ---
if (isset($_GET['delete_id'])) {
    $id_to_delete = intval($_GET['delete_id']);
    
    // WARNING: Deleting a category will also delete all products within it
    // because of the "ON DELETE CASCADE" setting in our database.
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    if ($stmt->execute()) {
        $delete_success_message = "Category (and all its products) deleted successfully.";
    } else {
        $delete_error_message = "Error deleting category.";
    }
    $stmt->close();
}

// Fetch all categories to display in the table
$result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

include_once '../includes/header.php'; // Use the main site header
?>

<h2>Manage Categories</h2>

<?php if(isset($delete_success_message)): ?>
    <div class="alert alert-success"><?php echo $delete_success_message; ?></div>
<?php endif; ?>
<?php if(isset($delete_error_message)): ?>
    <div class="alert alert-danger"><?php echo $delete_error_message; ?></div>
<?php endif; ?>

<a href="category_form.php" class="btn btn-primary">Add New Category</a>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                    <a href="category_form.php?edit_id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                    <a href="manage_categories.php?delete_id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('WARNING: Deleting this category will also delete ALL products inside it. Are you absolutely sure?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No categories found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include_once '../includes/footer.php'; ?>