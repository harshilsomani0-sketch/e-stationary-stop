<?php
include 'includes/header.php';
// Fetch showcase items
$showcase_result = $conn->query("SELECT * FROM showcase ORDER BY id DESC");
?>

<div class="hero-banner" style="height: 40vh; background-image: url('assets/images/hero-banner.jpg');">
    <div class="hero-content">
        <h1>Inspiration Gallery</h1>
        <p>See how others are styling their desks.</p>
    </div>
</div>

<div class="container" style="margin-top: 40px;">
    
    <div class="showcase-grid">
        <?php if ($showcase_result->num_rows > 0): ?>
            <?php while($item = $showcase_result->fetch_assoc()): ?>
                <div class="showcase-item">
                    <img src="assets/images/<?php echo $item['image_url']; ?>" alt="<?php echo $item['title']; ?>">
                    <div class="showcase-overlay">
                        <h4><?php echo $item['title']; ?></h4>
                        <p><?php echo $item['description']; ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="showcase-item">
                <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?w=600" alt="Minimal Desk">
                <div class="showcase-overlay"><h4>Minimalist Setup</h4></div>
            </div>
            <div class="showcase-item">
                <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=600" alt="Work Mode">
                <div class="showcase-overlay"><h4>Work Mode On</h4></div>
            </div>
            <div class="showcase-item">
                <img src="https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=600" alt="Study Vibes">
                <div class="showcase-overlay"><h4>Study Vibes</h4></div>
            </div>
        <?php endif; ?>
    </div>

</div>

<style>
    .showcase-grid {
        column-count: 3; /* Creates 3 columns like Pinterest */
        column-gap: 20px;
    }
    .showcase-item {
        margin-bottom: 20px;
        break-inside: avoid; /* Prevents cutting images in half */
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
    }
    .showcase-item img {
        width: 100%;
        display: block;
        transition: transform 0.3s ease;
    }
    .showcase-item:hover img {
        transform: scale(1.05);
    }
    .showcase-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white; padding: 20px; opacity: 0; transition: opacity 0.3s;
    }
    .showcase-item:hover .showcase-overlay { opacity: 1; }

    @media (max-width: 768px) {
        .showcase-grid { column-count: 2; }
    }
    @media (max-width: 480px) {
        .showcase-grid { column-count: 1; }
    }
</style>

<?php include 'includes/footer.php'; ?>