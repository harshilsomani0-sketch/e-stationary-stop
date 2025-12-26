<?php
session_start();
include 'includes/db_connect.php';

// --- 1. HANDLE "ADD TO CART" ---
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $product_id = intval($_POST['product_id']);
    $quantity = 1;

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // Refresh page to update badge
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}

// --- 2. SEARCH & FILTER ---
$where_clause = "1=1";
$params = [];
$types = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $where_clause .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $cat_id = intval($_GET['category']);
    $where_clause .= " AND category_id = ?";
    $params[] = $cat_id;
    $types .= "i";
}

// --- 3. FETCH PRODUCTS ---
$sql = "SELECT * FROM products WHERE $where_clause";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

include 'includes/header.php';
?>

<div class="container" style="margin-top: 40px; margin-bottom: 50px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>All Products</h1>
        <div class="category-filters">
            <a href="products.php" class="btn btn-outline" style="padding: 5px 15px;">All</a>
            <a href="products.php?category=1" class="btn btn-outline" style="padding: 5px 15px;">Notebooks</a>
            <a href="products.php?category=2" class="btn btn-outline" style="padding: 5px 15px;">Pens</a>
        </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="product-grid">
            <?php while ($product = $result->fetch_assoc()): 
                
                // --- IMAGE DISPLAY LOGIC ---
                $img_folder = "assets/images/";
                $img_name = $product['image_url'];
                $server_path = $img_folder . $img_name;
                
                // 1. Check if file exists on the server
                if (!empty($img_name) && file_exists($server_path)) {
                    // 2. ENCODE the filename for the browser
                    // This turns "office files.jpg" into "office%20files.jpg" so it works!
                    $img_src = $img_folder . rawurlencode($img_name);
                } else {
                    // Fallback placeholder
                    $img_src = "https://via.placeholder.com/300x300?text=No+Image";
                }
            ?>
            
            <div class="product-card">
                <a href="product_details.php?id=<?php echo $product['id']; ?>">
                    <img src="<?php echo $img_src; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px 5px 0 0;">
                </a>

                <div class="card-body" style="padding: 15px;">
                    <small style="color: #999; text-transform: uppercase;">Stationery</small>
                    
                    <h3 style="margin: 10px 0; font-size: 1.2rem;">
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" style="text-decoration: none; color: #333;">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a>
                    </h3>
                    
                    <p class="price" style="color: var(--primary-color); font-weight: bold; margin-bottom: 15px;">
                        $<?php echo number_format($product['price'], 2); ?>
                    </p>

                    <div class="product-actions" style="display: flex; gap: 10px; justify-content: center;">
                        <form method="POST" action="">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary" style="padding: 8px 15px;">
                                Add to Cart
                            </button>
                        </form>
                        <a href="wishlist_handler.php?add=<?php echo $product['id']; ?>" class="btn btn-outline" style="padding: 8px 12px;">
                            <i class="far fa-heart"></i>
                        </a>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 50px;">
            <h3>No products found</h3>
            <a href="products.php" class="btn btn-primary">View All Products</a>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>