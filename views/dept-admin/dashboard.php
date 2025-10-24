<?php

session_start();

require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    header('Location: ../../login.php');
    exit();
}

if ($_SESSION['role_id'] != ROLE_DEPT_ADMIN) {
    header('Location: ../../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role_name = $_SESSION['role_name'];
$department_id = $_SESSION['department_id'];
$department_name = $_SESSION['department_name'];

$dept_documents = 0;
$dept_users = 0;
$pending_requests = 0;
$recent_activities = [];
$user_list = [];
$document_stats = [];

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE department_id = ? AND is_active = 1");
    $stmt->execute([$department_id]);
    $dept_users = $stmt->fetch()['count'];
    
    // Get department documents count (if table exists)
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM documents WHERE department_id = ?");
        $stmt->execute([$department_id]);
        $dept_documents = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $dept_documents = 0;
    }
    
    // Get pending requests count (if table exists)
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM documents WHERE department_id = ? AND status = 'pending'");
        $stmt->execute([$department_id]);
        $pending_requests = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $pending_requests = 0;
    }
    
    // Get recent activities for this department
    $stmt = $pdo->prepare("
        SELECT a.*, u.username, u.first_name, u.last_name 
        FROM audit_trails a 
        LEFT JOIN users u ON a.user_id = u.user_id 
        WHERE u.department_id = ? OR a.user_id = ?
        ORDER BY a.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$department_id, $user_id]);
    $recent_activities = $stmt->fetchAll();
    
    // Get department user list
    $stmt = $pdo->prepare("
        SELECT u.*, r.role_name 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.role_id 
        WHERE u.department_id = ? 
        ORDER BY u.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$department_id]);
    $user_list = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1e88e5;
            --secondary-color: #6c757d;
            --success-color: #1e88e5;
            --danger-color: #1e88e5;
            --warning-color: #1e88e5;
            --info-color: #1e88e5;
            --teal-color: #1e88e5;
            --sidebar-bg: #0b2545;
            --sidebar-hover: #163a5f;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 100;
            padding: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .profile-image {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .sidebar-brand {
            font-size: 1.1rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 5px;
        }
        
        .sidebar-subtitle {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .menu-section-title {
            padding: 20px 20px 10px;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .sidebar-menu li {
            margin: 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            gap: 12px;
            font-size: 0.9rem;
        }
        
        .sidebar-menu a:hover {
            background: var(--sidebar-hover);
            color: white;
        }
        
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 3px solid var(--primary-color);
        }
        
        .sidebar-menu i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 0;
            min-height: 100vh;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .page-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .page-title i {
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .notification-icon, .settings-icon {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s;
            color: #666;
        }
        
        .notification-icon:hover, .settings-icon:hover {
            background: #f0f0f0;
        }
        
        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #dc3545;
            color: white;
            font-size: 0.65rem;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .user-dropdown {
            position: relative;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--teal-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }
        
        .user-avatar:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .dropdown-menu-custom {
            position: absolute;
            top: 60px;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            min-width: 250px;
            display: none;
            z-index: 1000;
            overflow: hidden;
        }
        
        .dropdown-menu-custom.show {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .dropdown-header {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .dropdown-header h6 {
            margin: 0 0 5px;
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .dropdown-header h5 {
            margin: 0;
            font-size: 1rem;
            color: #333;
            font-weight: 600;
        }
        
        .dropdown-body {
            padding: 10px;
        }
        
        .dropdown-item-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .dropdown-item-custom:hover {
            background: #f0f0f0;
        }
        
        .dropdown-item-custom i {
            width: 20px;
            text-align: center;
        }
        
        .dropdown-footer {
            padding: 10px;
            border-top: 1px solid #e9ecef;
        }
        
        .btn-logout {
            width: 100%;
            padding: 10px;
            background: var(--teal-color);
            color: white;
            border: none;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            text-decoration: none;
        }
        
        .btn-logout:hover {
            background: #138496;
            color: white;
        }
        
        
        /* Dashboard Cards */
        .content-area {
            padding: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card.primary {
            border-left-color: var(--primary-color);
        }
        
        .stat-card.success {
            border-left-color: var(--success-color);
        }
        
        .stat-card.warning {
            border-left-color: var(--warning-color);
        }
        
        .stat-card.info {
            border-left-color: var(--info-color);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        
        .stat-icon.primary {
            background: rgba(13, 110, 253, 0.1);
            color: var(--primary-color);
        }
        
        .stat-icon.success {
            background: rgba(25, 135, 84, 0.1);
            color: var(--success-color);
        }
        
        .stat-icon.warning {
            background: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }
        
        .stat-icon.info {
            background: rgba(13, 202, 240, 0.1);
            color: var(--info-color);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }
        
        /* Table Styles */
        .data-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .table-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .table {
            margin: 0;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="profile-image">
                <img src="../../assets/images/jr.png" alt="Admin" onerror="this.parentElement.innerHTML='<i class=\'bi bi-person-circle\' style=\'font-size: 3rem; color: var(--sidebar-bg);\'></i>';">
            </div>
            <div class="sidebar-brand">Jericho Riga</div>
            <div class="sidebar-subtitle">Department Admin</div>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="#dashboardSection" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            
            <li class="menu-section-title">ADMIN TOOLS</li>
            <li><a href="#documentManagement" class="nav-link"><i class="bi bi-file-earmark-text"></i> Document Management</a></li>
            <li><a href="#departmentManagement" class="nav-link"><i class="bi bi-building"></i> Department Management</a></li>
            <li><a href="#auditTrails" class="nav-link"><i class="bi bi-clock-history"></i> Audit Trails</a></li>
            
            <li class="menu-section-title">SETTING</li>
            <li><a href="#setting" class="nav-link"><i class="bi bi-info-circle"></i> Setting</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
     <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <h1 class="page-title">
                <i class="bi bi-speedometer2"></i>
                <span class="dashboard-title">DASHBOARD</span>
            </h1>
            <div class="navbar-right">
                <div class="notification-icon">
                    <i class="bi bi-bell" style="font-size: 1.2rem;"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="settings-icon">
                    <i class="bi bi-question-circle" style="font-size: 1.2rem;"></i>
                </div>
                <div class="user-dropdown">
                    <div class="user-avatar" onclick="toggleDropdown()">
                        DA
                    </div>
                    <div class="dropdown-menu-custom" id="userDropdown">
                        <div class="dropdown-header">
                            <h6>Department Admin</h6>
                            <h5>Hi, Admin!</h5>
                        </div>
                        <div class="dropdown-body">
                            <a href="profile.php" class="dropdown-item-custom">
                                <i class="bi bi-person"></i>
                                Manage Account
                            </a>
                        </div>
                        <div class="dropdown-footer">
                            <a href="../../logout.php" class="btn-logout">
                                <i class="bi bi-box-arrow-right"></i>
                                Log Out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            <!-- Department Info Alert -->
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Department:</strong> <?php echo htmlspecialchars($department_name); ?>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card primary">
                        <div class="stat-icon primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3 class="stat-value"><?php echo $dept_users; ?></h3>
                        <p class="stat-label">Department Users</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card primary">
                        <div class="stat-icon primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3 class="stat-value"><?php echo $dept_users; ?></h3>
                        <p class="stat-label">Department Users</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card warning">
                        <div class="stat-icon warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h3 class="stat-value"><?php echo $pending_requests; ?></h3>
                        <p class="stat-label">Pending Requests</p>
                    </div>
                </div>
            </div>
             
            <!-- Recent Activities -->
            <div class="data-table">
                <div class="table-header">
                    <h2 class="table-title"><i class="bi bi-clock-history me-2"></i>Recent Activities</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Table</th>
                                <th>IP Address</th>
                                <th>Date/Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_activities)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No activities found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($recent_activities as $activity): ?>
                                <tr>
                                    <td>
                                        <?php if ($activity['username']): ?>
                                            <?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            <?php 
                                            echo match($activity['action_type']) {
                                                'LOGIN' => 'bg-success',
                                                'LOGOUT' => 'bg-secondary',
                                                'LOGIN_FAILED' => 'bg-danger',
                                                'CREATE' => 'bg-primary',
                                                'UPDATE' => 'bg-warning',
                                                'DELETE' => 'bg-danger',
                                                default => 'bg-info'
                                            };
                                            ?>">
                                            <?php echo htmlspecialchars($activity['action_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($activity['table_name']); ?></td>
                                    <td><small><?php echo htmlspecialchars($activity['ip_address']); ?></small></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($activity['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
     <script>
        // Toggle user dropdown
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.user-avatar')) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        }

        $(document).ready(function() {
            var initialDashboardContent = $("#contentArea").html();

            // Sidebar nav active state and section switching
            $(document).on("click", ".sidebar .nav-link", function (e) {
                var href = $(this).attr("href");
                if (href && href.startsWith("#")) {
                    e.preventDefault();
                    $(".sidebar .nav-link").removeClass("active");
                    $(this).addClass("active");

                    // Update top header title
                    const titles = {
                    "#dashboardSection": "DASHBOARD",
                    "#departmentManagement": "DEPARTMENT MANAGEMENT",
                    "#documentManagement": "DOCUMENT MANAGEMENT",
                    "#auditTrails": "AUDIT TRAILS",
                    "#setting": "SETTING",
                    };
                    $(".dashboard-title").text(titles[href] || "DASHBOARD");

                    // Load section content
                    if (href === "#dashboardSection") {
                        $("#contentArea").html(initialDashboardContent);
                    } else {
                    // For other sections, show placeholder
                    $("#contentArea").html(
                        '<div class="card"><div class="card-body"><h5 class="card-title">' +
                        titles[href] +
                        '</h5><div class="text-muted">Content for ' +
                        titles[href] +
                        " will appear here.</div></div></div>"
                    );
                    }
                }
            });
        });
    </script>
</body>
</html>
