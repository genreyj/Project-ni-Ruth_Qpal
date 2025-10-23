# Database Structure Documentation

## Document Management System - Database Schema

### Database Name: `system_docman`

---

## Entity Relationship Overview

```
┌─────────────┐
│   roles     │
│  (3 roles)  │
└──────┬──────┘
       │
       │ role_id
       │
       ├──────────────────┐
       │                  │
┌──────▼──────────┐   ┌───▼──────────────┐
│  users          │   │ document_        │
│                 │   │ permissions      │
│ - super-admin   │   └──────────────────┘
│ - dept-admin    │
│ - user          │
└──────┬──────────┘
       │
       │ uploaded_by / user_id
       │
       ├──────────────────┬──────────────────┐
       │                  │                  │
┌──────▼──────────┐  ┌───▼──────────┐  ┌───▼──────────┐
│  documents      │  │  sessions    │  │  audit_      │
│                 │  │              │  │  trails      │
└──────┬──────────┘  └──────────────┘  └──────────────┘
       │
       ├──────────────────┬──────────────────┐
       │                  │                  │
┌──────▼──────────┐  ┌───▼──────────┐  ┌───▼──────────┐
│  document_      │  │  document_   │  │notifications │
│  versions       │  │  permissions │  │              │
└─────────────────┘  └──────────────┘  └──────────────┘

┌─────────────────┐       ┌──────────────────┐
│  departments    │       │  document_       │
│                 │       │  categories      │
└─────────────────┘       └──────────────────┘
```

---

## Core Tables

### 1. **roles** (Role Management)
Primary table defining the 3 system roles.

| Column           | Type         | Description                           |
|------------------|--------------|---------------------------------------|
| `role_id`        | INT (PK)     | Unique identifier                     |
| `role_name`      | VARCHAR(50)  | super-admin, department-admin, user   |
| `role_description` | TEXT       | Role description                      |
| `created_at`     | TIMESTAMP    | Creation timestamp                    |
| `updated_at`     | TIMESTAMP    | Last update timestamp                 |

**Data:**
- `1` - super-admin
- `2` - department-admin
- `3` - user

---

### 2. **users** (User Management)
Stores all system users with their credentials and role assignments.

| Column           | Type         | Description                           |
|------------------|--------------|---------------------------------------|
| `user_id`        | INT (PK)     | Unique identifier                     |
| `username`       | VARCHAR(50)  | Unique username                       |
| `email`          | VARCHAR(100) | Unique email address                  |
| `password_hash`  | VARCHAR(255) | Hashed password (bcrypt)              |
| `first_name`     | VARCHAR(50)  | First name                            |
| `last_name`      | VARCHAR(50)  | Last name                             |
| `role_id`        | INT (FK)     | References roles.role_id              |
| `department_id`  | INT (FK)     | References departments.department_id  |
| `is_active`      | TINYINT(1)   | Account status (1=active, 0=inactive) |
| `last_login`     | TIMESTAMP    | Last login timestamp                  |
| `created_at`     | TIMESTAMP    | Creation timestamp                    |
| `updated_at`     | TIMESTAMP    | Last update timestamp                 |

**Relationships:**
- Many-to-One with `roles`
- Many-to-One with `departments`
- One-to-Many with `documents`, `sessions`, `audit_trails`, `notifications`

---

### 3. **departments** (Department Management)
Organizational units within the system.

| Column           | Type         | Description                           |
|------------------|--------------|---------------------------------------|
| `department_id`  | INT (PK)     | Unique identifier                     |
| `department_name`| VARCHAR(100) | Department name                       |
| `department_code`| VARCHAR(20)  | Unique department code                |
| `description`    | TEXT         | Department description                |
| `is_active`      | TINYINT(1)   | Status (1=active, 0=inactive)         |
| `created_at`     | TIMESTAMP    | Creation timestamp                    |
| `updated_at`     | TIMESTAMP    | Last update timestamp                 |

**Sample Data:**
- HR (Human Resources)
- IT (Information Technology)
- FIN (Finance)
- OPS (Operations)

---

### 4. **documents** (Document Storage)
Main table for storing document metadata.

