<?php
include_once 'includes/header.php';

// Check for notification status messages
$notify_message = '';
$notify_type = '';
if (isset($_GET['notify'])) {
    if ($_GET['notify'] == 'success') {
        $notify_message = "You're on the list! We'll email you when this is back in stock.";
        $notify_type = 'success';
    } elseif ($_GET['notify'] == 'error') {
        $notify_message = 'Please enter a valid email address.';
        $notify_type = 'danger';
    }
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container'><h1>Product not found!</h1></div>";
    include_once 'includes/footer.php';
    exit();
}
$product_id = intval($_GET['id']);

// Fetch product details
$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<div class='container'><h1>Product not found!</h1></div>";
    include_once 'includes/footer.php';
    exit();
}
$product = $result->fetch_assoc();
$stmt->close();

// (Your review-fetching logic would go here)
// ...

?>

<?php if ($notify_message): ?>
<div class="container">
    <div class="alert alert-<?php echo $notify_type; ?>"><?php echo $notify_message; ?></div>
</div>
<?php endif; ?>


<div class="product-details-container">
    <div class="product-image-section">
        <div class="image-preview-wrapper">
            <img src="assets/images/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php if (($product['is_customizable'] ?? 0) == 1): ?>
    <span class="badge badge-info">Customizable</span>
<?php endif; ?>
        </div>
    </div>
    <div class="product-info-section">
        <span class="category-tag"><?php echo htmlspecialchars($product['category_name']); ?></span>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        
        <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
        <p class="description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        

        <?php if ($product['stock_quantity'] > 0): ?>
        
            <form action="add_to_cart.php" method="post" class="add-to-cart-form">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
               <?php if (($product['is_customizable'] ?? 0) == 1): ?>
    <div class="form-group">
        <label>Custom Name/Text:</label>
        <input type="text" name="custom_text" class="form-control" placeholder="Enter name on notebook">
    </div>
<?php endif; ?>

                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                    <span class="stock-info">Only <?php echo $product['stock_quantity']; ?> left in stock!</span>
                </div>
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>

        <?php else: ?>

            <div class="stock-message out-of-stock">
                <h3>Sold Out</h3>
                <p>This item is currently unavailable. Enter your email below to be notified when it's back in stock!</p>
                
                <form action="request_notification.php" method="POST" class="notify-form">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Enter your email address" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" required>
                        <button type="submit" class="btn btn-primary">Notify Me</button>
                    </div>
                </form>
            </div>

        <?php endif; ?>
        
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>