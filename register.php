<?php
// register.php
session_start();
include_once 'includes/db_connect.php';

// --- AJAX HANDLER ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');

    $full_name = trim($_POST['full_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validations
    if (empty($full_name) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }
    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit();
    }
    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters.']);
        exit();
    }

    // Check Duplicate
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email is already registered.']);
        exit();
    }
    $stmt->close();

    // Insert User
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $full_name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Account created! Redirecting to login...', 'redirect' => 'login.php']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again.']);
    }
    $stmt->close();
    exit();
}
?>

<?php include_once 'includes/header.php'; ?>

<div class="auth-body-bg">
    <div class="auth-blob blob-1"></div>
    <div class="auth-blob blob-2" style="background: #a8edea; bottom: -50px; left: -50px;"></div>

    <div class="glass-card">
        <h2>Create Account</h2>
        <p>Join our community of creators</p>

        <div id="auth-message"></div>

        <form id="register-form">
            <div class="ambient-form-group">
                <span class="input-icon">👤</span>
                <input type="text" name="full_name" class="ambient-input" placeholder="Full Name" required>
            </div>

            <div class="ambient-form-group">
                <span class="input-icon">✉️</span>
                <input type="email" name="email" class="ambient-input" placeholder="Email Address" required>
            </div>

            <div class="ambient-form-group">
                <span class="input-icon">🔒</span>
                <input type="password" name="password" id="reg-pass" class="ambient-input" placeholder="Password (Min 6 chars)" required>
                <span class="toggle-password" onclick="togglePass('reg-pass')">👁️</span>
            </div>

            <div class="ambient-form-group">
                <span class="input-icon">🔐</span>
                <input type="password" name="confirm_password" id="reg-confirm" class="ambient-input" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="btn-ambient">Sign Up</button>
        </form>

        <div class="login-links" style="justify-content: center; margin-top: 20px;">
            Already have an account? &nbsp; <a href="login.php">Sign In</a>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>