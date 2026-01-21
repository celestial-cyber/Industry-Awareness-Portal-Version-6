<?php
session_start();

// Check if user is logged in as student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../student_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password_db = "root@123";

$conn = new mysqli($servername, $username, $password_db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->select_db("iap_portal");

// Get all available sessions
$sessions = [];
for ($year = 1; $year <= 4; $year++) {
    $sql = "SELECT id, topic FROM sessions WHERE year = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $sessions[$year] = [];
    while ($row = $result->fetch_assoc()) {
        $sessions[$year][] = $row;
    }
    $stmt->close();
}

// Get student's registrations
$student_email = $_SESSION['email'];
$sql = "SELECT * FROM session_registrations WHERE email = ? ORDER BY submitted_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();
$registrations = [];
while ($row = $result->fetch_assoc()) {
    $registrations[] = $row;
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - IAP Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
        }

        body {
            background: #f3f4f6;
            color: #374151;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
        }

        /* Header */
        .header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            color: #0ea5e9;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: #ef4444;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        /* Dashboard Cards */
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .card h2 {
            color: #0ea5e9;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .card h3 {
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 18px;
        }

        /* Sessions Section */
        .sessions-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .session-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            transition: 0.3s;
        }

        .session-card:hover {
            background: #f1f5f9;
            border-color: #0ea5e9;
        }

        .session-year {
            color: #0ea5e9;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .session-topic {
            color: #374151;
            margin-bottom: 10px;
        }

        .register-btn {
            background: #0ea5e9;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }

        .register-btn:hover {
            background: #0284c7;
        }

        /* Registrations Section */
        .registrations-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .registration-item {
            background: #f8fafc;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            border-radius: 8px;
        }

        .registration-item strong {
            color: #0ea5e9;
        }

        .registration-detail {
            font-size: 14px;
            color: #6b7280;
            margin-top: 8px;
        }

        .registration-date {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 10px;
        }

        /* Welcome Section */
        .welcome-section {
            grid-column: 1 / -1;
            background: linear-gradient(135deg, #e0f2fe, #f0f9ff);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .welcome-section h1 {
            color: #0369a1;
            margin-bottom: 10px;
        }

        .welcome-section p {
            color: #0c4a6e;
            font-size: 16px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 40px;
            color: #d1d5db;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <h1><i class="fas fa-graduation-cap"></i> Student Dashboard</h1>
            <div class="user-info">
                <span><strong>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</strong></span>
                <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome to IAP Portal!</h1>
            <p>Explore available sessions, register for sessions that interest you, and track your enrollments. Select sessions aligned with your career goals and learning path.</p>
        </div>

        <div class="main-content">
            <!-- Available Sessions -->
            <div class="card">
                <h2><i class="fas fa-list"></i> Available Sessions</h2>
                <?php if (!empty($sessions) && array_filter($sessions)): ?>
                    <div class="sessions-grid">
                        <?php for ($year = 1; $year <= 4; $year++): ?>
                            <?php if (!empty($sessions[$year])): ?>
                                <?php foreach ($sessions[$year] as $session): ?>
                                    <div class="session-card">
                                        <div class="session-year">Year <?php echo $year; ?></div>
                                        <div class="session-topic"><?php echo htmlspecialchars($session['topic']); ?></div>
                                        <form action="../register.php" method="post" style="display: inline;">
                                            <input type="hidden" name="session_desired" value="<?php echo htmlspecialchars($session['topic']); ?>">
                                            <input type="hidden" name="year" value="<?php echo $year; ?>">
                                            <button type="submit" class="register-btn"><i class="fas fa-plus"></i> Register</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No sessions available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Your Registrations -->
            <div class="card">
                <h2><i class="fas fa-bookmark"></i> Your Registrations</h2>
                <?php if (!empty($registrations)): ?>
                    <div class="registrations-list">
                        <?php foreach ($registrations as $reg): ?>
                            <div class="registration-item">
                                <strong><?php echo htmlspecialchars($reg['session_desired']); ?></strong>
                                <div class="registration-detail">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($reg['name']); ?><br>
                                    <i class="fas fa-graduation-cap"></i> Year <?php echo htmlspecialchars($reg['year']); ?> • <?php echo htmlspecialchars($reg['department']); ?><br>
                                    <i class="fas fa-barcode"></i> Roll: <?php echo htmlspecialchars($reg['roll_number']); ?>
                                </div>
                                <div class="registration-date">
                                    Registered: <?php echo date('M d, Y - H:i', strtotime($reg['submitted_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-clipboard"></i>
                        <p>You haven't registered for any sessions yet.</p>
                        <p style="margin-top: 10px; font-size: 14px;">Browse available sessions and register to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
