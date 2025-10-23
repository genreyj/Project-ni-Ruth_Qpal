-- ============================================
-- TEST USER DATA for Document Management System
-- Pamantasan ng Lungsod ng Pasig
-- ============================================
-- Password for all users: Password@123
-- Bcrypt hash: $2y$10$YourHashHere
-- ============================================

-- First, ensure roles exist
INSERT INTO roles (role_id, role_name, role_description) VALUES
(1, 'super-admin', 'System Administrator with full access'),
(2, 'department-admin', 'Department Administrator with department-level access'),
(3, 'user', 'Regular user with basic access')
ON DUPLICATE KEY UPDATE role_name=role_name;

-- Insert Departments (if not exist)
INSERT INTO departments (department_id, department_name, department_code, is_active) VALUES
(1, 'College of Computer Studies', 'CCS', 1),
(2, 'College of Business and Accountancy', 'CBA', 1),
(3, 'College of Nursing', 'CN', 1),
(4, 'College of Education', 'COED', 1),
(5, 'College of Engineering', 'COE', 1)
ON DUPLICATE KEY UPDATE department_name=department_name;

-- ============================================
-- SUPER ADMIN (role_id = 1)
-- ============================================
INSERT INTO users (first_name, last_name, username, email, password_hash, role_id, department_id, is_active) VALUES
('Admin', 'System', 'admin', 'admin@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, 1),
('John', 'Administrator', 'johnadmin', 'john.admin@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, 1);

-- ============================================
-- DEPARTMENT ADMINS (role_id = 2)
-- ============================================
INSERT INTO users (first_name, last_name, username, email, password_hash, role_id, department_id, is_active) VALUES
-- College of Computer Studies Admin
('Jericho', 'Riga', 'echo', 'riga_jericho@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, 1),

-- College of Business and Accountancy Admin
('Cristiana May', 'Montifolca', 'cris', 'montifolca_cris@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 2, 1),

-- College of Nursing Admin
('Kishiel Faith', 'Yutuc', 'faith', 'yutuc_kish@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 3, 1),

-- College of Education Admin
('Ruth', 'Domino', 'ruthyy', 'domino_ruth@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 4, 1),

-- College of Engineering Admin
('Michael', 'Santos', 'mike_santos', 'santos.michael@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 5, 1);

-- ============================================
-- REGULAR USERS (role_id = 3)
-- ============================================

-- Users in College of Computer Studies (department_id = 1)
INSERT INTO users (first_name, last_name, username, email, password_hash, role_id, department_id, is_active) VALUES
('Maria', 'Garcia', 'maria_garcia', 'garcia.maria@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, 1),
('Juan', 'Dela Cruz', 'juan_dc', 'delacruz.juan@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, 1),
('Sofia', 'Reyes', 'sofia_reyes', 'reyes.sofia@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, 1),
('Daniel', 'Bautista', 'dan_bautista', 'bautista.daniel@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, 1),
('Isabella', 'Torres', 'bella_torres', 'torres.isabella@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, 1);

-- Users in College of Business and Accountancy (department_id = 2)
INSERT INTO users (first_name, last_name, username, email, password_hash, role_id, department_id, is_active) VALUES
('Angel', 'Yap', 'angel', 'yap_angel@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 2, 1),
('Carlos', 'Mendoza', 'carlos_m', 'mendoza.carlos@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 2, 1),
('Patricia', 'Santos', 'pat_santos', 'santos.patricia@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 2, 1),
('Miguel', 'Ramos', 'miguel_r', 'ramos.miguel@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 2, 1),
('Gabriela', 'Cruz', 'gab_cruz', 'cruz.gabriela@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 2, 1);

-- Users in College of Nursing (department_id = 3)
INSERT INTO users (first_name, last_name, username, email, password_hash, role_id, department_id, is_active) VALUES
('Ana', 'Martinez', 'ana_martinez', 'martinez.ana@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 3, 1),
('Ricardo', 'Flores', 'rico_flores', 'flores.ricardo@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 3, 1),
('Lucia', 'Gonzales', 'lucia_g', 'gonzales.lucia@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 3, 1),
('Fernando', 'Castro', 'fer_castro', 'castro.fernando@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 3, 1),
('Carmen', 'Villanueva', 'carmen_v', 'villanueva.carmen@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 3, 1);

-- Users in College of Education (department_id = 4)
INSERT INTO users (first_name, last_name, username, email, password_hash, role_id, department_id, is_active) VALUES
('Elena', 'Fernandez', 'elena_f', 'fernandez.elena@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 4, 1),
('Roberto', 'Diaz', 'rob_diaz', 'diaz.roberto@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 4, 1),
('Valentina', 'Morales', 'val_morales', 'morales.valentina@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 4, 1),
('Diego', 'Jimenez', 'diego_j', 'jimenez.diego@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 4, 1),
('Rosa', 'Aquino', 'rosa_aquino', 'aquino.rosa@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 4, 1);

-- Users in College of Engineering (department_id = 5)
INSERT INTO users (first_name, last_name, username, email, password_hash, role_id, department_id, is_active) VALUES
('Antonio', 'Valdez', 'tony_valdez', 'valdez.antonio@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 5, 1),
('Bianca', 'Ortega', 'bianca_o', 'ortega.bianca@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 5, 1),
('Rafael', 'Navarro', 'raf_navarro', 'navarro.rafael@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 5, 1),
('Camila', 'Herrera', 'cam_herrera', 'herrera.camila@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 5, 1),
('Sebastian', 'Salazar', 'seb_salazar', 'salazar.sebastian@plpasig.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 5, 1);

-- ============================================
-- SUMMARY
-- ============================================
-- Total Users: 32
-- - Super Admins: 2
-- - Department Admins: 5 (one per department)
-- - Regular Users: 25 (5 per department)
-- 
-- All passwords: Password@123
-- ============================================

-- Verify the insert
SELECT 
    r.role_name,
    COUNT(u.user_id) as user_count
FROM users u
LEFT JOIN roles r ON u.role_id = r.role_id
GROUP BY r.role_name;

-- Display all users
SELECT 
    u.user_id,
    u.first_name,
    u.last_name,
    u.username,
    u.email,
    r.role_name,
    d.department_name,
    u.is_active
FROM users u
LEFT JOIN roles r ON u.role_id = r.role_id
LEFT JOIN departments d ON u.department_id = d.department_id
ORDER BY u.role_id, u.department_id, u.user_id;
