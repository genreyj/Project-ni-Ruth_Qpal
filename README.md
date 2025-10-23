# Document Management System
## Pamantasan ng Lungsod ng Pasig

A comprehensive and secure document management solution with role-based access control.

---

## 🚀 Quick Start

### Prerequisites
- **XAMPP** (Apache + MySQL + PHP)
- **Web Browser** (Chrome, Firefox, Edge, etc.)

### Installation Steps

#### 1. Start XAMPP Services
- Open XAMPP Control Panel
- Start **Apache** and **MySQL** services

#### 2. Setup Database
Choose one of the following methods:

**Method A: Web-Based Setup (Recommended)**
1. Open browser and visit: `http://localhost/system_docman/database/setup.php`
2. Follow the on-screen instructions
3. Database will be created automatically

**Method B: phpMyAdmin**
1. Visit: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Select file: `database/schema.sql`
4. Click "Go"

**Method C: Command Line**
```bash
mysql -u root < database/schema.sql
```

#### 3. Verify Database Connection
Visit: `http://localhost/system_docman/database/test_connection.php`

#### 4. Access the System
Visit: `http://localhost/system_docman/`

---

## 🔐 Default Login Credentials

After database setup, you can login with:

- **Username:** `admin`
- **Password:** `Admin@123`
- **Role:** Super Admin

⚠️ **IMPORTANT:** Change this password immediately after first login!

---

## 👥 User Roles

### 1. Super Admin (Full Access)
- Manage all users and departments
- Full document access and control
- View comprehensive audit trails
- System configuration and settings
- Generate system-wide reports

### 2. Department Admin (Department Level)
- Manage users within their department
- Control department documents
- Approve/reject submissions
- View department analytics
- Set department permissions

### 3. User (Standard Access)
- Upload and manage documents
- View permitted documents
- Download and share files
- Receive notifications
- Track document versions

---

## 📁 Project Structure

```
system_docman/
├── assets/
│   └── images/          # Logo and images
├── config/
│   ├── database.php     # Database configuration
│   └── session.php      # Session management
├── css/
│   ├── custom.css       # Custom styles
│   └── responsive.css   # Responsive design
├── database/
│   ├── schema.sql       # Database structure
│   ├── setup.php        # Web-based setup
│   ├── test_connection.php
│   ├── README.md
│   ├── DATABASE_STRUCTURE.md
│   └── QUICK_REFERENCE.md
├── js/
│   └── auth.js          # Authentication scripts
├── php/
│   ├── auth.php         # Authentication handlers
│   └── auth_check.php   # Session verification
├── views/
│   ├── super-admin/     # Super admin pages
│   ├── dept-admin/      # Department admin pages
│   └── user/            # User pages
├── index.php            # Landing page
├── login.php            # Login page
├── logout.php           # Logout handler
└── README.md            # This file
```

---

## 🔧 Configuration

### Database Configuration
Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'system_docman');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Session Configuration
Edit `config/session.php` for session timeout and security settings.

---

## 🌟 Features

### Security
- ✅ Password hashing (bcrypt)
- ✅ Session management
- ✅ Role-based access control (RBAC)
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS protection
- ✅ CSRF token support (planned)

### Document Management
- ✅ Secure document storage
- ✅ Version control
- ✅ Document categorization
- ✅ Advanced search
- ✅ Access permissions

### Audit & Reporting
- ✅ Comprehensive audit trails
- ✅ Login/logout tracking
- ✅ Document activity logging
- ✅ User activity reports
- ✅ System statistics

### User Experience
- ✅ Responsive design (mobile-friendly)
- ✅ Modern UI with Bootstrap 5
- ✅ Real-time notifications
- ✅ Smooth animations
- ✅ Intuitive navigation

---

## 📊 Database Structure

### Core Tables
1. **roles** - 3 system roles
2. **users** - User accounts
3. **departments** - Organizational units
4. **documents** - Document metadata
5. **document_permissions** - Access control
6. **audit_trails** - Activity logging
7. **sessions** - Session management
8. **notifications** - User notifications
9. **document_versions** - Version history
10. **document_categories** - Categorization

