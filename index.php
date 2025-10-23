<?php
/**
 * Landing Page - Document Management System
 * Pamantasan ng Lungsod ng Pasig
 */

// Start session
session_start();

// Include database configuration
require_once 'config/database.php';

// Check if user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role_id'])) {
    $role_id = $_SESSION['role_id'];
    
    // Redirect based on role
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
        default:
            // Invalid role, destroy session
            session_destroy();
            break;
    }
}

// Initialize database connection flag
$db_connected = false;
$total_users = 0;
$total_departments = 0;
$total_documents = 0;

// Try to connect to database and fetch stats
try {
    $pdo = getDBConnection();
    $db_connected = true;
    
    // Get total users count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $result = $stmt->fetch();
    $total_users = $result['count'];
    
    // Get total departments count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM departments WHERE is_active = 1");
    $result = $stmt->fetch();
    $total_departments = $result['count'];
    
    // Get total documents count (if table exists)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM documents");
        $result = $stmt->fetch();
        $total_documents = $result['count'];
    } catch (Exception $e) {
        // Documents table might not exist yet
        $total_documents = 0;
    }
    
} catch (Exception $e) {
    // Database connection failed - show landing page anyway
    $db_connected = false;
    error_log("Database connection error on landing page: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document Management System - Pamantasan ng Lungsod ng Pasig</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Bootstrap Icons -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css"
      rel="stylesheet"
    />

    <style>
      :root {
        --dms-blue: #0d6efd;
        --dms-light-blue: #cfe2ff;
        --dms-dark-blue: #084298;
      }

      body {
        background-color: #f8f9fa;
        font-family: "Arial", sans-serif;
        padding-top: 100px; /* prevent navbar overlap */
      }

      .navbar-custom {
        background-color: white;
        border-bottom: 3px solid var(--dms-blue);
        padding: 15px 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
      }

      .logo-img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        object-fit: cover;
      }

      .logo-img:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
      }

      .organization-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--dms-dark-blue);
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 5px 10px;
        border-radius: 5px;
      }

      .organization-name:hover {
        background-color: var(--dms-light-blue);
        transform: translateY(-2px);
      }

      .brand-container {
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 10px;
        transition: all 0.3s ease;
      }

      .brand-container:hover {
        background-color: var(--dms-light-blue);
      }

      .btn-home {
        background-color: var(--dms-light-blue);
        border: 2px solid var(--dms-blue);
        color: var(--dms-dark-blue);
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
      }

      .btn-home:hover {
        background-color: var(--dms-blue);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
        text-decoration: none;
      }

      .btn-login {
        background-color: var(--dms-blue);
        border: 2px solid var(--dms-blue);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
      }

      .btn-login:hover {
        background-color: var(--dms-dark-blue);
        border-color: var(--dms-dark-blue);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(8, 66, 152, 0.3);
        text-decoration: none;
      }

      /* navbar toggler icon for white bg */
      .navbar-toggler {
        border-color: var(--dms-blue);
      }

      .navbar-toggler:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
      }

      .main-content {
        padding: 60px 0;
        min-height: 70vh;
      }

      .content-title {
        font-size: 3.5rem;
        font-weight: bold;
        color: var(--dms-blue);
        text-align: center;
        margin-bottom: 50px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
      }

      .features-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--dms-dark-blue);
        text-align: center;
        margin-bottom: 50px;
      }

      .feature-card {
        background: white;
        border: 2px solid var(--dms-light-blue);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        min-height: 200px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }

      .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(13, 110, 253, 0.2);
        border-color: var(--dms-blue);
      }

      .feature-icon {
        font-size: 3rem;
        color: var(--dms-blue);
        margin-bottom: 15px;
        text-align: center;
        display: block;
      }

      .feature-title {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--dms-dark-blue);
        margin-bottom: 15px;
        text-align: center;
      }

      .feature-description {
        color: #666;
        text-align: center;
        line-height: 1.5;
      }

      .hero-section {
        background: linear-gradient(
          135deg,
          var(--dms-light-blue) 0%,
          white 100%
        );
        padding: 40px 0;
        margin-bottom: 50px;
        border-radius: 20px;
      }

      .system-description {
        font-size: 1.2rem;
        color: var(--dms-dark-blue);
        text-align: center;
        margin-bottom: 30px;
        line-height: 1.6;
      }

      .cta-section {
        text-align: center;
        padding: 40px 0;
      }

      .btn-cta {
        background-color: var(--dms-blue);
        border: none;
        color: white;
        padding: 15px 40px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 30px;
        transition: all 0.3s ease;
      }

      .btn-cta:hover {
        background-color: var(--dms-dark-blue);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(8, 66, 152, 0.4);
        color: white;
      }

      .fade-in {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease;
      }

      .fade-in.active {
        opacity: 1;
        transform: translateY(0);
      }

      .bounce-in {
        animation: bounceIn 1s ease-out;
      }

      @keyframes bounceIn {
        0% {
          opacity: 0;
          transform: scale(0.3);
        }
        50% {
          opacity: 1;
          transform: scale(1.05);
        }
        70% {
          transform: scale(0.9);
        }
        100% {
          opacity: 1;
          transform: scale(1);
        }
      }

      footer {
        background-color: #1a1a2e;
        color: white;
        padding: 30px 0;
        margin-top: 60px;
      }

      .footer-bottom {
        text-align: center;
        padding: 20px 0;
      }

      .footer-bottom p {
        margin: 0;
        color: #ccc;
      }

      @media (max-width: 768px) {
        .organization-name {
          font-size: 1rem;
        }

        .content-title {
          font-size: 2.5rem;
        }

        .features-title {
          font-size: 2rem;
        }

        .brand-container {
          gap: 10px;
        }

        .logo-img {
          width: 50px;
          height: 50px;
        }

        body {
          padding-top: 80px;
        }
      }
    </style>
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
            onerror="this.src='assets/images/1x1.png'"
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


      <!-- Database Connection Status -->
      <?php if (!$db_connected): ?>
      <div class="alert alert-warning text-center mx-auto" style="max-width: 600px; margin-bottom: 30px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Database Not Connected</strong><br>
        <small>Please run the database setup to initialize the system.</small><br>
        <a href="database/setup.php" class="btn btn-sm btn-primary mt-2">
          <i class="bi bi-database-fill-gear me-1"></i>Setup Database
        </a>
      </div>
      <?php endif; ?>

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
        <p>&copy; <?php echo date('Y'); ?> Document Management System. All rights reserved.</p>
      </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
      // Contact Administrator Modal Function
      function showContactModal() {
        const modalHtml = `
          <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header" style="background-color: var(--dms-light-blue);">
                  <h5 class="modal-title" id="contactModalLabel" style="color: var(--dms-dark-blue);">
                    <i class="bi bi-person-fill-gear me-2"></i>Contact Administrator
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <p><strong>To request access to the Document Management System:</strong></p>
                  <ul>
                    <li>Provide your Gmail address for account creation</li>
                    <li>Specify your required access level</li>
                    <li>Wait for account activation via email</li>
                  </ul>
                  <hr>
                  <p><strong>System Administrator:</strong></p>
                  <p><i class="bi bi-envelope-fill me-2"></i>genrey570@gmail.com</p>
                  <p><i class="bi bi-telephone-fill me-2"></i>09913910935</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-login" onclick="window.location.href='mailto:genrey570@gmail.com?subject=Document%20Management%20System%20Access%20Request'">
                    <i class="bi bi-envelope-fill me-2"></i>Send Email
                  </button>
                </div>
              </div>
            </div>
          </div>
        `;

        // Remove existing modal if any
        $("#contactModal").remove();

        // Add modal to body and show
        $("body").append(modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('contactModal'));
        modal.show();
      }

      // Event listener for Contact Administrator button
      document
        .getElementById("contactAdminBtn")
        .addEventListener("click", function (e) {
          e.preventDefault();
          showContactModal();
        });

      // Event listener for Learn More button - scroll to Key Features
      document
        .getElementById("getStartedBtn")
        .addEventListener("click", function (e) {
          e.preventDefault();

          // Scroll to Key Features section
          const keyFeaturesSection = document.getElementById("keyFeatures");
          const offsetTop = keyFeaturesSection.offsetTop - 100; // Account for fixed navbar

          window.scrollTo({
            top: offsetTop,
            behavior: "smooth",
          });
        });

      // Smooth scrolling for Home button
      document
        .getElementById("homeBtn")
        .addEventListener("click", function (e) {
          e.preventDefault();

          // Scroll to the top of the page smoothly
          window.scrollTo({
            top: 0,
            behavior: "smooth",
          });
        });

      // Smooth scrolling for Brand Container (Logo + Organization Name)
      document
        .getElementById("brandContainer")
        .addEventListener("click", function (e) {
          e.preventDefault();

          // Scroll to the top of the page smoothly
          window.scrollTo({
            top: 0,
            behavior: "smooth",
          });
        });

      // Animation on scroll for fade-in elements
      document.addEventListener("DOMContentLoaded", function () {
        const fadeElements = document.querySelectorAll(".fade-in");

        const fadeInOnScroll = function () {
          fadeElements.forEach((element) => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;

            if (elementTop < window.innerHeight - elementVisible) {
              element.classList.add("active");
            }
          });
        };

        // Check on load
        fadeInOnScroll();

        // Check on scroll
        window.addEventListener("scroll", fadeInOnScroll);
      });

      // Smooth scrolling for anchor links
      $('a[href^="#"]').on('click', function (e) {
        const target = $($(this).attr("href"));
        if (target.length) {
          e.preventDefault();
          $("html, body").animate(
            {
              scrollTop: target.offset().top - 70,
            },
            800
          );
        }
      });

      // Add loading animation
      $(window).on("load", function () {
        $(".fade-in").first().addClass("active");
      });

      // Add keyboard navigation
      $(document).on('keydown', function (e) {
        // Escape key closes any open modals
        if (e.keyCode === 27) {
          const modals = document.querySelectorAll('.modal.show');
          modals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
              modalInstance.hide();
            }
          });
        }
      });
    </script>
  </body>
</html>
