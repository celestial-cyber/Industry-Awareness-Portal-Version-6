<?php
/**
 * Student Login Page
 * Authenticates students using roll number and password
 * Uses MySQLi prepared statements for security
 */

session_start();

// If already logged in as student, redirect to dashboard
if (isset($_SESSION['student_id']) && isset($_SESSION['roll_number'])) {
    header("Location: student_dashboard.php");
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_number = trim($_POST['roll_number'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Input validation
    if (empty($roll_number)) {
        $error_message = "Roll number is required";
    } elseif (empty($password)) {
        $error_message = "Password is required";
    } else {
        // Database connection
        $servername = "localhost";
        $db_username = "root";
        $db_password = "root@123";
        
        $conn = new mysqli($servername, $db_username, $db_password);
        
        if ($conn->connect_error) {
            $error_message = "Database connection failed";
        } else {
            // Create database if not exists
            $sql = "CREATE DATABASE IF NOT EXISTS iap_portal";
            $conn->query($sql);
            $conn->select_db("iap_portal");
            
            // Create students table if not exists
            $create_table_sql = "CREATE TABLE IF NOT EXISTS students (
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
            )";
            $conn->query($create_table_sql);
            
            // Create student_sessions table if not exists
            $create_sessions_sql = "CREATE TABLE IF NOT EXISTS student_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NOT NULL,
                session_id INT NOT NULL,
                registration_status ENUM('registered', 'completed', 'dropped') DEFAULT 'registered',
                registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
                FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
                UNIQUE KEY unique_student_session (student_id, session_id)
            )";
            $conn->query($create_sessions_sql);
            
            // Insert sample student if table is empty (for testing)
            $check_sql = "SELECT COUNT(*) as count FROM students";
            $result = $conn->query($check_sql);
            $row = $result->fetch_assoc();
            
            if ($row['count'] == 0) {
                // Default password is "student@IAP"
                $default_password_hash = password_hash("student@IAP", PASSWORD_BCRYPT);
                $insert_sql = "INSERT IGNORE INTO students (roll_number, full_name, email, department, year, password, is_password_changed) 
                              VALUES ('2021001', 'Test Student', 'test@example.com', 'Computer Science', '1', ?, FALSE)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("s", $default_password_hash);
                $insert_stmt->execute();
                $insert_stmt->close();
            }
            
            // Prepare authentication query using prepared statement
            $sql = "SELECT id, roll_number, full_name, email, department, year, password, is_password_changed FROM students WHERE roll_number = ?";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                $error_message = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("s", $roll_number);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $student = $result->fetch_assoc();
                    
                    // Verify password using password_verify()
                    if (password_verify($password, $student['password'])) {
                        // Password is correct - set session variables
                        $_SESSION['student_id'] = $student['id'];
                        $_SESSION['roll_number'] = $student['roll_number'];
                        $_SESSION['full_name'] = $student['full_name'];
                        $_SESSION['email'] = $student['email'];
                        $_SESSION['department'] = $student['department'];
                        $_SESSION['year'] = $student['year'];
                        $_SESSION['is_password_changed'] = $student['is_password_changed'];
                        
                        // If password is still default (not changed), redirect to password reset
                        if (!$student['is_password_changed']) {
                            header("Location: reset_password.php?first_login=1");
                            exit();
                        } else {
                            // Password already changed, go to dashboard
                            header("Location: student_dashboard.php");
                            exit();
                        }
                    } else {
                        $error_message = "Invalid password. Please try again.";
                    }
                } else {
                    $error_message = "Roll number not found. Please check and try again.";
                }
                
                $stmt->close();
            }
            
            $conn->close();
        }
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - IAP Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Theme CSS -->
    <link rel="stylesheet" href="theme.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .login-header p {
            font-size: 14px;
            margin: 0;
            opacity: 0.9;
        }

        .login-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .form-control {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 700;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            pointer-events: none;
        }

        .input-icon .form-control {
            padding-left: 45px;
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #c53030;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #15803d;
        }

        .demo-credentials {
            background: #f8f9ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #555;
        }

        .demo-credentials strong {
            color: #333;
        }

        @media (max-width: 500px) {
            .login-body {
                padding: 25px;
            }

            .login-header {
                padding: 20px 15px;
            }

            .login-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-graduation-cap"></i> Student Login</h1>
                <p>IAP Portal - Access Your Dashboard</p>
            </div>

            <div class="login-body">
                <!-- Error Message Alert -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Success Message Alert -->
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Demo Credentials Box -->
                <div class="demo-credentials">
                    <strong><i class="fas fa-info-circle"></i> Demo Credentials:</strong><br>
                    Roll Number: <strong>2021001</strong><br>
                    Password: <strong>student@IAP</strong>
                </div>

                <!-- Login Form -->
                <form method="POST" action="" novalidate>
                    <div class="form-group">
                        <label for="roll_number" class="form-label">Roll Number</label>
                        <div class="input-icon">
                            <i class="fas fa-id-card"></i>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="roll_number" 
                                name="roll_number" 
                                placeholder="Enter your roll number"
                                required
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                    </div>

                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                <div class="back-link">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