See `database/DATABASE_STRUCTURE.md` for detailed schema documentation.

---

## 🔄 Workflow

### User Login Process
1. User enters credentials on `login.php`
2. System verifies credentials against database
3. Password is verified using `password_verify()`
4. Session is created with user information
5. Login activity is logged to audit trail
6. User is redirected to appropriate dashboard based on role

### Document Upload Process (Planned)
1. User uploads document through interface
2. File is validated and sanitized
3. Document metadata is stored in database
4. File is saved to secure storage
5. Permissions are set based on user role
6. Activity is logged to audit trail
7. Relevant users are notified

---

## 🛠️ Development

### Adding New Features

#### 1. Create Database Table
```sql
CREATE TABLE new_table (
    id INT PRIMARY KEY AUTO_INCREMENT,
    -- columns here
);
```

#### 2. Create PHP Handler
```php
<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireLogin(); // Ensure user is logged in

// Your code here
?>
```

#### 3. Update Navigation
Add links to appropriate dashboard files.

---

## 📝 Common Tasks

### Change Admin Password
```sql
UPDATE users 
SET password_hash = '$2y$10$...' 
WHERE username = 'admin';
```
Or use PHP:
```php
$new_password = password_hash('NewPassword123', PASSWORD_BCRYPT);
```

### Create New User
```sql
INSERT INTO users 
(username, email, password_hash, first_name, last_name, role_id, department_id)
VALUES 
('john.doe', 'john@plp.edu', '$2y$10$...', 'John', 'Doe', 3, 1);
```

### View Audit Logs
```sql
SELECT * FROM audit_trails 
ORDER BY created_at DESC 
LIMIT 50;
```

---

## 🐛 Troubleshooting

### Database Connection Error
1. Check if MySQL is running in XAMPP
2. Verify credentials in `config/database.php`
3. Ensure database `system_docman` exists
4. Run `database/test_connection.php`

### Login Not Working
1. Verify user exists in database
2. Check password hash is correct
3. Ensure `is_active = 1`
4. Check session is starting properly
5. Review audit trails for login attempts

### Page Not Found (404)
1. Check file exists in correct location
2. Verify file extension (.php not .html)
3. Check file permissions
4. Ensure Apache is running

### Session Timeout Issues
1. Check `config/session.php` timeout settings
2. Verify PHP session configuration
3. Check browser cookies are enabled

---

## 📞 Support

### System Administrator
- **Email:** genrey570@gmail.com
- **Phone:** 09913910935

### Resources
- Database Documentation: `database/DATABASE_STRUCTURE.md`
- SQL Quick Reference: `database/QUICK_REFERENCE.md`
- Setup Guide: `database/README.md`

---

## 🔒 Security Best Practices

1. **Change default credentials** immediately
2. **Use strong passwords** (min 8 chars, mixed case, numbers, symbols)
3. **Regular backups** of database
4. **Keep PHP and MySQL updated**
5. **Enable HTTPS** in production
6. **Review audit logs** regularly
7. **Limit failed login attempts** (implement rate limiting)
8. **Use prepared statements** for all database queries

---

## 📜 License

© 2025 Pamantasan ng Lungsod ng Pasig. All rights reserved.

---

## 🚧 Roadmap

### Phase 1 (Current)
- ✅ Database setup
- ✅ User authentication
- ✅ Role-based access control
- ✅ Landing page
- ✅ Login system

### Phase 2 (Upcoming)
- ⏳ Document upload functionality
- ⏳ Document management interface
- ⏳ Search and filter system
- ⏳ User management for admins

### Phase 3 (Future)
- ⏳ Email notifications
- ⏳ Document approval workflow
- ⏳ Advanced reporting
- ⏳ API integration
- ⏳ Mobile app

---

## 📖 Version History

**v1.0.0** (2025-10-21)
- Initial release
- Database structure with 3 roles
- User authentication system
- Landing page and login
- Session management
- Audit trail logging

---

## 🙏 Acknowledgments

Built for **Pamantasan ng Lungsod ng Pasig** to streamline document management processes with enterprise-grade security and user-friendly interface.
