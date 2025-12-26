<?php
session_start();

// 1. Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 2. Get the Product IDs from the URL
$id1 = isset($_GET['id1']) ? intval($_GET['id1']) : 0;
$id2 = isset($_GET['id2']) ? intval($_GET['id2']) : 0;

// 3. Helper function to add item
function addToCart($id) {
    if ($id > 0) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++; // Increase quantity if already in cart
        } else {
            $_SESSION['cart'][$id] = 1; // Add new item
        }
    }
}

// 4. Add both items
if ($id1 > 0) addToCart($id1);
if ($id2 > 0) addToCart($id2);

// 5. Redirect to Cart Page
if ($id1 > 0 || $id2 > 0) {
    header("Location: cart.php");
    exit();
} else {
    // If user clicked add but selected nothing, go back
    header("Location: pairing_tool.php");
    exit();
}
?>