| Column           | Type         | Description                           |
|------------------|--------------|---------------------------------------|
| `document_id`    | INT (PK)     | Unique identifier                     |
| `document_title` | VARCHAR(255) | Document title                        |
| `document_number`| VARCHAR(100) | Unique document reference number      |
| `description`    | TEXT         | Document description                  |
| `file_name`      | VARCHAR(255) | Original file name                    |
| `file_path`      | VARCHAR(500) | Storage path                          |
| `file_size`      | BIGINT       | File size in bytes                    |
| `file_type`      | VARCHAR(50)  | MIME type or extension                |
| `category_id`    | INT (FK)     | References document_categories        |
| `department_id`  | INT (FK)     | References departments                |
| `uploaded_by`    | INT (FK)     | References users.user_id              |
| `status`         | ENUM         | draft, pending, approved, rejected, archived |
| `version`        | VARCHAR(20)  | Current version number                |
| `is_public`      | TINYINT(1)   | Public access flag                    |
| `created_at`     | TIMESTAMP    | Upload timestamp                      |
| `updated_at`     | TIMESTAMP    | Last update timestamp                 |

---

### 5. **document_permissions** (Access Control)
Granular permission management for documents.

| Column           | Type         | Description                           |
|------------------|--------------|---------------------------------------|
| `permission_id`  | INT (PK)     | Unique identifier                     |
| `document_id`    | INT (FK)     | References documents.document_id      |
| `user_id`        | INT (FK)     | References users.user_id (optional)   |
| `department_id`  | INT (FK)     | Department access (optional)          |
| `role_id`        | INT (FK)     | Role-based access (optional)          |
| `can_view`       | TINYINT(1)   | View permission                       |
| `can_edit`       | TINYINT(1)   | Edit permission                       |
| `can_delete`     | TINYINT(1)   | Delete permission                     |
| `can_download`   | TINYINT(1)   | Download permission                   |
| `created_at`     | TIMESTAMP    | Creation timestamp                    |
| `updated_at`     | TIMESTAMP    | Last update timestamp                 |

**Permission Levels:**
- User-specific (user_id set)
- Department-wide (department_id set)
- Role-based (role_id set)

---

### 6. **audit_trails** (Activity Logging)
Comprehensive logging of all system activities.

| Column           | Type         | Description                           |
|------------------|--------------|---------------------------------------|
| `audit_id`       | INT (PK)     | Unique identifier                     |
| `user_id`        | INT (FK)     | References users.user_id              |
| `action_type`    | VARCHAR(50)  | Type of action (CREATE, UPDATE, DELETE, etc.) |
| `table_name`     | VARCHAR(50)  | Affected table                        |
| `record_id`      | INT          | Affected record ID                    |
| `old_value`      | TEXT         | Previous value (JSON)                 |
| `new_value`      | TEXT         | New value (JSON)                      |
| `ip_address`     | VARCHAR(45)  | User's IP address                     |
| `user_agent`     | TEXT         | Browser/client info                   |
| `created_at`     | TIMESTAMP    | Action timestamp                      |

---

### 7. **sessions** (Session Management)
Active user sessions for security tracking.

| Column           | Type         | Description                           |
|------------------|--------------|---------------------------------------|
| `session_id`     | VARCHAR(128) | Session identifier (PK)               |
| `user_id`        | INT (FK)     | References users.user_id              |
| `ip_address`     | VARCHAR(45)  | User's IP address                     |
| `user_agent`     | TEXT         | Browser/client info                   |
| `last_activity`  | TIMESTAMP    | Last activity timestamp               |
| `created_at`     | TIMESTAMP    | Session start timestamp               |

---

### 8. **notifications** (Notification System)
User notification management.

| Column              | Type         | Description                           |
|---------------------|--------------|---------------------------------------|
| `notification_id`   | INT (PK)     | Unique identifier                     |
| `user_id`           | INT (FK)     | References users.user_id              |
| `title`             | VARCHAR(255) | Notification title                    |
| `message`           | TEXT         | Notification message                  |
| `type`              | VARCHAR(50)  | Notification type                     |
| `is_read`           | TINYINT(1)   | Read status                           |
| `related_document_id` | INT (FK)   | References documents.document_id      |
| `created_at`        | TIMESTAMP    | Creation timestamp                    |
| `read_at`           | TIMESTAMP    | Read timestamp                        |

---

### 9. **document_versions** (Version Control)
Document version history tracking.

