<?php
include_once 'includes/header.php';

$message = '';
$message_type = ''; // 'success' or 'danger'

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = 'danger';
    } else {
        // Check if the email exists in the users table
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            // Email exists, generate a secure token
            $token = bin2hex(random_bytes(50));
            // Set expiry time (e.g., 1 hour from now)
            $expires = new DateTime('NOW');
            $expires->add(new DateInterval('PT1H'));
            $expires_at = $expires->format('Y-m-d H:i:s');

            // Store the token in the password_resets table
            $stmt_insert = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $email, $token, $expires_at);
            $stmt_insert->execute();
            $stmt_insert->close();

            // --- IMPORTANT: EMAIL SIMULATION ---
            // In a real application, you would email this link. For this project, we will display it on screen.
            $reset_link = "http://localhost/e-stationary-stop/reset_password.php?token=" . $token;
            $message = "<strong>Password Reset Link (For Demo):</strong><br><a href='{$reset_link}'>{$reset_link}</a>";
            $message_type = 'success';
            
        } else {
            // To prevent user enumeration, we show the same message whether the email exists or not.
            $message = "If an account with that email exists, a password reset link has been sent.";
            $message_type = 'success';
        }
        $stmt_check->close();
    }
}
?>

<div class="form-container">
    <h2>Forgot Your Password?</h2>
    <p>Enter your email address below, and we'll send you a link to reset your password.</p>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="forgot_password.php" method="post">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
    <div style="text-align: center; margin-top: 15px;">
        <a href="login.php">Back to Login</a>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>