<?php
include_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect or show an error
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    // Simple validation
    if ($product_id > 0 && ($rating >= 1 && $rating <= 5)) {
        // Prevent duplicate reviews by the same user for the same product
        $check_stmt = $conn->prepare("SELECT id FROM reviews WHERE product_id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $product_id, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
            $stmt->execute();
            $stmt->close();
        }
        $check_stmt->close();
    }
    
    // Redirect back to the product page
    header("Location: product_details.php?id=" . $product_id);
    exit();
}
?>