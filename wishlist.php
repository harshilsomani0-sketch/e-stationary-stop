<?php
include_once 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<h1>My Wishlist</h1>

<div class="product-grid">
<?php
// Fetch products from user's wishlist
$sql = "SELECT p.* FROM products p JOIN wishlist w ON p.id = w.product_id WHERE w.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($product = $result->fetch_assoc()) {
        // This is a simplified product card for the wishlist page
        echo '<div class="product-card">';
        echo '  <a href="product_details.php?id=' . $product["id"] . '">';
        echo '      <img src="assets/images/' . htmlspecialchars($product["image_url"]) . '" alt="' . htmlspecialchars($product["name"]) . '">';
        echo '  </a>';
        echo '  <h3>' . htmlspecialchars($product["name"]) . '</h3>';
        echo '  <p class="price">$' . htmlspecialchars($product["price"]) . '</p>';
        echo '  <div class="product-actions">';
        echo '      <a href="product_details.php?id=' . $product["id"] . '" class="btn">View & Buy</a>';
        // Wishlist button to remove item directly from this page
        echo '      <button class="wishlist-btn active" data-product-id="' . $product['id'] . '" title="Remove from Wishlist">&#10084;</button>';
        echo '  </div>';
        echo '</div>';
    }
} else {
    echo "<p>Your wishlist is empty. Browse our products to find something you like!</p>";
}
$stmt->close();
?>
</div>

<?php include_once 'includes/footer.php'; ?>