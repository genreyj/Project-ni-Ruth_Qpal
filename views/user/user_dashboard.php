<?php
/**
 * User Dashboard - Document Management System
 * Pamantasan ng Lungsod ng Pasig
 */

// Start session
session_start();

// Include database configuration
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Check if user is a regular user
if ($_SESSION['role_id'] != ROLE_USER) {
    header('Location: ../../login.php');
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role_name = $_SESSION['role_name'];
$department_id = $_SESSION['department_id'];
$department_name = $_SESSION['department_name'];
$email = $_SESSION['email'];

// Initialize variables
$my_documents = 0;
$shared_documents = 0;
$recent_documents = [];
$recent_activities = [];
$notifications_count = 0;

try {
    $pdo = getDBConnection();
    
    // Get my documents count (if table exists)
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM documents WHERE created_by = ?");
        $stmt->execute([$user_id]);
        $my_documents = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $my_documents = 0;
    }
    
    // Get shared documents count (if table exists)
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT d.document_id) as count 
            FROM documents d 
            LEFT JOIN document_permissions dp ON d.document_id = dp.document_id 
            WHERE dp.user_id = ? OR d.department_id = ?
        ");
        $stmt->execute([$user_id, $department_id]);
        $shared_documents = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $shared_documents = 0;
    }
    
    // Get recent documents (if table exists)
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM documents 
            WHERE created_by = ? 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $recent_documents = $stmt->fetchAll();
    } catch (Exception $e) {
        $recent_documents = [];
    }
    
    // Get my recent activities
    $stmt = $pdo->prepare("
        SELECT a.* 
        FROM audit_trails a 
        WHERE a.user_id = ?
        ORDER BY a.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $recent_activities = $stmt->fetchAll();
    
    // Get notifications count (if table exists)
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user_id]);
        $notifications_count = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $notifications_count = 0;
    }
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Document Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --sidebar-bg: #16a085;
            --sidebar-hover: #1abc9c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #1abc9c 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            z-index: 100;
            padding: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand {
            font-size: 1.3rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-profile {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            text-align: center;
        }
        
        .user-profile-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: white;
            color: var(--sidebar-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 auto 10px;
        }
        
        .user-profile-name {
            font-weight: 600;
            margin: 0;
            font-size: 0.95rem;
        }
        
        .user-profile-dept {
            font-size: 0.75rem;
            opacity: 0.8;
            margin: 0;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s;
            gap: 12px;
        }
        
        .sidebar-menu a:hover {
            background: var(--sidebar-hover);
            color: white;
            padding-left: 25px;
        }
        
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.2);
            color: white;
            border-left: 4px solid white;
        }
        
        .sidebar-menu i {
            width: 20px;
            text-align: center;
        }
        
        .notification-badge {
            background: var(--danger-color);
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.7rem;
            margin-left: auto;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 0;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .top-actions {
            display: flex;
            gap: 10px;
        }
        
        /* Dashboard Cards */
        .content-area {
            padding: 30px;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, var(--sidebar-bg) 0%, #1abc9c 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .welcome-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .welcome-text {
            opacity: 0.9;
            margin: 0;
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
        
        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        
        .quick-actions-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            margin-bottom: 10px;
        }
        
        .action-btn:hover {
            background: var(--sidebar-bg);
            color: white;
            transform: translateX(5px);
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
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
            <a href="user_dashboard.php" class="sidebar-brand">
                <i class="bi bi-file-earmark-text"></i>
                <span>DMS - User</span>
            </a>
            <div class="user-profile">
                <div class="user-profile-avatar">
                    <?php echo strtoupper(substr($full_name, 0, 1)); ?>
                </div>
                <p class="user-profile-name"><?php echo htmlspecialchars($full_name); ?></p>
                <p class="user-profile-dept"><?php echo htmlspecialchars($department_name ?? 'No Department'); ?></p>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="user_dashboard.php" class="active">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="my_documents.php">
                    <i class="bi bi-file-earmark-text"></i> My Documents
                </a>
            </li>
            <li>
                <a href="shared_documents.php">
                    <i class="bi bi-folder-symlink"></i> Shared with Me
                </a>
            </li>
            <li>
                <a href="upload_document.php">
                    <i class="bi bi-cloud-upload"></i> Upload Document
                </a>
            </li>
            <li>
                <a href="notifications.php">
                    <i class="bi bi-bell"></i> Notifications
                    <?php if ($notifications_count > 0): ?>
                        <span class="notification-badge"><?php echo $notifications_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="profile.php">
                    <i class="bi bi-person"></i> My Profile
                </a>
            </li>
            <li>
                <a href="../../logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <h1 class="page-title">My Dashboard</h1>
            <div class="top-actions">
                <a href="upload_document.php" class="btn btn-primary">
                    <i class="bi bi-cloud-upload me-2"></i>Upload Document
                </a>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h2 class="welcome-title">
                    <i class="bi bi-emoji-smile me-2"></i>Welcome back, <?php echo htmlspecialchars(explode(' ', $full_name)[0]); ?>!
                </h2>
                <p class="welcome-text">
                    <i class="bi bi-calendar3 me-2"></i><?php echo date('l, F d, Y'); ?>
                </p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card primary">
                        <div class="stat-icon primary">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <h3 class="stat-value"><?php echo $my_documents; ?></h3>
                        <p class="stat-label">My Documents</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card success">
                        <div class="stat-icon success">
                            <i class="bi bi-folder-symlink-fill"></i>
                        </div>
                        <h3 class="stat-value"><?php echo $shared_documents; ?></h3>
                        <p class="stat-label">Shared with Me</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card warning">
                        <div class="stat-icon warning">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h3 class="stat-value"><?php echo $notifications_count; ?></h3>
                        <p class="stat-label">Notifications</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Quick Actions -->
                <div class="col-md-4">
                    <div class="quick-actions">
                        <h3 class="quick-actions-title">
                            <i class="bi bi-lightning-fill me-2"></i>Quick Actions
                        </h3>
                        
                        <a href="upload_document.php" class="action-btn">
                            <div class="action-icon" style="background: rgba(13, 110, 253, 0.1); color: var(--primary-color);">
                                <i class="bi bi-cloud-upload"></i>
                            </div>
                            <div>
                                <strong>Upload Document</strong>
                                <br><small class="text-muted">Add new document</small>
                            </div>
                        </a>
                        
                        <a href="my_documents.php" class="action-btn">
                            <div class="action-icon" style="background: rgba(25, 135, 84, 0.1); color: var(--success-color);">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div>
                                <strong>My Documents</strong>
                                <br><small class="text-muted">View all my files</small>
                            </div>
                        </a>
                        
                        <a href="shared_documents.php" class="action-btn">
                            <div class="action-icon" style="background: rgba(255, 193, 7, 0.1); color: var(--warning-color);">
                                <i class="bi bi-folder-symlink"></i>
                            </div>
                            <div>
                                <strong>Shared Documents</strong>
                                <br><small class="text-muted">Files shared with me</small>
                            </div>
                        </a>
                        
                        <a href="profile.php" class="action-btn">
                            <div class="action-icon" style="background: rgba(13, 202, 240, 0.1); color: var(--info-color);">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <strong>My Profile</strong>
                                <br><small class="text-muted">Update profile info</small>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Recent Documents -->
                <div class="col-md-8">
                    <div class="data-table">
                        <div class="table-header">
                            <h2 class="table-title"><i class="bi bi-clock-history me-2"></i>Recent Documents</h2>
                            <a href="my_documents.php" class="btn btn-primary btn-sm">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Document Name</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_documents)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox display-4 d-block mb-3 text-secondary"></i>
                                            <p>No documents yet. Upload your first document!</p>
                                            <a href="upload_document.php" class="btn btn-primary btn-sm">
                                                <i class="bi bi-cloud-upload me-1"></i>Upload Now
                                            </a>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_documents as $doc): ?>
                                        <tr>
                                            <td>
                                                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                                <?php echo htmlspecialchars($doc['document_name'] ?? $doc['title']); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($doc['category'] ?? 'General'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    <?php 
                                                    echo match($doc['status'] ?? 'pending') {
                                                        'approved' => 'bg-success',
                                                        'pending' => 'bg-warning',
                                                        'rejected' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                    ?>">
                                                    <?php echo ucfirst($doc['status'] ?? 'Pending'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($doc['created_at'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activities -->
            <div class="data-table">
                <div class="table-header">
                    <h2 class="table-title"><i class="bi bi-activity me-2"></i>My Recent Activities</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Table</th>
                                <th>IP Address</th>
                                <th>Date/Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_activities)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No activities found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($recent_activities as $activity): ?>
                                <tr>
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
</body>
</html>
