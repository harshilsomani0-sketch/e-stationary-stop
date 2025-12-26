<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

$is_edit = false;
$category = ['id' => '', 'name' => '', 'description' => ''];

// Check if we are editing an existing category
if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $edit_id = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    }
    $stmt->close();
}

include_once '../includes/header.php';
?>

<h2><?php echo $is_edit ? 'Edit' : 'Add New'; ?> Category</h2>

<form action="category_handler.php" method="post" class="form-container">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="name">Category Name</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($category['description']); ?></textarea>
    </div>

    <button type="submit" name="save_category" class="btn btn-primary"><?php echo $is_edit ? 'Update' : 'Save'; ?> Category</button>
</form>

<?php include_once '../includes/footer.php'; ?>