# Database Setup Instructions

## Overview
This document management system uses MySQL database with 3 user roles:
- **super-admin**: Full system access with all administrative privileges
- **department-admin**: Department-level administrative privileges
- **user**: Standard user with basic document access

## Setup Instructions

### Method 1: Using phpMyAdmin (Recommended for XAMPP)

1. Start XAMPP Control Panel
2. Start Apache and MySQL services
3. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
4. Click on "Import" tab
5. Choose the file: `database/schema.sql`
6. Click "Go" to execute the SQL script

### Method 2: Using MySQL Command Line

1. Open Command Prompt or Terminal
2. Navigate to the project directory:
   ```bash
   cd d:\xampp\htdocs\system_docman
   ```
3. Login to MySQL:
   ```bash
   mysql -u root -p
   ```
4. Execute the SQL file:
   ```bash
   source database/schema.sql
   ```

### Method 3: Using XAMPP Shell

1. Open XAMPP Shell
2. Run the following command:
   ```bash
   mysql -u root < d:\xampp\htdocs\system_docman\database\schema.sql
   ```

## Database Structure

### Core Tables

#### 1. roles
- Stores the 3 system roles
- Fields: role_id, role_name, role_description

#### 2. departments
- Manages different departments
- Fields: department_id, department_name, department_code, description

#### 3. users
- Stores user information and credentials
- Links users to roles and departments
- Fields: user_id, username, email, password_hash, role_id, department_id

#### 4. documents
- Main document storage table
- Fields: document_id, document_title, file_path, status, uploaded_by

#### 5. document_permissions
- Manages granular access control
- Fields: permission_id, document_id, user_id, can_view, can_edit, can_delete

#### 6. audit_trails
- Tracks all system activities
- Fields: audit_id, user_id, action_type, old_value, new_value

#### 7. sessions
- Manages user sessions
- Fields: session_id, user_id, ip_address, last_activity

#### 8. notifications
- User notification system
- Fields: notification_id, user_id, message, is_read

#### 9. document_versions
- Tracks document version history
- Fields: version_id, document_id, version_number, change_notes

#### 10. document_categories
- Document categorization
- Fields: category_id, category_name, description

## Default Credentials

After setup, you can login with:
- **Username**: admin
- **Email**: admin@system.com
- **Password**: Admin@123
- **Role**: super-admin

**⚠️ IMPORTANT**: Change the default password immediately after first login!

## Sample Data

The schema includes sample data for:
- 4 departments (HR, IT, Finance, Operations)
- 5 document categories (Policies, Reports, Forms, Contracts, Memos)
- 1 super admin user

## Database Configuration

Update the database connection settings in `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'system_docman');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## Verification

To verify the database was created successfully:

1. Login to MySQL:
   ```sql
   mysql -u root -p
   ```

2. Check the database:
   ```sql
   USE system_docman;
   SHOW TABLES;
   ```

3. Verify the 3 roles:
   ```sql
   SELECT * FROM roles;
   ```

You should see:
- super-admin (role_id: 1)
- department-admin (role_id: 2)
- user (role_id: 3)

## Role Permissions Overview

### Super Admin
- Full system access
- Manage all users, departments, and documents
- View audit trails
- System configuration

### Department Admin
- Manage users within their department
- Manage documents for their department
- View department-specific reports

### User
- Upload documents
- View/download permitted documents
- Manage their own documents
- Receive notifications

## Next Steps

1. Import the database using one of the methods above
2. Configure the database connection in `config/database.php`
3. Test the connection by logging in with default credentials
4. Change the default admin password
5. Start creating departments and users as needed

## Troubleshooting

### Connection Issues
- Ensure MySQL service is running in XAMPP
- Verify database credentials in `config/database.php`
- Check if port 3306 is not blocked

### Import Errors
- Make sure no database named `system_docman` exists before importing
- Check MySQL version compatibility (requires MySQL 5.7+)

### Permission Issues
- Grant proper privileges to the database user
- For development, using 'root' with no password is common in XAMPP
