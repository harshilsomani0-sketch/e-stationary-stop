<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

if (isset($_POST['save_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    // Check if we are updating an existing category or inserting a new one
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing category
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
    } else {
        // Insert new category
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
    }
    
    if ($stmt->execute()) {
        // Redirect back to the management page on success
        header("Location: manage_categories.php");
        exit();
    } else {
        // Handle error
        echo "Error: Could not save category. " . $stmt->error;
    }
    $stmt->close();
} else {
    // Redirect if accessed directly
    header("Location: manage_categories.php");
    exit();
}
?>