<?php
/**
 * Login Page - Document Management System
 * Pamantasan ng Lungsod ng Pasig
 */

// Start session
session_start();

// Include database configuration
require_once 'config/database.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role_id'])) {
    $role_id = $_SESSION['role_id'];
    
    switch ($role_id) {
        case ROLE_SUPER_ADMIN:
            header('Location: views/super-admin/admin_dashboard.php');
            exit();
        case ROLE_DEPT_ADMIN:
            header('Location: views/dept-admin/dashboard.php');
            exit();
        case ROLE_USER:
            header('Location: views/user/user_dashboard.php');
            exit();
    }
}

// Initialize variables
$error_message = '';
$success_message = '';

// Check for logout success
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success_message = 'You have been successfully logged out.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['email'] ?? ''); // Changed from 'username' to 'email' to match form
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['rememberMe']);
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both email/username and password.';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Get user by username or email
            $stmt = $pdo->prepare("
                SELECT u.*, r.role_name, d.department_name 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.role_id 
                LEFT JOIN departments d ON u.department_id = d.department_id 
                WHERE (u.username = ? OR u.email = ?) AND u.is_active = 1
            ");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && $password === $user['password']) {
                // Login successful - plain text password comparison
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['role_name'] = $user['role_name'];
                $_SESSION['department_id'] = $user['department_id'];
                $_SESSION['department_name'] = $user['department_name'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                // Handle remember me
                if ($remember_me) {
                    // Set cookie for 30 days
                    setcookie('remember_user', $user['username'], time() + (30 * 24 * 60 * 60), '/');
                }
                
                // Update last login
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
                $updateStmt->execute([$user['user_id']]);
                
                // Log audit trail
                try {
                    $auditStmt = $pdo->prepare("
                        INSERT INTO audit_trails (user_id, action_type, table_name, ip_address, user_agent) 
                        VALUES (?, 'LOGIN', 'users', ?, ?)
                    ");
                    $auditStmt->execute([
                        $user['user_id'],
                        $_SERVER['REMOTE_ADDR'],
                        $_SERVER['HTTP_USER_AGENT']
                    ]);
                } catch (Exception $e) {
                    // Audit trail failed, but don't stop login
                    error_log("Audit trail error: " . $e->getMessage());
                }
                
                // Set success message for redirect
                $_SESSION['login_success'] = true;
                
                // Redirect based on role
                switch ($user['role_id']) {
                    case ROLE_SUPER_ADMIN:
                        header('Location: views/super-admin/admin_dashboard.php');
                        exit();
                    case ROLE_DEPT_ADMIN:
                        header('Location: views/dept-admin/dashboard.php');
                        exit();
                    case ROLE_USER:
                        header('Location: views/user/user_dashboard.php');
                        exit();
                    default:
                        $error_message = 'Invalid user role.';
                }
            } else {
                // Login failed
                $error_message = 'Invalid email/username or password.';
                
                // Log failed login attempt
                try {
                    $auditStmt = $pdo->prepare("
                        INSERT INTO audit_trails (user_id, action_type, table_name, old_value, ip_address, user_agent) 
                        VALUES (NULL, 'LOGIN_FAILED', 'users', ?, ?, ?)
                    ");
                    $auditStmt->execute([
                        $username,
                        $_SERVER['REMOTE_ADDR'],
                        $_SERVER['HTTP_USER_AGENT']
                    ]);
                } catch (Exception $e) {
                    error_log("Audit trail error: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            $error_message = 'Database connection error. Please contact the administrator.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}

// Get remembered username if exists
$remembered_user = $_COOKIE['remember_user'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet" />

    <style>
      :root {
        --dms-blue: #0d6efd;
        --dms-light-blue: #cfe2ff;
        --dms-dark-blue: #084298;
      }

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        background: linear-gradient(135deg, #e3f2fd 0%, #f5f9ff 50%, #fff 100%);
        min-height: 100vh;
        font-family: "Arial", sans-serif;
        position: relative;
        overflow-x: hidden;
      }

      /* Background Decorative Shapes */
      .bg-shapes {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
      }

      .bg-shapes > div {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: float 25s infinite ease-in-out;
      }

      .shape-1 {
        width: 400px;
        height: 400px;
        background: rgba(144, 202, 249, 0.4);
        top: -10%;
        left: -5%;
        animation-delay: 0s;
      }

      .shape-2 {
        width: 350px;
        height: 350px;
        background: rgba(144, 202, 249, 0.3);
        top: 50%;
        right: -10%;
        animation-delay: 5s;
      }

      .shape-3 {
        width: 300px;
        height: 300px;
        background: rgba(144, 202, 249, 0.35);
        top: 20%;
        right: 10%;
        animation-delay: 8s;
      }

      .shape-4 {
        width: 320px;
        height: 320px;
        background: rgba(144, 202, 249, 0.25);
        bottom: -5%;
        left: 5%;
        animation-delay: 3s;
      }

      .shape-5 {
        width: 280px;
        height: 280px;
        background: rgba(144, 202, 249, 0.3);
        top: 10%;
        left: 40%;
        animation-delay: 10s;
      }

      .shape-6 {
        width: 360px;
        height: 360px;
        background: rgba(144, 202, 249, 0.2);
        bottom: 10%;
        right: 30%;
        animation-delay: 6s;
      }

      /* Background Animated Icons */
      .bg-icons {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        overflow: hidden;
      }

      .bg-icons i {
        position: absolute;
        font-size: 2rem;
        color: rgba(13, 110, 253, 0.08);
        animation: floatIcons 20s infinite ease linear;
        opacity: 0.9;
      }

      .bg-icons i:nth-child(1) {
        top: 17%;
        left: 8%;
        animation-duration: 25s;
        font-size: 70px;
      }

      .bg-icons i:nth-child(2) {
        top: 15%;
        left: 70%;
        animation-delay: 5s;
        font-size: 80px;
      }

      .bg-icons i:nth-child(3) {
        top: 45%;
        right: 10%;
        animation-delay: 2s;
        font-size: 85px;
      }

      .bg-icons i:nth-child(4) {
        bottom: 15%;
        left: 25%;
        animation-delay: 8s;
        font-size: 85px;
      }

      .bg-icons i:nth-child(5) {
        bottom: 50%;
        left: 19%;
        animation-delay: 4s;
        font-size: 65px;
      }

      .bg-icons i:nth-child(6) {
        bottom: 25%;
        right: 25%;
        animation-delay: 10s;
        font-size: 80px;
      }

      @keyframes float {
        0% {
          transform: translate(0px) scale(1);
        }
        50% {
          transform: translate(-20px) scale(1.05);
        }
        100% {
          transform: translate(0px) scale(1);
        }
      }

      .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
        padding: 20px;
      }

      .login-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(13, 110, 253, 0.15);
        overflow: hidden;
        max-width: 450px;
        width: 100%;
      }

      .login-header {
        background: linear-gradient(135deg, #1976d2 0%, #0d6efd 100%);
        color: white;
        padding: 40px 30px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }

      .logo-img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        margin-bottom: 20px;
        object-fit: cover;
        background-color: #0d6efd;
        display: block;
      }

      .organization-name {
        font-size: 1.3rem;
        font-weight: bold;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
      }

      .system-name {
        font-size: 0.95rem;
        opacity: 0.95;
      }

      .login-body {
        padding: 35px;
        background: #fafafa;
      }

      .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
        font-size: 0.95rem;
      }

      .form-control {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 12px 15px 12px 45px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        background: white;
        font-size: 0.95rem;
      }

      .form-control:focus {
        border-color: var(--dms-blue);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        background: white;
      }

      .btn-login {
        background: linear-gradient(135deg, #1976d2 0%, #0d6efd 100%);
        border: none;
        color: white;
        padding: 14px;
        border-radius: 12px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
        margin-bottom: 20px;
        font-size: 1rem;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
      }

      .btn-login:hover {
        background: linear-gradient(135deg, #1565c0 0%, #084298 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
      }

      .btn-login:active {
        transform: translateY(0);
      }

      .form-check-input:checked {
        background-color: var(--dms-blue);
        border-color: var(--dms-blue);
      }

      .form-check-label {
        font-size: 0.9rem;
        color: #666;
      }

      .forgot-password {
        color: var(--dms-blue);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
      }

      .forgot-password:hover {
        color: var(--dms-dark-blue);
        text-decoration: underline;
      }

      .alert {
        border-radius: 12px;
        border: none;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 0.9rem;
      }

      .back-home {
        color: var(--dms-blue);
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
      }

      .back-home:hover {
        color: var(--dms-dark-blue);
      }

      .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease infinite;
        margin: 0 auto;
      }

      @keyframes spin {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }

      .icon-input {
        position: relative;
      }

      .input-icon {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: #999;
        font-size: 1.1rem;
      }

      .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 1.1rem;
      }

      .password-toggle:hover {
        color: var(--dms-blue);
      }

      /* Custom Modal Styles */
      .modal-header {
        background-color: var(--dms-light-blue);
        border-bottom: 1px solid #dee2e6;
      }

      .modal-title {
        color: var(--dms-dark-blue);
        font-weight: 600;
      }

      .modal-body {
        padding: 25px;
      }

      .modal-body p {
        margin-bottom: 12px;
        color: #333;
      }

      .modal-body i {
        color: var(--dms-blue);
      }

      .modal-footer {
        border-top: 1px solid #dee2e6;
        padding: 15px;
      }

      .btn-send-email {
        background-color: var(--dms-blue);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
      }

      .btn-send-email:hover {
        background-color: var(--dms-dark-blue);
        color: white;
      }
    </style>
</head>

<body>
    <!-- Background decorative elements -->
    <div class="bg-shapes">
      <div class="shape-1"></div>
      <div class="shape-2"></div>
      <div class="shape-3"></div>
      <div class="shape-4"></div>
      <div class="shape-5"></div>
      <div class="shape-6"></div>
    </div>

    <!-- Animated document icons in background -->
    <div class="bg-icons">
      <i class="fas fa-search"></i>
      <i class="fas fa-file-pdf"></i>
      <i class="fas fa-folder-open"></i>
      <i class="fas fa-chart-line"></i>
      <i class="fas fa-clock"></i>
      <i class="fas fa-file-alt"></i>
    </div>

    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <img
            src="assets/images/logo.png"
            alt="DMS Logo"
            class="logo-img"
            onerror="this.src='assets/images/1x1.png'"
          />
          <div class="organization-name">Welcome Back</div>
          <div class="system-name">Document Management System</div>
        </div>

        <div class="login-body">
          <?php if ($error_message): ?>
          <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo htmlspecialchars($error_message); ?>
          </div>
          <?php endif; ?>

          <?php if ($success_message): ?>
          <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success_message); ?>
          </div>
          <?php endif; ?>

          <form method="POST" action="" id="loginForm">
            <!-- Email or Username -->
            <div class="mb-3">
              <label for="email" class="form-label">Email or Username</label>
              <div class="icon-input">
                <i class="bi bi-envelope-fill input-icon"></i>
                <input
                  type="text"
                  class="form-control"
                  id="email"
                  name="email"
                  placeholder="Email or Username"
                  required
                  value="<?php echo htmlspecialchars($remembered_user ?: ($_POST['email'] ?? '')); ?>"
                />
              </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="icon-input">
                <i class="bi bi-lock-fill input-icon"></i>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  name="password"
                  placeholder="Enter your password"
                  required
                />
                <button
                  type="button"
                  class="password-toggle"
                  id="togglePassword"
                >
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <!-- Remember Me + Forgot Password -->
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="rememberMe"
                  name="rememberMe"
                  <?php echo $remembered_user ? 'checked' : ''; ?>
                />
                <label class="form-check-label" for="rememberMe">
                  Remember me
                </label>
              </div>
              <a href="#" class="forgot-password" id="forgotPassword">
                Forgot Password?
              </a>
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn btn-login" id="loginButton">
              <span id="loginText">Login</span>
              <div class="loading-spinner" id="loginSpinner"></div>
            </button>

            <div class="text-center mt-3">
              <a href="index.php" class="back-home">
                <i class="bi bi-arrow-left"></i> Back to Homepage
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Contact Administrator Modal -->
    <div
      class="modal fade"
      id="contactModal"
      tabindex="-1"
      aria-labelledby="contactModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="contactModalLabel">
              <i class="bi bi-person-fill-gear me-2"></i>Contact Administrator
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p class="mb-3"><strong>System Administrator:</strong></p>
            <p class="mb-2">
              <i class="bi bi-envelope-fill me-2"></i>genrey570@gmail.com
            </p>
            <p class="mb-0">
              <i class="bi bi-telephone-fill me-2"></i>09913910935
            </p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
            <button
              type="button"
              class="btn btn-send-email"
              onclick="window.location.href='mailto:genrey570@gmail.com?subject=Document%20Management%20System%20-%20Password%20Reset%20Request'"
            >
              <i class="bi bi-envelope-fill me-2"></i>Send Email
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
      // Toggle password visibility
      document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
      });

      // Forgot Password - Show Contact Modal
      document.getElementById('forgotPassword').addEventListener('click', function(e) {
        e.preventDefault();
        const contactModal = new bootstrap.Modal(document.getElementById('contactModal'));
        contactModal.show();
      });

      // Show loading spinner on form submit
      document.getElementById('loginForm').addEventListener('submit', function() {
        document.getElementById('loginButton').disabled = true;
        document.getElementById('loginSpinner').style.display = 'block';
        document.getElementById('loginText').textContent = 'Logging in...';
      });

      // Auto-hide alerts after 5 seconds
      setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
          alert.style.transition = 'opacity 0.5s';
          alert.style.opacity = '0';
          setTimeout(() => alert.remove(), 500);
        });
      }, 5000);
    </script>
</body>
</html>