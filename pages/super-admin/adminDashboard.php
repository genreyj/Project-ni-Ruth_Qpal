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
    <link rel="stylesheet" href="../../assets/css/superAdmin/adminDashboard.css">

    <style>
      .content-area .section { margin-top: 0; padding-top: 0; }
      .content-area .section + .section { margin-top: 2rem; }
      .content-area .content-box { padding: 1.5rem; }
      .section-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
      .section-header h2 { margin: 0; font-size: 1.3rem; }
      .section-description { margin: 0 0 1rem; color: #6c757d; }
      #docTabs { margin-top: .25rem; }
    </style>
</head>
<body>
    <!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <button class="menu-toggle" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>
    <div class="logo-section">
        <div class="logo-circle">
            <img
                src="../../assets/images/1x1.png"
                alt="Admin Profile"
                class="profile-pic"
                onerror="this.style.display='none'"
            >
        </div>
        <div class="org-title">ADMIN SYSTEM</div>
        <div class="org-subtitle">Administrator</div>
    </div>
    
    <ul class="nav-menu">
        <li class="nav-item">
            <a href="#dashboard" class="nav-link active" data-section="dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="menu-section-title">ADMIN TOOLS</li>
        
        <li class="nav-item">
            <a href="#document-management" class="nav-link" data-section="document-management">
                <i class="bi bi-file-earmark-text"></i>
                <span>Document Management</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="#department-management" class="nav-link" data-section="department-management">
                <i class="bi bi-building"></i>
                <span>Department Management</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="#user-management" class="nav-link" data-section="user-management">
                <i class="bi bi-person-gear"></i>
                <span>User Management</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="#database-management" class="nav-link" data-section="database-management">
                <i class="bi bi-database"></i>
                <span>Database Management</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="#audit-trails" class="nav-link" data-section="audit-trails">
                <i class="bi bi-clock-history"></i>
                <span>Audit Trails</span>
            </a>
        </li>
        
        <li class="menu-section-title">SETTING</li>
        
        <li class="nav-item">
            <a href="#system-information" class="nav-link" data-section="system-information">
                <i class="bi bi-info-circle"></i>
                <span>System Information</span>
            </a>
        </li>
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
            <i class="bi bi-bell"></i>
            <span class="notification-badge">3</span>
        </div>
        <div class="settings-icon">
            <i class="bi bi-question-circle"></i>
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
                            <!-- was: 42 -->
                            <h3 class="stat-value" data-stat="users-total">0</h3>
                            <p class="stat-label">Total Users</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card success">
                            <div class="stat-icon success">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <!-- was: 38 -->
                            <h3 class="stat-value" data-stat="users-active">0</h3>
                            <p class="stat-label">Active Users</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <div class="stat-icon warning">
                                <i class="bi bi-building"></i>
                            </div>
                            <!-- was: 7 -->
                            <h3 class="stat-value" data-stat="departments">0</h3>
                            <p class="stat-label">Departments</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card info">
                            <div class="stat-icon info">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <!-- was: 156 -->
                            <h3 class="stat-value" data-stat="documents">0</h3>
                            <p class="stat-label">Documents</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="data-table">
                    <div class="table-header">
                        <h2 class="table-title"><i class="bi bi-clock-history me-2"></i>Recent Activities</h2>
                        <div class="d-flex gap-2">
                            <a href="#audit-trails" class="btn btn-primary btn-sm nav-jump" data-target-section="audit-trails">View All</a>
                            <!-- Removed Reports quick-jump button -->
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Table</th>
                                    <th>Date/Time</th>
                                </tr>
                            </thead>
                            <tbody id="recentActivitiesBody">
                                <!-- populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Reports anchor -->
                <div id="dashboard-reports"></div>
                <div class="content-box mt-4">
                    <div class="section-header">
                        <h2><i class="bi bi-clipboard-data me-2"></i>Reports</h2>
                    </div>
                    <p class="section-description">Filter and export consolidated document activity across departments.</p>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="filterStartDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" id="filterEndDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select class="form-select" id="filterDepartment">
                                <option value="">All</option>
                                <option value="Finance">Finance</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="IT">IT</option>
                                <option value="Clinic">Clinic</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="filterCategory">
                                <option value="">All</option>
                                <option value="announcement">Announcement</option>
                                <option value="memo">Memo</option>
                                <option value="policy">Policy</option>
                                <option value="invoice">Invoice</option>
                                <option value="report">Report</option>
                                <option value="uncategorized">Uncategorized</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2 mt-2">
                            <button class="btn btn-primary" id="applyReportFilters"><i class="bi bi-funnel me-1"></i>Apply Filters</button>
                            <button class="btn btn-outline-secondary" id="resetReportFilters"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</button>
                            <div class="ms-auto d-flex gap-2">
                                <button class="btn btn-success" id="exportReportCsv"><i class="bi bi-filetype-csv me-1"></i>Export CSV</button>
                                <button class="btn btn-warning" id="exportReportExcel"><i class="bi bi-file-earmark-excel me-1"></i>Export Excel</button>
                                <button class="btn btn-danger" id="exportReportPdf"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-hover table-striped" id="reportTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Document ID</th>
                                    <th>Title</th>
                                    <th>Department</th>
                                    <th>Category</th>
                                    <th>Uploader</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <!-- populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Document Management Section -->
            <div class="section" id="document-management">
                <div class="content-box">
                    <div class="section-header">
                        <h2><i class="bi bi-file-earmark-text me-2"></i>Document Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Document
                        </button>
                    </div>
                    <p class="section-description">Manage all documents in the system, including upload, categorization, and access control.</p>

                    <!-- Controls: search, filter, sort -->
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="docSearch" placeholder="ID, Title, Uploader...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Department</label>
                            <select class="form-select" id="docFilterDepartment">
                                <option value="">All</option>
                                <option value="Finance">Finance</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="IT">IT</option>
                                <option value="Clinic">Clinic</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="docFilterCategory">
                                <option value="">All</option>
                                <option value="announcement">Announcement</option>
                                <option value="memo">Memo</option>
                                <option value="policy">Policy</option>
                                <option value="invoice">Invoice</option>
                                <option value="report">Report</option>
                                <option value="uncategorized">Uncategorized</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="docFilterStatus">
                                <option value="">All</option>
                                <option value="Active">Active</option>
                                <option value="Published">Published</option>
                                <option value="Filed">Filed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sort</label>
                            <select class="form-select" id="docSortBy">
                                <option value="recent">Recent</option>
                                <option value="oldest">Oldest</option>
                                <option value="title">Title A-Z</option>
                            </select>
                        </div>
                    </div>

                    <!-- Downloadable Templates -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-arrow-down me-2"></i>Downloadable Templates</h5>
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                                    <i class="bi bi-plus-circle me-1"></i>Add Template
                                </button>
                            </div>
                            <div class="mt-2" id="templatesList">
                                <!-- populated by JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Tabs: Files / Archive / Trash -->
                    <ul class="nav nav-pills mb-3" id="docTabs">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" id="docTabFiles" data-view="files">
                                <i class="bi bi-folder2-open me-1"></i>Files <span class="badge bg-secondary ms-1" id="countFiles">0</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" id="docTabArchive" data-view="archive">
                                <i class="bi bi-archive me-1"></i>Archive <span class="badge bg-secondary ms-1" id="countArchive">0</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" id="docTabTrash" data-view="trash">
                                <i class="bi bi-trash3 me-1"></i>Trash <span class="badge bg-secondary ms-1" id="countTrash">0</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Documents table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="documentsManageTable">
                            <thead>
                                <tr>
                                    <th>Document ID</th>
                                    <th>Title</th>
                                    <th>Department</th>
                                    <th>Category</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th style="width: 220px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="documentsManageTableBody">
                                <!-- populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Department Management Section -->
            <div class="section" id="department-management">
                <div class="content-box">
                    <div class="section-header">
                        <h2><i class="bi bi-building me-2"></i>Department Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Department
                        </button>
                    </div>
                    <p class="section-description">Manage organizational departments, assign managers, and configure department-specific settings.</p>
                    
                        <div class="table-responsive mt-4" id="departmentsTableContainer">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Department ID</th>
                                        <th>Department Name</th>
                                        <th>Department Admin</th>
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
                    
                </div>
            </div>
            
            <!-- User Management Section -->
            <div class="section" id="user-management">
                <div class="content-box">
                    <div class="section-header">
                        <h2><i class="bi bi-person-gear me-2"></i>User Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-plus-circle me-1"></i> Add User
                        </button>
                    </div>
                    <p class="section-description">Manage system users, assign roles, and control access permissions.</p>
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <tr>
                                    <td>1</td>
                                    <td>Admin</td>
                                    <td>System</td>
                                    <td>admin</td>
                                    <td>admin@plpasig.edu.ph</td>
                                    <td><span class="masked-password">••••••••</span></td>
                                    <td>super admin</td>
                                    <td>null</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-warning btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Jericho</td>
                                    <td>Riga</td>
                                    <td>echo</td>
                                    <td>riga.jericho@plpasig.edu.ph</td>
                                    <td><span class="masked-password">••••••••</span></td>
                                    <td>department admin</td>
                                    <td>College of Computer Studies</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <div class="action-buttons">
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
                    <div class="section-header">
                        <h2><i class="bi bi-database me-2"></i>Database Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBackupModal">
                            <i class="bi bi-plus-circle me-1"></i> Create Backup
                        </button>
                    </div>
                    <p class="section-description">Manage database backups, perform maintenance, and monitor performance.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card card-hover">
                                <div class="card-body">
                                    <h5 class="card-title">Backup Status</h5>
                                    <p class="card-text">Last backup: Mar 15, 2024 08:30 AM</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBackupModal">Create Backup</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-hover">
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
                    <div class="section-header">
                        <h2><i class="bi bi-clock-history me-2"></i>Audit Trails</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportAuditModal">
                            <i class="bi bi-download me-1"></i> Export Logs
                        </button>
                    </div>
                    <p class="section-description">View system activity logs and track user actions for security and compliance.</p>
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Table</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mar 15, 2024 09:23 AM</td>
                                    <td>setting</td>
                                    <td><span class="badge bg-success">LOGIN</span></td>
                                    <td>Change profile</td>
                                    <td>User</td>
                                </tr>
                                <tr>
                                    <td>Mar 15, 2024 09:15 AM</td>
                                    <td>Document Management</td>
                                    <td><span class="badge bg-primary">CREATE</span></td>
                                    <td>Created new document: HR Policy Update</td>
                                    <td>Department Admin</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Remove standalone Reports Section to avoid duplicate IDs -->
            <!-- [deleted old <div class="section" id="reports"> ... </div>] -->
        </div>
    </div>
    
<!-- Add Document Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDocumentModalLabel">Add Documents</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="ocrUploadForm" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="documentFile" class="form-label">Upload File(s)</label>
            <input
              type="file"
              class="form-control"
              id="documentFile"
              name="documentFile[]"
              accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx"
              multiple
              required
            >
          </div>

          <!-- Progress & Preview -->
          <div id="progressContainer" class="mt-3">
            <progress id="progressBar" value="0" max="100" style="width:100%;"></progress>
            <p class="small text-muted mt-1" id="progressText">Awaiting upload...</p>
          </div>
          <div id="previewContainer" class="mt-3"></div>
          <hr>
          <div class="mb-3">
            <h6 class="mb-2">Analysis Results</h6>
            <div id="resultsContainer"></div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="analyzeBtn">Analyze</button>
      </div>
    </div>
  </div>
</div>

    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../assets/js/superAdmin/adminDashboard.js"></script>
</body>
</html>