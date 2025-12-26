<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'db_connect.php'; 

$user_initial = 'U';
if(isset($_SESSION['full_name'])) {
    $user_initial = strtoupper(substr($_SESSION['full_name'], 0, 1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-stationary stop</title>
    <link rel="icon" type="image/png" href="/e-stationary-stop/assets/images/logo.png">
    <link rel="stylesheet" href="/e-stationary-stop/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="/e-stationary-stop/index.php" class="nav-logo">
                <img src="/e-stationary-stop/assets/images/logo.png" alt="Logo">
                <span>E-Stationary-stop</span>
            </a>

            <ul class="nav-menu">
                <li><a href="/e-stationary-stop/index.php" class="nav-link">Home</a></li>
                <li><a href="/e-stationary-stop/products.php" class="nav-link">Products</a></li>
                <li><a href="/e-stationary-stop/showcase.php" class="nav-link">Showcase</a></li>
                <li><a href="/e-stationary-stop/pairing_tool.php" class="nav-link">Pairing Tool</a></li>
                <li><a href="/e-stationary-stop/about.php" class="nav-link">About</a></li>
            </ul>

            <div class="nav-right-section">
                
                <div class="search-container">
                    <form action="/e-stationary-stop/products.php" method="get">
                        <input type="search" name="search" id="live-search-input" placeholder="Search...">
                    </form>
                    <div id="search-results" class="search-results"></div>
                </div>

               <a href="/e-stationary-stop/cart.php" class="nav-icon-link" title="Cart">
    <i class="fas fa-shopping-bag"></i>
    
    <?php 
    // SMART COUNT: Only count items with quantity > 0
    $cart_count = 0;
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Filter out any 0 or null values before counting
        $cart_count = count(array_filter($_SESSION['cart']));
    }
    ?>
    
    <span id="cart-item-count" class="cart-badge">
        <?php echo $cart_count; ?>
    </span>
</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/e-stationary-stop/wishlist.php" class="nav-icon-link" title="Wishlist">
                        <i class="far fa-heart"></i>
                    </a>

                    <div class="user-dropdown">
                        <div class="user-avatar"><?php echo $user_initial; ?></div>
                        <i class="fas fa-chevron-down" style="font-size: 0.8rem; color: #888;"></i>
                        
                        <div class="dropdown-content">
                            <div style="padding: 15px; border-bottom: 1px solid #eee; background: #f9f9f9;">
                                <small style="color: #888;">Signed in as</small><br>
                                <strong style="color: #333;"><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
                            </div>
                            <a href="/e-stationary-stop/profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                                <a href="/e-stationary-stop/admin/index.php"><i class="fas fa-tachometer-alt"></i> Admin Panel</a>
                            <?php endif; ?>
                            <a href="/e-stationary-stop/logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>

                <?php else: ?>
                    <a href="/e-stationary-stop/login.php" class="btn-login-nav">Log In</a>
                    <a href="/e-stationary-stop/register.php" class="btn-register-nav">Register</a>
                <?php endif; ?>

                <div class="hamburger" id="hamburger">
                    <i class="fas fa-bars"></i>
                </div>

            </div>
        </nav>
    </header>
    <main class="container">