<?php
// This file is included at the top of all admin pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is not logged in or is not an admin, redirect them
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: /e-stationary-stop/login.php");
    exit();
}
?>