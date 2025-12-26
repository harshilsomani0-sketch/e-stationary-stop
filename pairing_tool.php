<?php
include 'includes/header.php';

// 1. Fetch Categories for the Dropdown list
$cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = [];
while($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}

// 2. Fetch Products WITH Category Names (Using JOIN)
// We join 'products' with 'categories' so JS can see the category name (e.g., 'Pens')
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";

$products_result = $conn->query($sql);
$all_products = [];

if ($products_result) {
    while ($row = $products_result->fetch_assoc()) {
        $all_products[] = $row;
    }
} else {
    // Fallback if query fails
    echo "Database Error: " . $conn->error;
}
?>

<div class="container" style="margin-top: 40px; text-align: center;">
    <h1>✨ Build Your Perfect Set</h1>
    <p style="color: #666; margin-bottom: 40px;">Mix and match items to see how they look together.</p>

    <div class="pairing-interface" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        
        <div class="pairing-slot" id="slot-1">
            <h3>1. Choose a Base</h3>
            <select class="form-control category-select" onchange="filterProducts(1, this.value)">
                <option value="">Select Category...</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <div class="product-selection-area" id="area-1">
                <p style="padding:10px; color:#999; font-size:0.9em;">Select a category above</p>
            </div>
            
            <div class="selected-preview">
                <img src="https://via.placeholder.com/300x200?text=Select+Item" id="img-preview-1">
                <h4 id="name-preview-1">Select an Item</h4>
                <p id="price-preview-1">$0.00</p>
            </div>
        </div>

        <div style="display: flex; align-items: center; font-size: 2rem; color: #ccc;">+</div>

        <div class="pairing-slot" id="slot-2">
            <h3>2. Choose a Match</h3>
            <select class="form-control category-select" onchange="filterProducts(2, this.value)">
                <option value="">Select Category...</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <div class="product-selection-area" id="area-2">
                 <p style="padding:10px; color:#999; font-size:0.9em;">Select a category above</p>
            </div>

            <div class="selected-preview">
                <img src="https://via.placeholder.com/300x200?text=Select+Item" id="img-preview-2">
                <h4 id="name-preview-2">Select an Item</h4>
                <p id="price-preview-2">$0.00</p>
            </div>
        </div>
    </div>

    <div class="pairing-total" style="margin-top: 40px; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h3>Total Bundle Price: <span id="total-price" style="color: var(--primary-color);">$0.00</span></h3>
        <button class="btn btn-primary" onclick="addAllToCart()">Add Bundle to Cart</button>
    </div>
</div>

<style>
    .pairing-slot {
        background: #fff; padding: 20px; border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05); width: 300px; border: 1px solid #eee;
    }
    .product-selection-area {
        height: 180px; overflow-y: auto; margin: 15px 0; border: 1px solid #eee;
        display: grid; grid-template-columns: 1fr 1fr; gap: 5px; padding: 5px; background: #fdfdfd;
    }
    .mini-product {
        cursor: pointer; border: 1px solid #eee; padding: 5px; background: #fff; text-align: center;
        transition: all 0.2s;
    }
    .mini-product:hover { border-color: var(--primary-color); transform: scale(1.02); }
    .mini-product img { width: 100%; height: 60px; object-fit: contain; margin-bottom: 5px; }
    .mini-product small { display: block; font-size: 0.8em; line-height: 1.2; overflow: hidden; height: 2.4em; }
    .selected-preview img { width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; }
</style>

<script>
    // 1. Receive Data from PHP
    const products = <?php echo json_encode($all_products); ?>;
    let selectedItems = { 1: null, 2: null };

    // Debug: Check if data loaded correctly in Console (F12)
    console.log("Loaded Products:", products);

    function filterProducts(slotId, category) {
        const area = document.getElementById(`area-${slotId}`);
        area.innerHTML = '';
        
        // 2. Filter using 'category_name' from the JOIN query
        // We use loose comparison (==) or includes to be safe against minor differences
        const filtered = products.filter(p => p.category_name === category);
        
        if (filtered.length === 0) {
            area.innerHTML = '<p style="grid-column: span 2; padding:10px; color:#999; font-size:0.8em;">No products found in this category.</p>';
            return;
        }

        // 3. Render Images
        filtered.forEach(p => {
            const div = document.createElement('div');
            div.className = 'mini-product';
            // Use 'image_url' or a default if missing
            const imgPath = p.image_url ? `assets/images/${p.image_url}` : 'https://via.placeholder.com/100';
            
            div.innerHTML = `<img src="${imgPath}"><small>${p.name}</small>`;
            div.onclick = () => selectProduct(slotId, p);
            area.appendChild(div);
        });
    }

    function selectProduct(slotId, product) {
        // Update Visuals
        const imgPath = product.image_url ? `assets/images/${product.image_url}` : 'https://via.placeholder.com/300';
        document.getElementById(`img-preview-${slotId}`).src = imgPath;
        document.getElementById(`name-preview-${slotId}`).innerText = product.name;
        document.getElementById(`price-preview-${slotId}`).innerText = '$' + parseFloat(product.price).toFixed(2);
        
        // Update Data
        selectedItems[slotId] = product;
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        if(selectedItems[1]) total += parseFloat(selectedItems[1].price);
        if(selectedItems[2]) total += parseFloat(selectedItems[2].price);
        document.getElementById('total-price').innerText = '$' + total.toFixed(2);
    }

    function addAllToCart() {
        const item1 = selectedItems[1];
        const item2 = selectedItems[2];

        if (!item1 && !item2) {
            alert("Please select at least one item.");
            return;
        }

        let url = "add_bundle.php?";
        if (item1) url += "id1=" + item1.id + "&";
        if (item2) url += "id2=" + item2.id;
        
        window.location.href = url;
    }
</script>

<?php include 'includes/footer.php'; ?>