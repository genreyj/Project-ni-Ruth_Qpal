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
    <title>Super Admin Dashboard</title>
    
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
        
        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #34495e 100%);
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
        
        .department-badge {
            background: rgba(13, 110, 253, 0.3);
            padding: 8px 12px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.85rem;
            text-align: center;
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
            color: rgba(255,255,255,0.8);
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
            background: var(--primary-color);
            color: white;
            border-left: 4px solid white;
        }
        
        .sidebar-menu i {
            width: 20px;
            text-align: center;
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
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .user-details {
            text-align: right;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: #6c757d;
            margin: 0;
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
            <a href="dashboard.php" class="sidebar-brand">
                <i class="bi bi-file-earmark-text"></i>
                <span>DMS - Dept Admin</span>
            </a>
            <div class="department-badge">
                <i class="bi bi-building"></i> <?php echo htmlspecialchars($department_name); ?>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="dept_users.php"><i class="bi bi-people"></i> Department Users</a></li>
            <li><a href="dept_documents.php"><i class="bi bi-file-earmark-text"></i> Documents</a></li>
            <li><a href="approval_queue.php"><i class="bi bi-check-circle"></i> Approval Queue</a></li>
            <li><a href="dept_reports.php"><i class="bi bi-graph-up"></i> Reports</a></li>
            <li><a href="../../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <h1 class="page-title">Department Dashboard</h1>
            <div class="user-info">
                <div class="user-details">
                    <p class="user-name"><?php echo htmlspecialchars($full_name); ?></p>
                    <p class="user-role"><?php echo htmlspecialchars($role_name); ?></p>
                </div>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($full_name, 0, 1)); ?>
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
</body>
</html>
