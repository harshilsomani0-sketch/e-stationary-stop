<?php
// login.php
session_start();
include_once 'includes/db_connect.php';

// --- AJAX HANDLER (For JavaScript Requests) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json'); // Tell browser we are sending JSON
    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, full_name, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Success! Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $email;
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Redirect URL (Admin goes to dashboard, User goes to home)
           // Redirect URL (Admin goes to dashboard, User goes to home)
            // FIXED PATHS FOR LOCALHOST: Added '/e-stationary-stop'
            if ($user['is_admin'] == 1) {
                $redirect = '/e-stationary-stop/admin/index.php';
            } else {
                $redirect = '/e-stationary-stop/index.php';
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Login successful! Redirecting...', 'redirect' => $redirect]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No account found with this email.']);
    }
    $stmt->close();
    exit(); // Stop script here so HTML isn't sent
}
?>

<?php include_once 'includes/header.php'; ?>

<div class="auth-body-bg">
    <div class="auth-blob blob-1"></div>
    <div class="auth-blob blob-2"></div>

    <div class="glass-card">
        <h2>Welcome Back</h2>
        <p>Sign in to continue your stationery journey</p>

        <div id="auth-message"></div>

        <form id="login-form">
            <div class="ambient-form-group">
                <span class="input-icon">✉️</span>
                <input type="email" name="email" class="ambient-input" placeholder="Email Address" required>
            </div>

            <div class="ambient-form-group">
                <span class="input-icon">🔒</span>
                <input type="password" name="password" id="login-pass" class="ambient-input" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePass('login-pass')">👁️</span>
            </div>

            <button type="submit" class="btn-ambient">Sign In</button>
        </form>

        <div class="login-links" style="justify-content: center; margin-top: 20px;">
            <a href="register.php">Create Account</a> &nbsp;|&nbsp; <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>