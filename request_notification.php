<?php
include_once 'includes/db_connect.php';

// Check if it's a POST request with the required data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'], $_POST['email'])) {

    $product_id = intval($_POST['product_id']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    // Validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect back with an error
        header("Location: product_details.php?id=" . $product_id . "&notify=error");
        exit();
    }

    // Use "INSERT IGNORE" to handle duplicate entries silently.
    $stmt = $conn->prepare("INSERT IGNORE INTO stock_notifications (product_id, user_email) VALUES (?, ?)");
    $stmt->bind_param("is", $product_id, $email);
    $stmt->execute();
    $stmt->close();

    // Send the user back with a success message
    header("Location: product_details.php?id=" . $product_id . "&notify=success");
    exit();

} else {
    // If accessed directly, redirect home
    header("Location: index.php");
    exit();
}
?>