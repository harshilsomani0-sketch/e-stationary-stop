<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';
include_once '../includes/header.php';

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $id_to_delete = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    $stmt->close();
    echo "<div class='alert alert-success'>Product deleted successfully.</div>";
}

$result = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
?>

<h2>Manage Products</h2>
<a href="product_form.php" class="btn btn-primary">Add New Product</a>

<table class="admin-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="../assets/images/<?php echo htmlspecialchars($row['image_url']); ?>" width="50"></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
            <td>$<?php echo htmlspecialchars($row['price']); ?></td>
            <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
            <td>
                <a href="product_form.php?edit_id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                <a href="manage_products.php?delete_id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include_once '../includes/footer.php'; ?>