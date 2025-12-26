<?php
include_once 'includes/header.php';
?>

<div class="hero-banner" style="background-image: url('assets/images/hero-banner.jpg');">
    <div class="hero-content">
        <h1>Quality Supplies for Inspired Work</h1>
        <p>Discover our curated collection of fine stationery and office essentials.</p>
        <a href="products.php" class="btn btn-primary">Shop The Collection</a>
    </div>
</div>

<h2>Featured Products</h2>
<div class="product-grid">
    <?php
    // Fetch a few products to feature on the homepage
    $result = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product-card">';
            echo '  <a href="product_details.php?id=' . $row["id"] . '">';
            // NOTE: Make sure your image paths are correct!
            echo '      <img src="assets/images/' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["name"]) . '">';
            echo '  </a>';
            echo '  <h3>' . htmlspecialchars($row["name"]) . '</h3>';
            echo '  <p class="price">$' . htmlspecialchars($row["price"]) . '</p>';
            echo '  <a href="product_details.php?id=' . $row["id"] . '" class="btn">View Details</a>';
            echo '</div>';
        }
    } else {
        echo "<p>No featured products available at the moment.</p>";
    }
    ?>
</div>
<div class="text-section-container" style="background-image: url('assets/images/about-bg.jpg');">
    <div class="text-section-content">
        <h2>About E-stationary stop</h2>
        <p>Born from a passion for paper and the perfect pen, E-stationary stop is your destination for supplies that blend functionality with beautiful design. We believe the right tools can transform your work and inspire creativity, whether you're in a corporate office or a home studio.</p>
    </div>
</div>

<div class="why-choose-us">
    <h2>Why Shop With Us?</h2>
    <div class="features-grid">
        <div class="feature-box">
            <h3>&#128230;</h3>
            <h4>Curated Selection</h4>
            <p>We hand-pick every item for its quality, design, and durability.</p>
        </div>
        <div class="feature-box">
            <h3>&#128666;</h3>
            <h4>Fast Shipping</h4>
            <p>Get your supplies delivered to your door quickly and reliably across India.</p>
        </div>
        <div class="feature-box">
            <h3>&#128176;</h3>
            <h4>Great Value</h4>
            <p>Premium products at competitive prices, because quality shouldn't be a luxury.</p>
        </div>
    </div>
</div>
<?php
include_once 'includes/footer.php';
?>