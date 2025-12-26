    </main> <!-- end container -->
    
    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-column">
                <h4>E-stationary stop</h4>
                <p>Your one-stop shop for quality office and school supplies that inspire your best work. Based in Ahmedabad, Gujarat.</p>
            </div>
            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="/e-stationary-stop/index.php">Home</a></li>
                    <li><a href="/e-stationary-stop/products.php">Products</a></li>
                    <li><a href="/e-stationary-stop/cart.php">Your Cart</a></li>
                    <!-- This link is now updated -->
                    <li><a href="/e-stationary-stop/about.php">About Us</a></li>
                    <li><a href="/e-stationary-stop/showcase.php">Showcase</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact Us</h4>
                <p>
                    Email: <a href="mailto:harshilsomani0@gmail.com">harshilsomani0@gmail.com</a><br>
                    Phone: +91 98793 94959
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("2021"); ?> E-stationary stop. All Rights Reserved.</p>
        </div>
    </footer>
    <?php
// Fetch the single featured product from the database
$featured_product_stmt = $conn->query("SELECT * FROM products WHERE is_featured = 1 LIMIT 1");
if ($featured_product_stmt->num_rows > 0) {
    $featured_product = $featured_product_stmt->fetch_assoc();
?>
    <!-- Promotional Modal HTML -->
    <div id="promo-modal-overlay" class="modal-overlay">
        <div id="promo-modal-content" class="modal-content">
            <span id="close-promo-modal" class="modal-close">&times;</span>
            <div class="promo-modal-body">
                <div class="promo-image">
                    <img src="/e-stationary-stop/assets/images/<?php echo htmlspecialchars($featured_product['image_url']); ?>" alt="<?php echo htmlspecialchars($featured_product['name']); ?>">
                </div>
                <div class="promo-text">
                    <h3>You Might Also Love...</h3>
                    <h2><?php echo htmlspecialchars($featured_product['name']); ?></h2>
                    <p><?php echo substr(htmlspecialchars($featured_product['description']), 0, 100); ?>...</p>
                    <p class="price">$<?php echo htmlspecialchars($featured_product['price']); ?></p>
                    <a href="/e-stationary-stop/product_details.php?id=<?php echo $featured_product['id']; ?>" class="btn btn-primary">View Product</a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
    
    <script src="/e-stationary-stop/assets/js/script.js"></script>
</body>
</html>
