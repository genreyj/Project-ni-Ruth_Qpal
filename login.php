<?php
session_start();
include 'includes/db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($username !== '' && $password !== '') {
        $stmt = $conn->prepare("SELECT user_id, username, password, role, department_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $valid = false;
        if ($user) {
            // Prefer hashed passwords, fallback to legacy plaintext match
            if (password_verify($password, $user['password'])) {
                $valid = true;
            } elseif ($password === $user['password']) {
                $valid = true;
            }
        }

        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['department_id'] = $user['department_id'];

            if ($user['role'] == 'super_admin') {
                header("Location: pages/super-admin/adminDashboard.php");
            } elseif ($user['role'] == 'department_admin') {
                header("Location: pages/depart-admin/dashboard.php");
            } else {
                header("Location: pages/user/userDashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Please enter both username and password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Document Management System</title>
    <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="libs/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="libs/fontawesome/css/all.min.css">
    <link href="assets/css/login.css" rel="stylesheet">
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
          <?php if (!empty($error)): ?>
          <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
          </div>
          <?php endif; ?>

          <?php if (!empty($success)): ?>
          <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success); ?>
          </div>
          <?php endif; ?>

          <form method="POST" action="" id="loginForm">
            <!-- Username Field -->
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <div class="icon-input">
                <i class="bi bi-person-fill input-icon"></i>
                <input
                  type="text"
                  class="form-control"
                  id="username"
                  name="username"
                  placeholder="Enter your username"
                  required
                  value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
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
                  <?php echo isset($_POST['rememberMe']) ? 'checked' : ''; ?>
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
    <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
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