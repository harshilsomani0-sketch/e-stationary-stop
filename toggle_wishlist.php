<?php
include_once 'includes/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'login_required']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);

    // Check if the item is already in the wishlist
    $check_stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Item exists, so remove it
        $delete_stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $delete_stmt->bind_param("ii", $user_id, $product_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        echo json_encode(['status' => 'removed']);
    } else {
        // Item does not exist, so add it
        $insert_stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $user_id, $product_id);
        $insert_stmt->execute();
        $insert_stmt->close();
        echo json_encode(['status' => 'added']);
    }
    
    $check_stmt->close();
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>