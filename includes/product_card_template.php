<?php
// This template expects a $product variable to be available
global $user_wishlist; // Make wishlist data available in this scope
?>
<div class="product-card">
    <a href="product_details.php?id=<?php echo $product["id"]; ?>">
        <img src="assets/images/<?php echo htmlspecialchars($product["image_url"]); ?>" alt="<?php echo htmlspecialchars($product["name"]); ?>">
    </a>
    <h3><?php echo htmlspecialchars($product["name"]); ?></h3>
    <p class="price">$<?php echo htmlspecialchars($product["price"]); ?></p>
    <div class="product-actions">
        <form action="add_to_cart.php" method="post" style="display:inline;">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="quantity" value="0">
            <button type="submit" class="btn">Add to Cart</button>
        </form>
        <?php $is_wishlisted = in_array($product['id'], $user_wishlist); ?>
        <button class="wishlist-btn <?php echo ($is_wishlisted ? 'active' : ''); ?>" data-product-id="<?php echo $product['id']; ?>" title="<?php echo ($is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'); ?>">&#10084;</button>
    </div>
</div>