<?php
include_once 'includes/header.php';

$token = $_GET['token'] ?? '';
$errors = [];
$success_message = '';

// Check if token is valid and not expired
$stmt_check = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt_check->bind_param("s", $token);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    // If token is invalid or expired, show an error and stop.
    echo "<div class='form-container'><div class='alert alert-danger'>This password reset link is invalid or has expired.</div></div>";
    include_once 'includes/footer.php';
    exit();
}
$row = $result->fetch_assoc();
$email = $row['email'];
$stmt_check->close();


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password validation (same as registration page)
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update the user's password in the users table
        $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt_update->bind_param("ss", $hashed_password, $email);
        $stmt_update->execute();
        $stmt_update->close();

        // Delete the used token from the password_resets table
        $stmt_delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt_delete->bind_param("s", $email);
        $stmt_delete->execute();
        $stmt_delete->close();
        
        $success_message = "Your password has been reset successfully! You can now log in with your new password.";
    }
}
?>

<div class="form-container">
    <h2>Reset Your Password</h2>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <p><?php echo $success_message; ?></p>
            <a href="login.php" class="btn">Go to Login</a>
        </div>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" required minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>