| Column           | Type         | Description                           |
|------------------|--------------|---------------------------------------|
| `version_id`     | INT (PK)     | Unique identifier                     |
| `document_id`    | INT (FK)     | References documents.document_id      |
| `version_number` | VARCHAR(20)  | Version number (e.g., "1.0", "2.3")   |
| `file_name`      | VARCHAR(255) | Version file name                     |
| `file_path`      | VARCHAR(500) | Storage path                          |
| `file_size`      | BIGINT       | File size in bytes                    |
| `uploaded_by`    | INT (FK)     | References users.user_id              |
| `change_notes`   | TEXT         | Version change description            |
| `created_at`     | TIMESTAMP    | Upload timestamp                      |

---

### 10. **document_categories** (Categorization)
Document classification system.

| Column           | Type         | Description                           |
|------------------|--------------|---------------------------------------|
| `category_id`    | INT (PK)     | Unique identifier                     |
| `category_name`  | VARCHAR(100) | Category name                         |
| `description`    | TEXT         | Category description                  |
| `is_active`      | TINYINT(1)   | Status                                |
| `created_at`     | TIMESTAMP    | Creation timestamp                    |
| `updated_at`     | TIMESTAMP    | Last update timestamp                 |

**Sample Categories:**
- Policies
- Reports
- Forms
- Contracts
- Memos

---

## Role-Based Access Control

### Super Admin (role_id: 1)
**Full System Access:**
- ✅ Manage all users (create, update, delete)
- ✅ Manage all departments
- ✅ Manage all documents (all operations)
- ✅ View and manage audit trails
- ✅ System configuration
- ✅ Assign roles to users
- ✅ Access all reports

### Department Admin (role_id: 2)
**Department-Level Access:**
- ✅ Manage users within their department
- ✅ Manage documents for their department
- ✅ View department audit trails
- ✅ Generate department reports
- ⛔ Cannot modify system settings
- ⛔ Cannot access other departments' data

### User (role_id: 3)
**Basic Access:**
- ✅ Upload documents
- ✅ View/download permitted documents
- ✅ Edit own documents (if status allows)
- ✅ View own notifications
- ⛔ Cannot manage other users
- ⛔ Cannot modify system/department settings
- ⛔ Limited document access based on permissions

---

## Indexes for Performance

The schema includes strategic indexes on:
- All foreign keys
- Frequently searched columns (username, email, document_number)
- Status and date columns for filtering
- Permission lookup fields

---

## Security Features

1. **Password Hashing:** Bcrypt hashing for all passwords
2. **Session Tracking:** IP and user agent logging
3. **Audit Trails:** Complete activity logging
4. **Role-Based Access:** Granular permission system
5. **Active Status:** Soft delete capability with is_active flags

---

## Data Integrity

- **Foreign Key Constraints:** Maintain referential integrity
- **Unique Constraints:** Prevent duplicate entries
- **Cascade Rules:** Proper deletion handling
- **Timestamps:** Automatic tracking of creation and updates

---

## Default Data

### Default Super Admin
- **Username:** admin
- **Email:** admin@system.com
- **Password:** Admin@123
- **Role:** super-admin (role_id: 1)

⚠️ **Change the default password immediately after setup!**

---

## Usage Examples

### Check User Role
```sql
SELECT u.username, r.role_name 
FROM users u 
JOIN roles r ON u.role_id = r.role_id 
WHERE u.user_id = 1;
```

### Get User's Department Documents
```sql
SELECT d.* 
FROM documents d 
JOIN users u ON d.department_id = u.department_id 
WHERE u.user_id = 1;
```

### Find Documents User Can Access
```sql
SELECT d.* 
FROM documents d 
LEFT JOIN document_permissions dp ON d.document_id = dp.document_id 
WHERE dp.user_id = 1 AND dp.can_view = 1 
   OR d.uploaded_by = 1 
   OR d.is_public = 1;
```

### Audit Trail for Specific Document
```sql
SELECT a.*, u.username 
FROM audit_trails a 
JOIN users u ON a.user_id = u.user_id 
WHERE a.table_name = 'documents' AND a.record_id = 1 
ORDER BY a.created_at DESC;
```

---

## Maintenance Recommendations

1. **Regular Backups:** Daily automated backups recommended
2. **Audit Trail Archiving:** Archive old audit logs (>1 year)
3. **Session Cleanup:** Remove expired sessions regularly
4. **Index Optimization:** Monitor and optimize indexes quarterly
5. **Security Audits:** Regular review of permissions and access logs

---

## Version History

- **v1.0** (2025-10-21): Initial schema with 3 roles, 10 tables, complete RBAC system
