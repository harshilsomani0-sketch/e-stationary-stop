<?php
// admin/add_product.php
include_once '../includes/db_connect.php';
// include_once 'auth_check.php'; // Uncomment if you have this file

$is_edit = false;
$product = [
    'id' => '', 'name' => '', 'description' => '', 'price' => '', 
    'stock_quantity' => '', 'category_id' => '', 'image_url' => '', 'is_featured' => 0
];
$gallery_images = [];

// --- HANDLE EDIT MODE ---
if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $edit_id = intval($_GET['edit_id']);
    
    // 1. Fetch Main Product Data
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    // 2. Fetch Existing Gallery Images
    $g_stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
    $g_stmt->bind_param("i", $edit_id);
    $g_stmt->execute();
    $g_result = $g_stmt->get_result();
    while($row = $g_result->fetch_assoc()) {
        $gallery_images[] = $row;
    }
    $g_stmt->close();
}

// Fetch categories
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

include_once '../includes/header.php';
?>

<div class="container" style="max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    
    <h2 style="border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 25px;">
        <?php echo $is_edit ? 'Edit Product' : 'Add New Product'; ?>
    </h2>

    <form action="product_handler.php" method="POST" enctype="multipart/form-data">
        
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <?php endif; ?>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Product Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Description</label>
            <textarea name="description" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Price ($)</label>
                <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Stock Quantity</label>
                <input type="number" name="stock_quantity" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Category</label>
            <select name="category_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Select a Category</option>
                <?php while($cat = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 20px; background: #f9f9f9; padding: 10px; border-radius: 4px;">
            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="is_featured" value="1" <?php echo ($product['is_featured'] == 1) ? 'checked' : ''; ?>>
                <span>Feature this product (Show in pop-ups/banners)?</span>
            </label>
        </div>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

        <div class="form-group" style="margin-bottom: 25px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Main Product Image</label>
            <input type="file" name="image" accept="image/*" class="form-control" style="padding: 5px;">
            
            <?php if ($is_edit && !empty($product['image_url'])): ?>
                <div style="margin-top: 10px; padding: 10px; background: #f1f1f1; border-radius: 4px; display: inline-block;">
                    <p style="margin: 0 0 5px 0; font-size: 0.9em; color: #666;">Current Main Image:</p>
                    <img src="../assets/images/<?php echo htmlspecialchars($product['image_url']); ?>" style="height: 80px; border: 1px solid #ccc;">
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom: 30px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Additional Gallery Images</label>
            <small style="color: #666; display: block; margin-bottom: 8px;">Hold <strong>Ctrl</strong> (or Cmd) to select multiple photos.</small>
            
            <input type="file" name="gallery_images[]" multiple accept="image/*" class="form-control" style="padding: 5px;">
            
            <?php if ($is_edit && !empty($gallery_images)): ?>
                <div style="margin-top: 15px;">
                    <p style="margin-bottom: 5px; font-weight: bold; font-size: 0.9em;">Current Gallery:</p>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php foreach($gallery_images as $g_img): ?>
                            <div style="position: relative;">
                                <img src="../assets/images/<?php echo htmlspecialchars($g_img['image_url']); ?>" style="width: 60px; height: 60px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" name="save_product" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1.1em; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            <?php echo $is_edit ? 'Update Product' : 'Save New Product'; ?>
        </button>

    </form>
</div>

<?php include_once '../includes/footer.php'; ?>