<?php
session_start();
include 'includes/db_connect.php';

// --- HANDLE ACTIONS (Remove / Clear) ---
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'remove' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        unset($_SESSION['cart'][$id]);
        header("Location: cart.php");
        exit();
    }
    if ($_GET['action'] == 'clear') {
        unset($_SESSION['cart']);
        header("Location: cart.php");
        exit();
    }
}

// --- HANDLE UPDATE ---
if (isset($_POST['update_qty'])) {
    $id = intval($_POST['product_id']);
    $qty = intval($_POST['quantity']);
    if ($qty > 0) $_SESSION['cart'][$id] = $qty;
    else unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

include 'includes/header.php';
?>

<div class="container" style="margin-top: 40px; margin-bottom: 40px;">
    <h1>Your Shopping Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info" style="text-align: center; padding: 30px; background-color: #f8f9fa; border: 1px solid #ddd;">
            <h3>Your cart is empty!</h3>
            <p>Looks like you haven't added anything yet.</p>
            <a href="products.php" class="btn btn-primary" style="margin-top: 10px;">Start Shopping</a>
        </div>
    <?php else: ?>

    <form action="" method="post">
        <table class="cart-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background: #f1f1f1; text-align: left;">
                    <th style="padding: 15px;">Product</th>
                    <th style="padding: 15px;">Price</th>
                    <th style="padding: 15px;">Qty</th>
                    <th style="padding: 15px;">Total</th>
                    <th style="padding: 15px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                $cart_ids = array_keys($_SESSION['cart']);
                $found_products = false;

                // --- THE FIX: SANITIZE IDS ---
                // 1. Force all IDs to be integers (turns weird text into 0)
                $safe_ids = array_map('intval', $cart_ids);
                // 2. Remove any 0s
                $safe_ids = array_filter($safe_ids);

                if (!empty($safe_ids)) {
                    // 3. Create a safe list for SQL (e.g., "1,2,5")
                    $ids_string = implode(',', $safe_ids);
                    
                    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids_string)");

                    if ($result && $result->num_rows > 0) {
                        $found_products = true;
                        while ($product = $result->fetch_assoc()):
                            // Ensure we use the safe ID to get quantity
                            $qty = isset($_SESSION['cart'][$product['id']]) ? $_SESSION['cart'][$product['id']] : 1;
                            $subtotal = $product['price'] * $qty;
                            $total += $subtotal;
                ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                            <?php if(!empty($product['image_url'])): ?>
                                <img src="assets/images/<?php echo $product['image_url']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <div style="width:50px; height:50px; background:#ddd; border-radius:4px;"></div>
                            <?php endif; ?>
                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                        </td>
                        <td style="padding: 15px;">$<?php echo number_format($product['price'], 2); ?></td>
                        <td style="padding: 15px;">
                            <input type="number" value="<?php echo $qty; ?>" style="width: 40px;" disabled>
                        </td>
                        <td style="padding: 15px; color: var(--primary-color); font-weight: bold;">
                            $<?php echo number_format($subtotal, 2); ?>
                        </td>
                        <td style="padding: 15px;">
                            <a href="cart.php?action=remove&id=<?php echo $product['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;">Remove</a>
                        </td>
                    </tr>
                <?php 
                        endwhile; 
                    } 
                }
                
                // If we found NO valid products (because of corruption or deletion)
                if (!$found_products) {
                    echo "<tr><td colspan='5' style='text-align:center; padding:30px; color: #dc3545;'>
                            <strong>Notice:</strong> Your cart contained invalid items which have been removed.
                            <br><br>
                            <a href='cart.php?action=clear' class='btn btn-danger'>Reset Cart Completely</a>
                          </td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php if($found_products): ?>
        <div style="margin-top: 30px; text-align: right; padding: 20px; background: #f9f9f9; border-radius: 8px;">
            <h3>Grand Total: <span style="color: var(--primary-color);">$<?php echo number_format($total, 2); ?></span></h3>
            <br>
            <a href="cart.php?action=clear" class="btn btn-outline" style="margin-right: 10px;">Clear Cart</a>
            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
        </div>
        <?php endif; ?>

    </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>