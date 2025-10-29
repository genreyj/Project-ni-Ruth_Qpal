<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Document Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1e88e5;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --teal-color: #20c997;
            --sidebar-bg: #0b2545;
            --sidebar-hover: #163a5f;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            overflow-x: hidden;
        }
        
        /* Sidebar */
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
        
        /* Content Area */
        .content-area {
            padding: 30px;
            background: #f5f7fa;
        }
        
        .content-box {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .content-box h2 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .content-box p {
            color: #666;
            line-height: 1.6;
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
        
        .btn-action {
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 5px;
        }
        
        /* Section Styles */
        .section {
            display: none;
        }
        
        .section.active {
            display: block;
        }
        
        /* Modal Styles */
        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        /* Alert Styles */
        .alert {
            border-radius: 8px;
            padding: 12px 16px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="profile-image">
                <i class='bi bi-person-circle' style='font-size: 3rem; color: var(--sidebar-bg);'></i>
            </div>
            <div class="sidebar-brand">ADMIN SYSTEM</div>
            <div class="sidebar-subtitle">Administrator</div>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="#dashboard" class="nav-link active" data-section="dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            
            <li class="menu-section-title">ADMIN TOOLS</li>
            <li><a href="#document-management" class="nav-link" data-section="document-management"><i class="bi bi-file-earmark-text"></i> Document Management</a></li>
            <li><a href="#department-management" class="nav-link" data-section="department-management"><i class="bi bi-building"></i> Department Management</a></li>
            <li><a href="#user-management" class="nav-link" data-section="user-management"><i class="bi bi-person-gear"></i> User Management</a></li>
            <li><a href="#database-management" class="nav-link" data-section="database-management"><i class="bi bi-database"></i> Database Management</a></li>
            <li><a href="#audit-trails" class="nav-link" data-section="audit-trails"><i class="bi bi-clock-history"></i> Audit Trails</a></li>
            
            <li class="menu-section-title">SETTING</li>
            <li><a href="#system-information" class="nav-link" data-section="system-information"><i class="bi bi-info-circle"></i> System Information</a></li>
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
                        SA
                    </div>
                    <div class="dropdown-menu-custom" id="userDropdown">
                        <div class="dropdown-header">
                            <h6>SUPER ADMIN</h6>
                            <h5>Hi, Admin!</h5>
                        </div>
                        <div class="dropdown-body">
                            <a href="#" class="dropdown-item-custom">
                                <i class="bi bi-person"></i>
                                Manage Account
                            </a>
                        </div>
                        <div class="dropdown-footer">
                            <a href="#" class="btn-logout">
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
            <!-- Dashboard Section -->
            <div class="section active" id="dashboard">
                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card primary">
                            <div class="stat-icon primary">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h3 class="stat-value">127</h3>
                            <p class="stat-label">Total Users</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card success">
                            <div class="stat-icon success">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <h3 class="stat-value">115</h3>
                            <p class="stat-label">Active Users</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <div class="stat-icon warning">
                                <i class="bi bi-building"></i>
                            </div>
                            <h3 class="stat-value">8</h3>
                            <p class="stat-label">Departments</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card info">
                            <div class="stat-icon info">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <h3 class="stat-value">2,458</h3>
                            <p class="stat-label">Documents</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="data-table">
                    <div class="table-header">
                        <h2 class="table-title"><i class="bi bi-clock-history me-2"></i>Recent Activities</h2>
                        <a href="#" class="btn btn-primary btn-sm">View All</a>
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
                                <tr>
                                    <td>John Smith</td>
                                    <td><span class="badge bg-success">LOGIN</span></td>
                                    <td>users</td>
                                    <td><small>192.168.1.105</small></td>
                                    <td>Mar 15, 2024 09:23 AM</td>
                                </tr>
                                <tr>
                                    <td>Sarah Johnson</td>
                                    <td><span class="badge bg-primary">CREATE</span></td>
                                    <td>documents</td>
                                    <td><small>192.168.1.112</small></td>
                                    <td>Mar 15, 2024 09:15 AM</td>
                                </tr>
                                <tr>
                                    <td>Michael Brown</td>
                                    <td><span class="badge bg-warning">UPDATE</span></td>
                                    <td>users</td>
                                    <td><small>192.168.1.108</small></td>
                                    <td>Mar 15, 2024 08:45 AM</td>
                                </tr>
                                <tr>
                                    <td>System</td>
                                    <td><span class="badge bg-info">BACKUP</span></td>
                                    <td>database</td>
                                    <td><small>127.0.0.1</small></td>
                                    <td>Mar 15, 2024 08:30 AM</td>
                                </tr>
                                <tr>
                                    <td>Lisa Anderson</td>
                                    <td><span class="badge bg-danger">DELETE</span></td>
                                    <td>documents</td>
                                    <td><small>192.168.1.120</small></td>
                                    <td>Mar 15, 2024 08:12 AM</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Document Management Section -->
            <div class="section" id="document-management">
                <div class="content-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="bi bi-file-earmark-text me-2"></i>Document Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Document
                        </button>
                    </div>
                    <p>Manage all documents in the system, including upload, categorization, and access control.</p>
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Document ID</th>
                                    <th>Title</th>
                                    <th>Department</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="documentsTableBody">
                                <tr>
                                    <td>DOC-00123</td>
                                    <td>Quarterly Financial Report</td>
                                    <td>Finance</td>
                                    <td>John Smith</td>
                                    <td>Mar 14, 2024</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-primary btn-sm">View</button>
                                            <button class="btn btn-warning btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>DOC-00122</td>
                                    <td>HR Policy Update</td>
                                    <td>Human Resources</td>
                                    <td>Sarah Johnson</td>
                                    <td>Mar 13, 2024</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-primary btn-sm">View</button>
                                            <button class="btn btn-warning btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Department Management Section -->
            <div class="section" id="department-management">
                <div class="content-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="bi bi-building me-2"></i>Department Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Department
                        </button>
                    </div>
                    <p>Manage organizational departments, assign managers, and configure department-specific settings.</p>
                    
                    <!-- Loading Spinner -->
                    <div id="departmentsLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading departments...</span>
                        </div>
                        <p class="mt-2">Loading departments...</p>
                    </div>
                    
                    <div class="table-responsive mt-4" id="departmentsTableContainer" style="display: none;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Department ID</th>
                                    <th>Department Name</th>
                                    <th>Manager</th>
                                    <th>User Count</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="departmentsTableBody">
                                <!-- Departments will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="departmentsEmpty" class="empty-state" style="display: none;">
                        <i class="bi bi-building"></i>
                        <h4>No Departments Found</h4>
                        <p>Get started by creating your first department.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Department
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- User Management Section -->
            <div class="section" id="user-management">
                <div class="content-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="bi bi-person-gear me-2"></i>User Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-plus-circle me-1"></i> Add User
                        </button>
                    </div>
                    <p>Manage system users, assign roles, and control access permissions.</p>
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Department</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <tr>
                                    <td>USR-00123</td>
                                    <td>John Smith</td>
                                    <td>john.smith</td>
                                    <td>Finance</td>
                                    <td>Manager</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-primary btn-sm">View</button>
                                            <button class="btn btn-warning btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>USR-00124</td>
                                    <td>Sarah Johnson</td>
                                    <td>sarah.johnson</td>
                                    <td>Human Resources</td>
                                    <td>Manager</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-primary btn-sm">View</button>
                                            <button class="btn btn-warning btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Database Management Section -->
            <div class="section" id="database-management">
                <div class="content-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="bi bi-database me-2"></i>Database Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBackupModal">
                            <i class="bi bi-plus-circle me-1"></i> Create Backup
                        </button>
                    </div>
                    <p>Manage database backups, perform maintenance, and monitor performance.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Backup Status</h5>
                                    <p class="card-text">Last backup: Mar 15, 2024 08:30 AM</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBackupModal">Create Backup</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Database Size</h5>
                                    <p class="card-text">Current size: 245 MB</p>
                                    <button class="btn btn-warning">Optimize</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="data-table mt-4">
                        <div class="table-header">
                            <h2 class="table-title"><i class="bi bi-database me-2"></i>Recent Backups</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Backup ID</th>
                                        <th>Date Created</th>
                                        <th>Size</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="backupsTableBody">
                                    <tr>
                                        <td>BK-00123</td>
                                        <td>Mar 15, 2024 08:30 AM</td>
                                        <td>245 MB</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-primary btn-sm">Download</button>
                                                <button class="btn btn-danger btn-sm">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>BK-00122</td>
                                        <td>Mar 14, 2024 08:30 AM</td>
                                        <td>240 MB</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-primary btn-sm">Download</button>
                                                <button class="btn btn-danger btn-sm">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Audit Trails Section -->
            <div class="section" id="audit-trails">
                <div class="content-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="bi bi-clock-history me-2"></i>Audit Trails</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportAuditModal">
                            <i class="bi bi-download me-1"></i> Export Logs
                        </button>
                    </div>
                    <p>View system activity logs and track user actions for security and compliance.</p>
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mar 15, 2024 09:23 AM</td>
                                    <td>John Smith</td>
                                    <td><span class="badge bg-success">LOGIN</span></td>
                                    <td>User logged in successfully</td>
                                    <td>192.168.1.105</td>
                                </tr>
                                <tr>
                                    <td>Mar 15, 2024 09:15 AM</td>
                                    <td>Sarah Johnson</td>
                                    <td><span class="badge bg-primary">CREATE</span></td>
                                    <td>Created new document: HR Policy Update</td>
                                    <td>192.168.1.112</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- System Information Section -->
            <div class="section" id="system-information">
                <div class="content-box">
                    <h2><i class="bi bi-info-circle me-2"></i>System Information</h2>
                    <p>View system configuration, version information, and server status.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">System Details</h5>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">Version: 2.1.0</li>
                                        <li class="list-group-item">Last Updated: Mar 1, 2024</li>
                                        <li class="list-group-item">PHP Version: 8.1.12</li>
                                        <li class="list-group-item">Database: MySQL 8.0</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Server Status</h5>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">CPU Usage: 24%</li>
                                        <li class="list-group-item">Memory Usage: 1.2 GB / 4 GB</li>
                                        <li class="list-group-item">Disk Space: 245 MB / 50 GB</li>
                                        <li class="list-group-item">Uptime: 15 days, 8 hours</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Document Modal -->
    <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocumentModalLabel">Add New Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDocumentForm">
                        <div class="mb-3">
                            <label for="documentTitle" class="form-label">Document Title</label>
                            <input type="text" class="form-control" id="documentTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="documentDepartment" class="form-label">Department</label>
                            <select class="form-select" id="documentDepartment" required>
                                <option value="">Select Department</option>
                                <option value="Finance">Finance</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="IT">IT</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="documentFile" class="form-label">Upload File</label>
                            <input type="file" class="form-control" id="documentFile" required>
                        </div>
                        <div class="mb-3">
                            <label for="documentDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="documentDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addDocument()">Add Document</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDepartmentModalLabel">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDepartmentForm">
                        <div class="mb-3">
                            <label for="departmentName" class="form-label">Department Name *</label>
                            <input type="text" class="form-control" id="departmentName" required>
                        </div>
                        <div class="mb-3">
                            <label for="departmentCode" class="form-label">Department Code *</label>
                            <input type="text" class="form-control" id="departmentCode" required>
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addDepartment()">Add Department</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editDepartmentForm">
                        <input type="hidden" id="editDepartmentId">
                        <div class="mb-3">
                            <label for="editDepartmentName" class="form-label">Department Name *</label>
                            <input type="text" class="form-control" id="editDepartmentName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDepartmentCode" class="form-label">Department Code *</label>
                            <input type="text" class="form-control" id="editDepartmentCode" required>
                        </div>
                     
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateDepartment()">Update Department</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Department Modal -->
    <div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDepartmentModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">Are you sure?</h4>
                        <p>You are about to delete the department: <strong id="deleteDepartmentName"></strong></p>
                        <p class="text-danger">This action cannot be undone. All users in this department will need to be reassigned.</p>
                    </div>
                    <input type="hidden" id="deleteDepartmentId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteDepartment()">Delete Department</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="userFullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="userFullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="userUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="userUsername" required>
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="userDepartment" class="form-label">Department</label>
                            <select class="form-select" id="userDepartment" required>
                                <option value="">Select Department</option>
                                <option value="Finance">Finance</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="IT">IT</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="userRole" class="form-label">Role</label>
                            <select class="form-select" id="userRole" required>
                                <option value="">Select Role</option>
                                <option value="Admin">Admin</option>
                                <option value="Manager">Manager</option>
                                <option value="User">User</option>
                                <option value="Viewer">Viewer</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addUser()">Add User</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Backup Modal -->
    <div class="modal fade" id="addBackupModal" tabindex="-1" aria-labelledby="addBackupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBackupModalLabel">Create Database Backup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBackupForm">
                        <div class="mb-3">
                            <label for="backupName" class="form-label">Backup Name</label>
                            <input type="text" class="form-control" id="backupName" required>
                        </div>
                        <div class="mb-3">
                            <label for="backupDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="backupDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="includeFiles">
                            <label class="form-check-label" for="includeFiles">Include uploaded files in backup</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addBackup()">Create Backup</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Export Audit Modal -->
    <div class="modal fade" id="exportAuditModal" tabindex="-1" aria-labelledby="exportAuditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportAuditModalLabel">Export Audit Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="exportAuditForm">
                        <div class="mb-3">
                            <label for="exportFormat" class="form-label">Export Format</label>
                            <select class="form-select" id="exportFormat" required>
                                <option value="csv">CSV</option>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dateRange" class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange" required>
                                <option value="today">Today</option>
                                <option value="week">Last 7 Days</option>
                                <option value="month">Last 30 Days</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="mb-3" id="customDateRange" style="display: none;">
                            <div class="row">
                                <div class="col">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                                <div class="col">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="exportAuditLogs()">Export Logs</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Custom JS -->
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
            // Section titles mapping
            const sectionTitles = {
                'dashboard': 'DASHBOARD',
                'document-management': 'DOCUMENT MANAGEMENT',
                'department-management': 'DEPARTMENT MANAGEMENT',
                'user-management': 'USER MANAGEMENT',
                'database-management': 'DATABASE MANAGEMENT',
                'audit-trails': 'AUDIT TRAILS',
                'system-information': 'SYSTEM INFORMATION'
            };

            // Handle navigation clicks
            $('.sidebar .nav-link').on('click', function(e) {
                e.preventDefault();
                
                // Update active state
                $('.sidebar .nav-link').removeClass('active');
                $(this).addClass('active');
                
                // Get target section
                const targetSection = $(this).data('section');
                
                // Update page title
                $('.dashboard-title').text(sectionTitles[targetSection] || 'DASHBOARD');
                
                // Show target section, hide others
                $('.section').removeClass('active');
                $(`#${targetSection}`).addClass('active');
                
                // Load departments when department management section is opened
                if (targetSection === 'department-management') {
                    loadDepartments();
                }
            });
            
            // Handle date range selection for audit export
            $('#dateRange').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('#customDateRange').show();
                } else {
                    $('#customDateRange').hide();
                }
            });

            // Load departments on page load if department section is active
            if ($('#department-management').hasClass('active')) {
                loadDepartments();
            }
        });

        // Department Management Functions
        async function loadDepartments() {
            const tableBody = document.getElementById('departmentsTableBody');
            const loadingDiv = document.getElementById('departmentsLoading');
            const tableContainer = document.getElementById('departmentsTableContainer');
            const emptyDiv = document.getElementById('departmentsEmpty');

            // Show loading, hide table and empty state
            loadingDiv.style.display = 'block';
            tableContainer.style.display = 'none';
            emptyDiv.style.display = 'none';
            tableBody.innerHTML = '';

            try {
                const response = await fetch('../../api/readDepartment.php');
                const data = await response.json();

                // Hide loading
                loadingDiv.style.display = 'none';

                if (data.length === 0) {
                    emptyDiv.style.display = 'block';
                    return;
                }

                // Show table
                tableContainer.style.display = 'block';

                data.forEach(dept => {
                    const statusBadge = dept.is_active == 1
                        ? `<span class="badge bg-success">Active</span>`
                        : `<span class="badge bg-secondary">Inactive</span>`;

                    const row = `
                        <tr>
                            <td>${dept.department_code}</td>
                            <td>${dept.department_name}</td>
                            <td>-</td> <!-- Placeholder for Department Admin -->
                            <td>-</td> <!-- Placeholder for Member Count -->
                            <td>${statusBadge}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-warning btn-sm" onclick="editDepartment(${dept.department_id}, '${dept.department_name}', '${dept.department_code}', '${dept.description || ''}', ${dept.is_active})">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteDepartment(${dept.department_id}, '${dept.department_name}')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            } catch (error) {
                console.error('Error loading departments:', error);
                loadingDiv.style.display = 'none';
                emptyDiv.style.display = 'block';
                emptyDiv.innerHTML = `
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    <h4>Error Loading Departments</h4>
                    <p>Failed to load departments. Please try again.</p>
                    <button class="btn btn-primary" onclick="loadDepartments()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Retry
                    </button>
                `;
            }
        }

        // Add Department Function
        async function addDepartment() {
            const name = document.getElementById('departmentName').value.trim();
            const code = document.getElementById('departmentCode').value.trim();
   
            if (!name || !code) {
                alert("Please fill in both Department Name and Code.");
                return;
            }

            try {
                const response = await fetch('http://localhost/DOCMAN/Project-ni-Ruth_Qpal/api/createDepartment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        name, 
                        code, 
                     
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert(result.message, 'success');
                    document.getElementById('addDepartmentForm').reset();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addDepartmentModal'));
                    modal.hide();
                    loadDepartments();
                } else {
                    showAlert("Error: " + result.message, 'error');
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('Failed to connect to the server.', 'error');
            }
        }

        // Edit Department Function
        function editDepartment(id, name, code, description, isActive) {
            document.getElementById('editDepartmentId').value = id;
            document.getElementById('editDepartmentName').value = name;
            document.getElementById('editDepartmentCode').value = code;
            document.getElementById('editDepartmentDescription').value = description;
            document.getElementById('editDepartmentStatus').checked = isActive == 1;
            
            const modal = new bootstrap.Modal(document.getElementById('editDepartmentModal'));
            modal.show();
        }

        // Update Department Function
        async function updateDepartment() {
            const id = document.getElementById('editDepartmentId').value;
            const name = document.getElementById('editDepartmentName').value.trim();
            const code = document.getElementById('editDepartmentCode').value.trim();
            const description = document.getElementById('editDepartmentDescription').value.trim();
            const isActive = document.getElementById('editDepartmentStatus').checked ? 1 : 0;

            if (!name || !code) {
                alert("Please fill in both Department Name and Code.");
                return;
            }

            try {
                const response = await fetch('../../api/updateDepartment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        department_id: id,
                        department_name: name, 
                        department_code: code, 
                        description: description,
                        is_active: isActive
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editDepartmentModal'));
                    modal.hide();
                    loadDepartments();
                } else {
                    showAlert("Error: " + result.error, 'error');
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('Failed to connect to the server.', 'error');
            }
        }
        let selectedDepartmentId = null;
        // Delete Department Function
        function deleteDepartment(id, name) {
              selectedDepartmentId = id;
            document.getElementById('deleteDepartmentId').value = id;
            document.getElementById('deleteDepartmentName').textContent = name;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteDepartmentModal'));
            modal.show();
        }

        // Confirm Delete Department Function
        async function confirmDeleteDepartment() {
            const id = document.getElementById('deleteDepartmentId').value;
            const name = document.getElementById('deleteDepartmentName').textContent;

            try {
                const response = await fetch('../../api/deleteDepartment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ department_id: id })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteDepartmentModal'));
                    modal.hide();
                    loadDepartments();
                } else {
                    showAlert("Error: " + result.error, 'error');
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteDepartmentModal'));
                    modal.hide();
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('Failed to connect to the server.', 'error');
            }
        }

        // Show Alert Function
        function showAlert(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Add alert to the top of the content area
            $('.content-area').prepend(alertHtml);
            
            // Auto remove alert after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        // Other existing functions (for other sections)
        function addDocument() {
            const title = $('#documentTitle').val();
            const department = $('#documentDepartment').val();
            const description = $('#documentDescription').val();
            
            if (!title || !department) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Generate a new document ID
            const docId = 'DOC-' + Math.floor(10000 + Math.random() * 90000);
            
            // Add new row to the table
            const newRow = `
                <tr>
                    <td>${docId}</td>
                    <td>${title}</td>
                    <td>${department}</td>
                    <td>Admin</td>
                    <td>${new Date().toLocaleDateString()}</td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary btn-sm">View</button>
                            <button class="btn btn-warning btn-sm">Edit</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </div>
                    </td>
                </tr>
            `;
            
            $('#documentsTableBody').prepend(newRow);
            
            // Close modal and reset form
            $('#addDocumentModal').modal('hide');
            $('#addDocumentForm')[0].reset();
            
            // Show success message
            showAlert('Document added successfully!', 'success');
        }
        
        function addUser() {
            const fullName = $('#userFullName').val();
            const username = $('#userUsername').val();
            const email = $('#userEmail').val();
            const department = $('#userDepartment').val();
            const role = $('#userRole').val();
            
            if (!fullName || !username || !email || !department || !role) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Generate a new user ID
            const userId = 'USR-' + Math.floor(10000 + Math.random() * 90000);
            
            // Add new row to the table
            const newRow = `
                <tr>
                    <td>${userId}</td>
                    <td>${fullName}</td>
                    <td>${username}</td>
                    <td>${department}</td>
                    <td>${role}</td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary btn-sm">View</button>
                            <button class="btn btn-warning btn-sm">Edit</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </div>
                    </td>
                </tr>
            `;
            
            $('#usersTableBody').prepend(newRow);
            
            // Close modal and reset form
            $('#addUserModal').modal('hide');
            $('#addUserForm')[0].reset();
            
            // Show success message
            showAlert('User added successfully!', 'success');
        }
        
        function addBackup() {
            const name = $('#backupName').val();
            const description = $('#backupDescription').val();
            const includeFiles = $('#includeFiles').is(':checked');
            
            if (!name) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Generate a new backup ID
            const backupId = 'BK-' + Math.floor(10000 + Math.random() * 90000);
            
            // Add new row to the table
            const newRow = `
                <tr>
                    <td>${backupId}</td>
                    <td>${new Date().toLocaleString()}</td>
                    <td>${Math.floor(240 + Math.random() * 20)} MB</td>
                    <td><span class="badge bg-success">Completed</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary btn-sm">Download</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </div>
                    </td>
                </tr>
            `;
            
            $('#backupsTableBody').prepend(newRow);
            
            // Close modal and reset form
            $('#addBackupModal').modal('hide');
            $('#addBackupForm')[0].reset();
            
            // Show success message
            showAlert('Backup created successfully!', 'success');
        }
        
        function exportAuditLogs() {
            const format = $('#exportFormat').val();
            const dateRange = $('#dateRange').val();
            
            // Close modal and reset form
            $('#exportAuditModal').modal('hide');
            $('#exportAuditForm')[0].reset();
            
            // Show success message
            showAlert(`Audit logs exported as ${format.toUpperCase()} file!`, 'success');
        }
    </script>
</body>
</html>