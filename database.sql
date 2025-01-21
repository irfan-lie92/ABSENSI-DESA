CREATE DATABASE IF NOT EXISTS attendance_system;
USE attendance_system;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    position_order INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    staff_id INT NOT NULL,
    date DATE NOT NULL,
    signature TEXT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    UNIQUE KEY unique_attendance (staff_id, date)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Insert staff members
INSERT INTO staff (name, position, position_order) VALUES
('Didi Dulhadi', 'Kepala Desa', 1),
('Asep Saefulloh, ST', 'Sekretaris Desa', 2),
('Irfan Ali, S.Pd', 'Kaur TU dan Umum', 3),
('Ade Siti Nurjanah', 'Kaur Perencanaan', 4),
('Iin Indriani, A.Md', 'Kaur Keuangan', 5),
('Wina Agustina', 'Kasi Pemerintahan', 6),
('Armin', 'Kasi Kesejahteraan', 7),
('Lili Andalusi', 'Kasi Pelayanan', 8),
('Didin Saidin', 'Kepala Dusun Kidul', 9),
('Muhammad Idris, S.Pd', 'Kepala Dusun Tengah', 10),
('Nur Fauzi Hidayatullah, SE', 'Kepala Dusun Kaler', 11);