<?php
// Basic router
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$reqPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('#^' . preg_quote($base, '#') . '/?#', '', $reqPath);

// Simple routes
switch ($path) {
    case '':
    case 'home':
        echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document Management System</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
    
  </head>
  <body>
    <!-- Fixed Responsive Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
      <div class="container">
        <!-- Logo + Organization Name -->
        <div class="brand-container" id="brandContainer">
          <img
            src="assets/images/logo.png"
            alt="PLP Logo"
            class="logo-img"
            id="logoImg"
          />
          <span class="organization-name">Pamantasan ng Lungsod ng Pasig</span>
        </div>

        <!-- Toggler for mobile -->
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarMenu"
          aria-controls="navbarMenu"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Right-side buttons -->
        <div
          class="collapse navbar-collapse justify-content-end"
          id="navbarMenu"
        >
          <div class="d-flex gap-3 mt-3 mt-lg-0">
            <a href="#" class="btn btn-home" id="homeBtn">
              <i class="bi bi-house-fill me-2"></i>Home
            </a>
            <a href="login.php" class="btn btn-login" id="loginBtn">
              <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container main-content">
      <!-- Hero Section -->
      <div class="hero-section fade-in active" id="heroSection">
        <div class="container">
          <h1 class="content-title bounce-in">Document Management System</h1>
          <p class="system-description">
            A comprehensive and secure document management solution. Streamline
            your document workflows with advanced security, role-based access
            control, and automated processes.
          </p>
          <div class="cta-section">
            <button class="btn btn-cta" id="getStartedBtn">
              <i class="bi bi-arrow-right-circle me-2"></i>Learn more
            </button>
          </div>
        </div>
      </div>

      <!-- Key Features Section -->
      <h2 class="features-title fade-in" id="keyFeatures">Key Features</h2>

      <div class="row" id="featuresContainer">
        <!-- Feature 1 -->
        <div class="col-md-4">
          <div class="feature-card fade-in">
            <i class="bi bi-shield-check feature-icon"></i>
            <h3 class="feature-title">Secure Document Storage</h3>
            <p class="feature-description">
              All documents are encrypted and securely stored with role-based
              access control to ensure data confidentiality.
            </p>
          </div>
        </div>

        <!-- Feature 2 -->
        <div class="col-md-4">
          <div class="feature-card fade-in">
            <i class="bi bi-search feature-icon"></i>
            <h3 class="feature-title">Advanced Search</h3>
            <p class="feature-description">
              Quickly find documents using our powerful search functionality
              with filters by date, type, and keywords.
            </p>
          </div>
        </div>

        <!-- Feature 3 -->
        <div class="col-md-4">
          <div class="feature-card fade-in">
            <i class="bi bi-graph-up feature-icon"></i>
            <h3 class="feature-title">Workflow Automation</h3>
            <p class="feature-description">
              Automate document approval processes with customizable workflows
              to increase efficiency.
            </p>
          </div>
        </div>

        <!-- Feature 4 -->
        <div class="col-md-4">
          <div class="feature-card fade-in">
            <i class="bi bi-clock-history feature-icon"></i>
            <h3 class="feature-title">Version Control</h3>
            <p class="feature-description">
              Track document changes with complete version history and rollback
              capabilities.
            </p>
          </div>
        </div>

        <!-- Feature 5 -->
        <div class="col-md-4">
          <div class="feature-card fade-in">
            <i class="bi bi-people feature-icon"></i>
            <h3 class="feature-title">Collaboration Tools</h3>
            <p class="feature-description">
              Enable team collaboration with commenting, annotation, and
              real-time editing features.
            </p>
          </div>
        </div>

        <!-- Feature 6 -->
        <div class="col-md-4">
          <div class="feature-card fade-in">
            <i class="bi bi-bar-chart feature-icon"></i>
            <h3 class="feature-title">Reporting & Analytics</h3>
            <p class="feature-description">
              Generate comprehensive reports on document usage, access patterns,
              and system performance.
            </p>
          </div>
        </div>
      </div>


      <!-- Call to Action -->
      <div class="cta-section fade-in">
        <h3 style="color: var(--dms-dark-blue); margin-bottom: 20px">
          Ready to Get Started?
        </h3>
        <p style="color: #666; margin-bottom: 30px">
          Contact system administrator to request access to the Document
          Management System.
        </p>
        <button class="btn btn-cta" id="contactAdminBtn">
          <i class="bi bi-person-fill-gear me-2"></i>Contact Administrator
        </button>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <div class="footer-bottom">
        <p>&copy;2025 Document Management System. All rights reserved.</p>
      </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Add jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/index.js"></script>
  </body>
</html>';
        break;

    // ...add more routes here as needed...

    default:
        http_response_code(404);
        require __DIR__ . '/404.php';
        break;
}
?>
