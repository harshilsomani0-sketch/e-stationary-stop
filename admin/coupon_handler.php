<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

if (isset($_POST['save_coupon'])) {
    $code = trim($_POST['code']);
    $type = $_POST['type'];
    $value = $_POST['value'];
    $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE coupons SET code = ?, type = ?, value = ?, expires_at = ?, is_active = ? WHERE id = ?");
        $stmt->bind_param("ssdssi", $code, $type, $value, $expires_at, $is_active, $id);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO coupons (code, type, value, expires_at, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $code, $type, $value, $expires_at, $is_active);
    }
    
    if ($stmt->execute()) {
        header("Location: manage_coupons.php");
        exit();
    } else {
        echo "Error: Could not save coupon. " . $stmt->error;
    }
    $stmt->close();

} else {
    header("Location: manage_coupons.php");
    exit();
}
?>