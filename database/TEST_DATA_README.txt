================================================================================
                    TEST USER DATA - LOADING INSTRUCTIONS
              Document Management System - Test Database
================================================================================

FILE: test_users.sql
PURPOSE: Populate the database with test users for development and testing

================================================================================
                    HOW TO LOAD TEST DATA
================================================================================

METHOD 1: Using MySQL Command Line
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Open Command Prompt or Terminal
2. Navigate to the database folder:
   cd d:\xampp\htdocs\system_docman\database

3. Login to MySQL (using port 3307 based on your config):
   mysql -u root -P 3307 -h localhost

4. Select the database:
   USE system_docman;

5. Load the test data:
   SOURCE test_users.sql;
   
   OR (if not in the directory):
   SOURCE d:/xampp/htdocs/system_docman/database/test_users.sql;

METHOD 2: Using phpMyAdmin
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Select 'system_docman' database
3. Click 'Import' tab
4. Click 'Choose File'
5. Select: test_users.sql
6. Click 'Go'

METHOD 3: Copy and Paste
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Open test_users.sql in a text editor
2. Copy all the SQL content
3. Open phpMyAdmin or MySQL Workbench
4. Select 'system_docman' database
5. Go to SQL tab
6. Paste the content
7. Click 'Execute' or 'Go'

================================================================================
                    TEST DATA SUMMARY
================================================================================

TOTAL USERS: 32

BREAKDOWN:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• Super Admins (role_id = 1): 2 users
• Department Admins (role_id = 2): 5 users (one per department)
• Regular Users (role_id = 3): 25 users (5 per department)

DEPARTMENTS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. College of Computer Studies (CCS)
2. College of Business and Accountancy (CBA)
3. College of Nursing (CN)
4. College of Education (COED)
5. College of Engineering (COE)

================================================================================
                    TEST CREDENTIALS
================================================================================

ALL USERS HAVE THE SAME PASSWORD: Password@123

SUPER ADMIN ACCOUNTS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Username: admin
Email: admin@plpasig.edu.ph
Password: Password@123
Role: Super Administrator
Department: None

Username: johnadmin
Email: john.admin@plpasig.edu.ph
Password: Password@123
Role: Super Administrator
Department: None

DEPARTMENT ADMIN ACCOUNTS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. CCS Admin:
   Username: echo
   Email: riga_jericho@plpasig.edu.ph
   Password: Password@123
   Department: College of Computer Studies

2. CBA Admin:
   Username: cris
   Email: montifolca_cris@plpasig.edu.ph
   Password: Password@123
   Department: College of Business and Accountancy

3. CN Admin:
   Username: faith
   Email: yutuc_kish@plpasig.edu.ph
   Password: Password@123
   Department: College of Nursing

4. COED Admin:
   Username: ruthyy
   Email: domino_ruth@plpasig.edu.ph
   Password: Password@123
   Department: College of Education

5. COE Admin:
   Username: mike_santos
   Email: santos.michael@plpasig.edu.ph
   Password: Password@123
   Department: College of Engineering

SAMPLE REGULAR USER ACCOUNTS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
From CCS:
   Username: maria_garcia
   Email: garcia.maria@plpasig.edu.ph
   Password: Password@123

From CBA:
   Username: angel
   Email: yap_angel@plpasig.edu.ph
   Password: Password@123

From CN:
   Username: ana_martinez
   Email: martinez.ana@plpasig.edu.ph
   Password: Password@123

(See test_users.sql for all 25 regular users)

================================================================================
                    TESTING THE DATA
================================================================================

After loading the data, test using the web interface:

1. TEST SUPER ADMIN LOGIN:
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   URL: http://localhost/system_docman/login.php
   Username: admin
   Password: Password@123
   
   Expected: Redirect to Super Admin Dashboard
   Should see: All users, all departments, system-wide data

2. TEST DEPARTMENT ADMIN LOGIN:
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   URL: http://localhost/system_docman/login.php
   Username: echo
   Password: Password@123
   
   Expected: Redirect to Department Admin Dashboard
   Should see: Only CCS users and CCS data

3. TEST REGULAR USER LOGIN:
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   URL: http://localhost/system_docman/login.php
   Username: maria_garcia
   Password: Password@123
   
   Expected: Redirect to User Dashboard
   Should see: Personal documents and profile

4. RUN TEST SCRIPT:
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   URL: http://localhost/system_docman/database/verify_test_data.php
   
   This will show a report of all loaded users

================================================================================
                    VERIFICATION QUERIES
================================================================================

Run these queries in phpMyAdmin or MySQL to verify the data:

1. Count users by role:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SELECT 
    r.role_name,
    COUNT(u.user_id) as user_count
FROM users u
LEFT JOIN roles r ON u.role_id = r.role_id
GROUP BY r.role_name;

Expected Result:
- super-admin: 2
- department-admin: 5
- user: 25

2. Count users by department:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SELECT 
    d.department_name,
    COUNT(u.user_id) as user_count
FROM users u
LEFT JOIN departments d ON u.department_id = d.department_id
WHERE u.role_id = 3
GROUP BY d.department_name;

Expected Result:
- Each department should have 5 regular users

3. List all users:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SELECT 
    u.user_id,
    CONCAT(u.first_name, ' ', u.last_name) as full_name,
    u.username,
    u.email,
    r.role_name,
    d.department_name,
    u.is_active
FROM users u
LEFT JOIN roles r ON u.role_id = r.role_id
LEFT JOIN departments d ON u.department_id = d.department_id
ORDER BY u.role_id, u.department_id, u.user_id;

4. Test password hash:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SELECT username, password_hash FROM users WHERE username = 'admin';

The password_hash should be:
$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

================================================================================
                    TROUBLESHOOTING
================================================================================

ISSUE: Duplicate entry error
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SOLUTION: Data already exists. Clear existing data first:
   TRUNCATE TABLE users;
   TRUNCATE TABLE departments;
   TRUNCATE TABLE roles;
   
Then reload test_users.sql

ISSUE: Foreign key constraint error
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SOLUTION: Make sure roles and departments exist first.
The SQL file handles this automatically with INSERT...ON DUPLICATE KEY

ISSUE: Cannot login with test credentials
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SOLUTION: 
1. Check if data was loaded: SELECT COUNT(*) FROM users;
2. Verify password hash is correct
3. Make sure is_active = 1
4. Clear browser cache/cookies

ISSUE: Port 3307 connection error
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SOLUTION: Your MySQL is running on port 3307 (not default 3306)
- Use: mysql -u root -P 3307
- Your config/database.php already has port 3307 configured

================================================================================
                    CLEANUP (Remove Test Data)
================================================================================

If you want to remove all test data and start fresh:

TRUNCATE TABLE audit_trails;
TRUNCATE TABLE users;
TRUNCATE TABLE departments;
TRUNCATE TABLE roles;

Then reload schema.sql and test_users.sql

================================================================================
                    NOTES
================================================================================

• All passwords use bcrypt hashing for security
• The hash provided is for: Password@123
• All users are set to is_active = 1 (active)
• Department admins are assigned to their respective departments
• Super admins have no department assignment (NULL)
• Each department has exactly 1 admin and 5 regular users

================================================================================
                    READY TO TEST!
================================================================================

After loading the data, you can:
✓ Login as different roles
✓ Test dashboard functionality
✓ Verify role-based access control
✓ Test department filtering
✓ Create documents (when feature is added)
✓ Test audit trail logging

Total test accounts: 32 users across 3 roles and 5 departments

================================================================================
