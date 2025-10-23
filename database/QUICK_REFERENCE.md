# Quick Reference Guide - Database Operations

## Common SQL Queries for Document Management System

### Table of Contents
1. [User Management](#user-management)
2. [Document Operations](#document-operations)
3. [Permission Management](#permission-management)
4. [Audit & Reports](#audit--reports)
5. [Department Management](#department-management)

---

## User Management

### Create a New User
```sql
-- Super Admin
INSERT INTO users (username, email, password_hash, first_name, last_name, role_id, department_id)
VALUES ('john.doe', 'john@company.com', '$2y$10$...', 'John', 'Doe', 1, NULL);

-- Department Admin
INSERT INTO users (username, email, password_hash, first_name, last_name, role_id, department_id)
VALUES ('jane.smith', 'jane@company.com', '$2y$10$...', 'Jane', 'Smith', 2, 1);

-- Regular User
INSERT INTO users (username, email, password_hash, first_name, last_name, role_id, department_id)
VALUES ('bob.wilson', 'bob@company.com', '$2y$10$...', 'Bob', 'Wilson', 3, 2);
```

### Get User by Username
```sql
SELECT u.*, r.role_name, d.department_name
FROM users u
LEFT JOIN roles r ON u.role_id = r.role_id
LEFT JOIN departments d ON u.department_id = d.department_id
WHERE u.username = 'john.doe' AND u.is_active = 1;
```

### List All Users by Role
```sql
-- All Super Admins
SELECT u.user_id, u.username, u.email, u.first_name, u.last_name
FROM users u
WHERE u.role_id = 1 AND u.is_active = 1;

-- All Department Admins
SELECT u.user_id, u.username, u.email, u.first_name, u.last_name, d.department_name
FROM users u
LEFT JOIN departments d ON u.department_id = d.department_id
WHERE u.role_id = 2 AND u.is_active = 1;

-- All Regular Users
SELECT u.user_id, u.username, u.email, u.first_name, u.last_name, d.department_name
FROM users u
LEFT JOIN departments d ON u.department_id = d.department_id
WHERE u.role_id = 3 AND u.is_active = 1;
```

### Update User Role
```sql
-- Promote user to department admin
UPDATE users 
SET role_id = 2, department_id = 1
WHERE user_id = 5;

-- Change to regular user
UPDATE users 
SET role_id = 3
WHERE user_id = 5;
```

### Deactivate User (Soft Delete)
```sql
UPDATE users 
SET is_active = 0 
WHERE user_id = 5;
```

### Get Users by Department
```sql
SELECT u.user_id, u.username, u.email, r.role_name
FROM users u
JOIN roles r ON u.role_id = r.role_id
WHERE u.department_id = 1 AND u.is_active = 1
ORDER BY r.role_id, u.last_name;
```

---

## Document Operations

### Upload New Document
```sql
INSERT INTO documents 
(document_title, document_number, description, file_name, file_path, 
 file_size, file_type, category_id, department_id, uploaded_by, status)
VALUES 
('Q4 Financial Report', 'DOC-2024-001', 'Annual financial report', 
 'q4_report.pdf', '/uploads/2024/q4_report.pdf', 
 2048576, 'application/pdf', 2, 3, 5, 'approved');
```

### Get All Documents
```sql
SELECT d.*, 
       u.username as uploaded_by_name,
       dept.department_name,
       cat.category_name
FROM documents d
LEFT JOIN users u ON d.uploaded_by = u.user_id
LEFT JOIN departments dept ON d.department_id = dept.department_id
LEFT JOIN document_categories cat ON d.category_id = cat.category_id
ORDER BY d.created_at DESC;
```

### Get Documents by Department
```sql
SELECT d.document_id, d.document_title, d.document_number, 
       d.status, d.created_at, u.username as uploaded_by
FROM documents d
JOIN users u ON d.uploaded_by = u.user_id
WHERE d.department_id = 1
ORDER BY d.created_at DESC;
```

### Get Documents by Category
```sql
SELECT d.*, c.category_name
FROM documents d
JOIN document_categories c ON d.category_id = c.category_id
WHERE c.category_name = 'Reports'
ORDER BY d.created_at DESC;
```

### Get Documents by Status
```sql
-- Pending documents
SELECT d.*, u.username as uploaded_by
FROM documents d
JOIN users u ON d.uploaded_by = u.user_id
WHERE d.status = 'pending'
ORDER BY d.created_at ASC;

-- Approved documents
SELECT d.*, u.username
FROM documents d
JOIN users u ON d.uploaded_by = u.user_id
WHERE d.status = 'approved'
ORDER BY d.created_at DESC;
```

### Search Documents
```sql
SELECT d.*, u.username as uploaded_by
FROM documents d
JOIN users u ON d.uploaded_by = u.user_id
WHERE d.document_title LIKE '%report%' 
   OR d.description LIKE '%report%'
   OR d.document_number LIKE '%report%'
ORDER BY d.created_at DESC;
```

### Update Document Status
```sql
-- Approve document
UPDATE documents 
SET status = 'approved' 
WHERE document_id = 10;

-- Reject document
UPDATE documents 
SET status = 'rejected' 
WHERE document_id = 10;

-- Archive document
UPDATE documents 
SET status = 'archived' 
WHERE document_id = 10;
```

### Get Document with Version History
```sql
SELECT d.document_id, d.document_title, d.version as current_version,
       dv.version_id, dv.version_number, dv.created_at, 
       u.username as version_uploaded_by
FROM documents d
LEFT JOIN document_versions dv ON d.document_id = dv.document_id
LEFT JOIN users u ON dv.uploaded_by = u.user_id
WHERE d.document_id = 5
ORDER BY dv.created_at DESC;
```

---

## Permission Management

### Grant Document Access to User
```sql
-- Give view and download permissions
INSERT INTO document_permissions 
(document_id, user_id, can_view, can_edit, can_delete, can_download)
VALUES (10, 5, 1, 0, 0, 1);

-- Give full permissions
INSERT INTO document_permissions 
(document_id, user_id, can_view, can_edit, can_delete, can_download)
VALUES (10, 5, 1, 1, 1, 1);
```

### Grant Department-Wide Access
```sql
INSERT INTO document_permissions 
(document_id, department_id, can_view, can_edit, can_delete, can_download)
VALUES (10, 2, 1, 0, 0, 1);
```

### Grant Role-Based Access
```sql
-- All department admins can view and edit
INSERT INTO document_permissions 
(document_id, role_id, can_view, can_edit, can_delete, can_download)
VALUES (10, 2, 1, 1, 0, 1);
```

### Check User's Document Permissions
```sql
SELECT d.document_id, d.document_title,
       MAX(dp.can_view) as can_view,
       MAX(dp.can_edit) as can_edit,
       MAX(dp.can_delete) as can_delete,
       MAX(dp.can_download) as can_download
FROM documents d
LEFT JOIN document_permissions dp ON d.document_id = dp.document_id
WHERE dp.user_id = 5 
   OR dp.department_id = (SELECT department_id FROM users WHERE user_id = 5)
   OR dp.role_id = (SELECT role_id FROM users WHERE user_id = 5)
   OR d.uploaded_by = 5
   OR d.is_public = 1
GROUP BY d.document_id, d.document_title;
```

### Get All Users with Access to Document
```sql
SELECT DISTINCT u.user_id, u.username, u.email,
       dp.can_view, dp.can_edit, dp.can_delete, dp.can_download
FROM users u
JOIN document_permissions dp ON u.user_id = dp.user_id
WHERE dp.document_id = 10;
```

### Revoke Document Access
```sql
DELETE FROM document_permissions 
WHERE document_id = 10 AND user_id = 5;
```

### Update Permissions
```sql
UPDATE document_permissions 
SET can_edit = 1, can_delete = 1 
WHERE document_id = 10 AND user_id = 5;
```

---

## Audit & Reports

### View Recent Activity
```sql
SELECT a.audit_id, a.action_type, a.table_name, 
       u.username, a.created_at, a.ip_address
FROM audit_trails a
LEFT JOIN users u ON a.user_id = u.user_id
ORDER BY a.created_at DESC
LIMIT 50;
```

### User Activity Log
```sql
SELECT a.action_type, a.table_name, a.record_id, 
       a.created_at, a.ip_address
FROM audit_trails a
WHERE a.user_id = 5
ORDER BY a.created_at DESC;
```

### Document Activity History
```sql
SELECT a.action_type, u.username, a.old_value, a.new_value, a.created_at
FROM audit_trails a
LEFT JOIN users u ON a.user_id = u.user_id
WHERE a.table_name = 'documents' AND a.record_id = 10
ORDER BY a.created_at DESC;
```

### Department Document Statistics
```sql
SELECT dept.department_name,
       COUNT(d.document_id) as total_documents,
       SUM(CASE WHEN d.status = 'approved' THEN 1 ELSE 0 END) as approved,
       SUM(CASE WHEN d.status = 'pending' THEN 1 ELSE 0 END) as pending,
       SUM(CASE WHEN d.status = 'rejected' THEN 1 ELSE 0 END) as rejected
FROM departments dept
LEFT JOIN documents d ON dept.department_id = d.department_id
GROUP BY dept.department_id, dept.department_name;
```

### User Upload Statistics
```sql
SELECT u.username, u.email, COUNT(d.document_id) as uploads,
       SUM(d.file_size) as total_size_bytes
FROM users u
LEFT JOIN documents d ON u.user_id = d.uploaded_by
WHERE u.role_id = 3
GROUP BY u.user_id, u.username, u.email
ORDER BY uploads DESC;
```

### Most Active Users (Last 30 Days)
```sql
SELECT u.username, COUNT(a.audit_id) as activity_count
FROM users u
JOIN audit_trails a ON u.user_id = a.user_id
WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY u.user_id, u.username
ORDER BY activity_count DESC
LIMIT 10;
```

### Documents by Category Report
```sql
SELECT c.category_name, COUNT(d.document_id) as document_count
FROM document_categories c
LEFT JOIN documents d ON c.category_id = d.category_id
GROUP BY c.category_id, c.category_name
ORDER BY document_count DESC;
```

---

## Department Management

### Create Department
```sql
INSERT INTO departments (department_name, department_code, description)
VALUES ('Marketing', 'MKT', 'Marketing and Communications Department');
```

### List All Departments
```sql
SELECT d.*,
       COUNT(u.user_id) as user_count,
       COUNT(doc.document_id) as document_count
FROM departments d
LEFT JOIN users u ON d.department_id = u.department_id AND u.is_active = 1
LEFT JOIN documents doc ON d.department_id = doc.department_id
WHERE d.is_active = 1
GROUP BY d.department_id;
```

### Get Department Details
```sql
SELECT d.*,
       (SELECT COUNT(*) FROM users WHERE department_id = d.department_id AND is_active = 1) as user_count,
       (SELECT COUNT(*) FROM documents WHERE department_id = d.department_id) as document_count
FROM departments d
WHERE d.department_id = 1;
```

### Update Department
```sql
UPDATE departments 
SET department_name = 'Human Resources & Administration',
    description = 'Updated description'
WHERE department_id = 1;
```

### Deactivate Department
```sql
UPDATE departments 
SET is_active = 0 
WHERE department_id = 5;
```

---

## Notifications

### Create Notification
```sql
INSERT INTO notifications 
(user_id, title, message, type, related_document_id)
VALUES 
(5, 'Document Approved', 'Your document "Q4 Report" has been approved.', 'approval', 10);
```

### Get User's Unread Notifications
```sql
SELECT n.*, d.document_title
FROM notifications n
LEFT JOIN documents d ON n.related_document_id = d.document_id
WHERE n.user_id = 5 AND n.is_read = 0
ORDER BY n.created_at DESC;
```

### Mark Notification as Read
```sql
UPDATE notifications 
SET is_read = 1, read_at = NOW() 
WHERE notification_id = 15;
```

### Mark All Notifications as Read
```sql
UPDATE notifications 
SET is_read = 1, read_at = NOW() 
WHERE user_id = 5 AND is_read = 0;
```

---

## Session Management

### Create Session
```sql
INSERT INTO sessions (session_id, user_id, ip_address, user_agent)
VALUES ('abc123xyz789', 5, '192.168.1.100', 'Mozilla/5.0...');
```

### Get Active Sessions
```sql
SELECT s.session_id, u.username, s.ip_address, s.last_activity
FROM sessions s
JOIN users u ON s.user_id = u.user_id
WHERE s.last_activity >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
ORDER BY s.last_activity DESC;
```

### Clean Expired Sessions
```sql
DELETE FROM sessions 
WHERE last_activity < DATE_SUB(NOW(), INTERVAL 24 HOUR);
```

### Update Session Activity
```sql
UPDATE sessions 
SET last_activity = NOW() 
WHERE session_id = 'abc123xyz789';
```

---

## Maintenance Queries

### Database Size
```sql
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'system_docman'
ORDER BY (data_length + index_length) DESC;
```

### Archive Old Audit Trails
```sql
-- Create archive table first
CREATE TABLE IF NOT EXISTS audit_trails_archive LIKE audit_trails;

-- Move old records (older than 1 year)
INSERT INTO audit_trails_archive 
SELECT * FROM audit_trails 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Delete archived records
DELETE FROM audit_trails 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

### Optimize All Tables
```sql
OPTIMIZE TABLE users, documents, document_permissions, audit_trails, 
               sessions, notifications, departments, roles, 
               document_categories, document_versions;
```

---

## Security Queries

### Find Inactive Users
```sql
SELECT user_id, username, email, last_login
FROM users
WHERE last_login < DATE_SUB(NOW(), INTERVAL 90 DAY)
   OR last_login IS NULL
ORDER BY last_login ASC;
```

### Get Failed Login Attempts (from audit)
```sql
SELECT a.*, u.username
FROM audit_trails a
LEFT JOIN users u ON a.user_id = u.user_id
WHERE a.action_type = 'LOGIN_FAILED'
  AND a.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
ORDER BY a.created_at DESC;
```

### Find Documents Without Permissions
```sql
SELECT d.document_id, d.document_title, d.is_public
FROM documents d
LEFT JOIN document_permissions dp ON d.document_id = dp.document_id
WHERE dp.permission_id IS NULL AND d.is_public = 0;
```

---

## Quick Tips

1. **Always use prepared statements** in your PHP code to prevent SQL injection
2. **Index frequently searched columns** for better performance
3. **Archive old audit trails** regularly to maintain performance
4. **Use transactions** for operations that modify multiple tables
5. **Regular backups** are essential - automate them!
6. **Monitor slow queries** and optimize as needed
7. **Clean expired sessions** to maintain security

---

## Need Help?

- Check `DATABASE_STRUCTURE.md` for schema details
- Review `README.md` for setup instructions
- Test connection with `test_connection.php`
- View audit trails to debug permission issues
