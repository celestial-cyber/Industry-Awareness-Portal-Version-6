-- Database Migration for IAP Portal Student System
-- This file creates/updates tables needed for student authentication and dashboard

-- Create students table (separate from admin users)
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll_number VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    department VARCHAR(100),
    year ENUM('1', '2', '3', '4') NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_password_changed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create sessions table with title (update existing table if needed)
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    year ENUM('1', '2', '3', '4') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create student_sessions junction table (many-to-many relationship)
CREATE TABLE IF NOT EXISTS student_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    session_id INT NOT NULL,
    registration_status ENUM('registered', 'completed', 'dropped') DEFAULT 'registered',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_session (student_id, session_id)
);

-- Insert sample students with default password (hash of "student@IAP")
-- Default password hash: password_hash("student@IAP", PASSWORD_BCRYPT) 
INSERT IGNORE INTO students (roll_number, full_name, email, department, year, password, is_password_changed) 
VALUES 
('2021001', 'John Doe', 'john.doe@example.com', 'Computer Science', '1', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/ECm', FALSE),
('2021002', 'Jane Smith', 'jane.smith@example.com', 'Information Technology', '2', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/ECm', FALSE),
('2021003', 'Bob Johnson', 'bob.johnson@example.com', 'Electronics', '3', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/ECm', FALSE),
('2021004', 'Alice Brown', 'alice.brown@example.com', 'Mechanical', '4', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/ECm', FALSE);

-- Insert sample sessions
INSERT IGNORE INTO sessions (id, title, year, description) 
VALUES 
(1, 'Introduction to Engineering Careers', '1', 'Career awareness and industry expectations'),
(2, 'How to Ace Ideathons', '1', 'Innovation and problem-solving skills'),
(3, 'Resume Building and Career Positioning', '2', 'Professional skills development'),
(4, 'Interview Preparation Fundamentals', '2', 'Interview techniques and tips'),
(5, 'Internship Readiness Program', '3', 'Preparing for internships'),
(6, 'Advanced System Design', '3', 'Technical depth and scalability'),
(7, 'Startup Ecosystem and Entrepreneurship', '4', 'Entrepreneurial pathways'),
(8, 'Leadership and Management Skills', '4', 'Leadership development');

-- Link sample students to sessions (registrations)
INSERT IGNORE INTO student_sessions (student_id, session_id, registration_status) 
VALUES 
(1, 1, 'registered'),
(1, 2, 'registered'),
(2, 3, 'registered'),
(2, 4, 'completed'),
(3, 5, 'registered'),
(3, 6, 'registered'),
(4, 7, 'registered'),
(4, 8, 'registered');
