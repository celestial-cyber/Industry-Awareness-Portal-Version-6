<?php
/**
 * Student Dashboard
 * Displays personalized dashboard with registered sessions
 * Shows only sessions the student is registered for
 * Includes "Take Quiz" button for each session
 */

// Include session protection - must be at the top
require_once 'Student/student_session_check.php';

$error_message = '';
$registered_sessions = [];

try {
    // Fetch student's registered sessions using MySQLi prepared statement
    $sql = "SELECT 
                s.id,
                s.topic as title,
                s.year,
                '' as description,
                ss.registration_status,
                ss.registered_at
            FROM sessions s
            JOIN student_sessions ss ON s.id = ss.session_id
            WHERE ss.student_id = ?
            ORDER BY s.year ASC, s.topic ASC";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $error_message = "Database error: " . $conn->error;
    } else {
        $stmt->bind_param("i", $_SESSION['student_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $registered_sessions[] = $row;
        }
        
        $stmt->close();
    }
} catch (Exception $e) {
    $error_message = "Error fetching sessions: " . $e->getMessage();
}

// Group sessions by year
$sessions_by_year = [];
foreach ($registered_sessions as $session) {
    $year = $session['year'];
    if (!isset($sessions_by_year[$year])) {
        $sessions_by_year[$year] = [];
    }
    $sessions_by_year[$year][] = $session;
}

// Logout function
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: Student/student_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - IAP Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Theme CSS -->
    <link rel="stylesheet" href="theme.css">
    <style>
        :root {
            --primary-color: #7c3aed;
            --primary-light: #f3e8ff;
            --primary-dark: #5b21b6;
            --secondary-color: #f8fafc;
            --accent-color: #10b981;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        .dashboard-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
            border-right: 1px solid var(--border-color);
            padding: 24px;
            position: fixed;
            left: 0;
            top: 70px;
            height: calc(100vh - 70px - 80px); /* Subtract footer height */
            overflow-y: auto;
            box-shadow: var(--shadow);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
        }

        .sidebar-logo img {
            height: 80px;
            width: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-light);
            box-shadow: var(--shadow);
        }

        .sidebar-nav .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            margin-bottom: 8px;
            text-decoration: none;
            color: var(--text-secondary);
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            position: relative;
        }

        .sidebar-nav .sidebar-link:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            transform: translateX(4px);
        }

        .sidebar-nav .sidebar-link.active {
            background: linear-gradient(135deg, var(--primary-light) 0%, rgba(255, 255, 255, 0.9) 100%);
            color: var(--primary-color);
            box-shadow: var(--shadow);
            transform: translateX(4px);
            border: 1px solid var(--primary-light);
        }

        .sidebar-nav .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: var(--primary-color);
            border-radius: 0 2px 2px 0;
        }

        .main-dashboard-content {
            margin-left: 260px;
            flex: 1;
            width: calc(100% - 260px);
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .container-lg {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 0 24px;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
        }

        .navbar-custom .navbar-brand {
            font-weight: 700;
            font-size: 22px;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar-brand img {
            height: 42px;
            width: auto;
            filter: brightness(1.1);
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.95) !important;
            font-weight: 500;
            transition: all 0.3s;
            white-space: nowrap;
            padding: 0.5rem 1rem !important;
        }

        .navbar-custom .nav-link:hover {
            color: white !important;
        }

        .navbar-nav {
            gap: 20px;
            align-items: center;
        }

        .navbar-nav .nav-item {
            display: flex;
            align-items: center;
        }

        .user-info {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .user-info small {
            display: block;
            font-size: 12px;
            opacity: 0.9;
        }

        .user-name {
            font-weight: 600;
            color: white;
        }

        /* Main Content */
        .dashboard-container {
            padding: 30px 20px;
            flex: 1;
        }

        .welcome-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            color: var(--text-primary);
            padding: 32px 24px;
            border-radius: 16px;
            margin-bottom: 32px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .welcome-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .welcome-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .welcome-header p {
            font-size: 16px;
            margin: 0;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .student-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .info-badge {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 16px 20px;
            border-radius: 12px;
            font-size: 14px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .info-badge:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .info-badge strong {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Section Title */
        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 3px solid var(--primary-color);
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        /* Year Section */
        .year-section {
            margin-bottom: 48px;
        }

        .year-header {
            background: linear-gradient(135deg, var(--primary-light) 0%, rgba(255,255,255,0.8) 100%);
            padding: 20px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            border-left: 4px solid var(--primary-color);
            box-shadow: var(--shadow);
        }

        .year-header h2 {
            font-size: 22px;
            color: var(--primary-color);
            margin: 0;
            font-weight: 600;
            font-weight: 700;
        }

        /* Session Cards */
        .sessions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .session-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid var(--border-color);
        }

        .session-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-light);
        }

        .session-card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 24px;
            position: relative;
        }

        .session-card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            pointer-events: none;
        }

        .session-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 12px 0;
            line-height: 1.4;
            color: var(--text-primary);
            position: relative;
            z-index: 1;
        }

        .session-year-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
            position: relative;
            z-index: 1;
        }

        .session-card-body {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .session-description {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 16px;
            flex-grow: 1;
            line-height: 1.6;
        }

        .session-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
            box-shadow: var(--shadow);
        }

        .status-registered {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-completed {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .status-dropped {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .quiz-button {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            width: 100%;
        }

        .quiz-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
            color: white;
            text-decoration: none;
        }

        .quiz-button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 24px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .empty-state-icon {
            font-size: 64px;
            color: var(--text-secondary);
            margin-bottom: 24px;
            opacity: 0.6;
        }

        .empty-state h3 {
            font-size: 24px;
            color: var(--text-primary);
            margin-bottom: 12px;
            font-weight: 600;
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 16px;
            margin: 0;
            line-height: 1.6;
        }

        /* Alert */
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 24px;
            font-size: 14px;
            box-shadow: var(--shadow);
            padding: 16px 20px;
        }

        /* Footer */
        .dashboard-footer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 0%);
            color: white;
            text-align: center;
            padding: 20px;
            position: fixed;
            bottom: 0;
            left: 260px;
            right: 0;
            width: calc(100% - 260px);
            z-index: 100;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .dashboard-footer p {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
        }

        /* Adjust main content to account for fixed footer */
        .main-dashboard-content {
            margin-left: 260px;
            margin-bottom: 80px; /* Space for footer */
            flex: 1;
            width: calc(100% - 260px);
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #c53030;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #15803d;
        }

        .alert-info {
            background-color: #f0f9ff;
            color: #0369a1;
        }

        /* Footer */
        .dashboard-footer {
            text-align: center;
            padding: 30px 20px;
            color: #fcfcfc;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .dashboard-sidebar {
                width: 100% !important;
                height: auto !important;
                position: relative !important;
                top: auto !important;
                padding: 16px !important;
                border-right: none !important;
                border-bottom: 1px solid var(--border-color) !important;
            }

            .main-dashboard-content {
                margin-left: 0 !important;
                width: 100% !important;
                margin-bottom: 120px !important; /* More space for mobile footer */
            }

            .dashboard-footer {
                left: 0 !important;
                width: 100% !important;
                position: relative !important;
                margin-top: 40px;
            }

            .welcome-header {
                padding: 25px 20px;
            }

            .welcome-header h1 {
                font-size: 24px;
            }

            .sessions-grid {
                grid-template-columns: 1fr;
            }

            .student-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-lg">
            <a class="navbar-brand" href="index.php">
                Industry Awareness Program Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <div class="user-info">
                            <i class="fas fa-user-circle"></i> 
                            <div>
                                <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                                <small><?php echo htmlspecialchars($_SESSION['email']); ?></small>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?logout=1">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="dashboard-sidebar">
        <div class="sidebar-logo">
            <div style="display: flex; align-items: center; gap: 12px;">
                <img src="images/SA Main logo.jpg" alt="SA Main Logo" title="SA Main">
                <div style="display: flex; flex-direction: column;">
                        <span style="font-size: 18px; font-weight: 700; color: #7c3aed; line-height: 1.2;">SPECANCIENS</span>
                        <span style="font-size: 14px; font-weight: 700; color: #6b7280; line-height: 1.2;">IAP Portal</span>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="sidebar-nav" style="margin-top: 20px;">
            <a href="?view=dashboard" class="sidebar-link <?php echo (!isset($_GET['view']) || $_GET['view'] == 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>

            <a href="?view=register_session" class="sidebar-link <?php echo (isset($_GET['view']) && $_GET['view'] == 'register_session') ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i> Register for Session
            </a>

            <a href="?view=view_progress" class="sidebar-link <?php echo (isset($_GET['view']) && $_GET['view'] == 'view_progress') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> View Progress
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-dashboard-content">
        <div class="container-lg">
            <?php
            $view = isset($_GET['view']) ? $_GET['view'] : 'dashboard';

            if ($view == 'dashboard') {
                // Default dashboard view
                ?>
                <!-- Welcome Header -->
                <div class="welcome-header">
                    <h1><i class="fas fa-chart-line"></i> Welcome, <?php echo htmlspecialchars(explode(' ', $_SESSION['full_name'])[0]); ?>!</h1>
                    <p>Here are the IAP sessions you have registered for. Click "Take Quiz" to participate in a session's quiz.</p>

                    <div class="student-info-grid">
                        <div class="info-badge">
                            <strong>Roll Number:</strong> <?php echo htmlspecialchars($_SESSION['roll_number']); ?>
                        </div>
                        <div class="info-badge">
                            <strong>Department:</strong> <?php echo htmlspecialchars($_SESSION['department']); ?>
                        </div>
                        <div class="info-badge">
                            <strong>Year:</strong> Year <?php echo htmlspecialchars($_SESSION['year']); ?>
                        </div>
                        <div class="info-badge">
                            <strong>Sessions Registered:</strong> <?php echo count($registered_sessions); ?>
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Sessions Content -->
                <?php if (!empty($sessions_by_year)): ?>
                    <h2 class="section-title"><i class="fas fa-list"></i> Your Registered Sessions</h2>
                <?php endif; ?>
            <?php
            } elseif ($view == 'register_session') {
                // Register for Session view
                ?>
                <div class="welcome-header">
                    <h1><i class="fas fa-plus-circle"></i> Register for Sessions</h1>
                    <p>Browse all available IAP sessions and register for the ones that interest you.</p>
                </div>
                <?php
                // Fetch all available sessions
                $all_sessions_sql = "SELECT s.*, COUNT(ss.student_id) as registered_count FROM sessions s
                                   LEFT JOIN student_sessions ss ON s.id = ss.session_id
                                   GROUP BY s.id ORDER BY s.year ASC, s.title ASC";
                $all_sessions_result = $conn->query($all_sessions_sql);

                if ($all_sessions_result && $all_sessions_result->num_rows > 0):
                    // Group sessions by year
                    $all_sessions_by_year = [];
                    while ($session = $all_sessions_result->fetch_assoc()) {
                        $all_sessions_by_year[$session['year']][] = $session;
                    }

                    foreach ($all_sessions_by_year as $year => $year_sessions):
                        ?>
                        <div class="year-section" style="margin-bottom: 30px;">
                            <h3 class="year-title" style="color: #7c3aed; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f0f4ff;">
                                <i class="fas fa-graduation-cap"></i> Year <?php echo $year; ?> Sessions
                            </h3>

                            <div class="sessions-grid">
                                <?php foreach ($year_sessions as $session):
                                    // Check if student is already registered
                                    $is_registered = false;
                                    foreach ($registered_sessions as $registered) {
                                        if ($registered['id'] == $session['id']) {
                                            $is_registered = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="session-card <?php echo $is_registered ? 'registered' : ''; ?>" style="background: <?php echo $is_registered ? '#f0fdf4' : '#ffffff'; ?>; border: 1px solid <?php echo $is_registered ? '#d1fae5' : '#e5e7eb'; ?>; border-radius: 12px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <div class="session-card-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                            <h4 style="margin: 0; color: #1f2937;"><?php echo htmlspecialchars($session['title']); ?></h4>
                                            <span class="session-year-badge" style="background: #7c3aed; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                                Year <?php echo htmlspecialchars($session['year']); ?>
                                            </span>
                                        </div>

                                        <?php if ($session['description']): ?>
                                            <p style="color: #6b7280; margin-bottom: 15px; font-size: 14px;"><?php echo htmlspecialchars($session['description']); ?></p>
                                        <?php endif; ?>

                                        <div class="session-stats" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                            <small style="color: #6b7280;">
                                                <i class="fas fa-users"></i> <?php echo $session['registered_count']; ?> registered
                                            </small>
                                            <?php if ($is_registered): ?>
                                                <span style="background: #10b981; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                                    <i class="fas fa-check"></i> Registered
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="session-actions" style="display: flex; gap: 10px;">
                                            <button onclick="viewSessionDetail(<?php echo $session['id']; ?>, '<?php echo htmlspecialchars($session['title']); ?>')" class="btn btn-outline-primary btn-sm" style="flex: 1; padding: 8px; border: 1px solid #7c3aed; color: #7c3aed; border-radius: 6px; background: transparent; cursor: pointer;">
                                                <i class="fas fa-info-circle"></i> View Details
                                            </button>

                                            <?php if ($is_registered): ?>
                                                <a href="quiz.php?session_id=<?php echo $session['id']; ?>" class="btn btn-primary btn-sm" style="flex: 1; padding: 8px; background: #7c3aed; color: white; border-radius: 6px; text-decoration: none; text-align: center;">
                                                    <i class="fas fa-play"></i> Take Quiz
                                                </a>
                                            <?php else: ?>
                                                <button onclick="registerForSession(<?php echo $session['id']; ?>, '<?php echo htmlspecialchars($session['title']); ?>')" class="btn btn-success btn-sm" style="flex: 1; padding: 8px; background: #10b981; color: white; border-radius: 6px; border: none; cursor: pointer;">
                                                    <i class="fas fa-plus"></i> Register
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info" style="padding: 20px; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; color: #1e40af;">
                        <i class="fas fa-info-circle"></i> No sessions are currently available.
                    </div>
                <?php endif; ?>

                <?php
            } elseif ($view == 'view_progress') {
                // View Progress
                ?>
                <div class="welcome-header">
                    <h1><i class="fas fa-chart-line"></i> Your Progress</h1>
                    <p>Track your performance across all registered IAP sessions.</p>
                </div>
                <?php
                if (!empty($registered_sessions)): ?>
                    <div class="progress-overview" style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 30px;">
                        <h4 style="margin-bottom: 15px; color: #1f2937;">Progress Summary</h4>
                        <div class="progress-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                            <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, var(--primary-light) 100%); padding: 20px; border-radius: 12px; border-left: 4px solid var(--primary-color); box-shadow: var(--shadow); transition: all 0.3s ease;">
                                <div class="stat-number" style="font-size: 26px; font-weight: bold; color: var(--primary-color);"><?php echo count($registered_sessions); ?></div>
                                <div class="stat-label" style="color: var(--text-secondary); font-size: 14px; font-weight: 500;">Total Registered</div>
                            </div>
                            <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #dcfce7 100%); padding: 20px; border-radius: 12px; border-left: 4px solid var(--accent-color); box-shadow: var(--shadow); transition: all 0.3s ease;">
                                <div class="stat-number" style="font-size: 26px; font-weight: bold; color: #059669;">
                                    <?php echo count(array_filter($registered_sessions, function($s) { return $s['registration_status'] === 'completed'; })); ?>
                                </div>
                                <div class="stat-label" style="color: var(--text-secondary); font-size: 14px; font-weight: 500;">Completed</div>
                            </div>
                            <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%); padding: 20px; border-radius: 12px; border-left: 4px solid #f59e0b; box-shadow: var(--shadow);">
                                <div class="stat-number" style="font-size: 26px; font-weight: bold; color: #d97706;">
                                    <?php echo count(array_filter($registered_sessions, function($s) { return $s['registration_status'] === 'registered'; })); ?>
                                </div>
                                <div class="stat-label" style="color: var(--text-secondary); font-size: 14px; font-weight: 500;">In Progress</div>
                            </div>
                        </div>
                    </div>

                    <h3 style="margin-bottom: 24px; color: var(--text-primary); font-size: 24px; font-weight: 600;">Session Details</h3>

                    <?php foreach ($sessions_by_year as $year => $year_sessions): ?>
                        <div class="year-progress-section" style="margin-bottom: 30px;">
                            <h4 style="color: var(--primary-color); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--primary-light); font-size: 20px; font-weight: 600;">
                                <i class="fas fa-graduation-cap"></i> Year <?php echo $year; ?> Sessions
                            </h4>

                            <div class="progress-sessions">
                                <?php foreach ($year_sessions as $session): ?>
                                    <div class="progress-card" style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <div class="progress-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                            <h5 style="margin: 0; color: #1f2937;"><?php echo htmlspecialchars($session['title']); ?></h5>
                                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                                <?php
                                                switch($session['registration_status']) {
                                                    case 'completed': echo 'background: #d1fae5; color: #065f46;'; break;
                                                    case 'registered': echo 'background: #dbeafe; color: #1e40af;'; break;
                                                    default: echo 'background: #f3f4f6; color: #374151;';
                                                }
                                                ?>">
                                                <?php echo ucfirst($session['registration_status']); ?>
                                            </span>
                                        </div>

                                        <div class="progress-details" style="margin-bottom: 15px;">
                                            <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                                <span style="color: #6b7280; font-size: 14px;">Registered:</span>
                                                <span style="font-weight: 600;"><?php echo date('M j, Y', strtotime($session['registered_at'])); ?></span>
                                            </div>
                                        </div>

                                        <div class="progress-actions" style="display: flex; gap: 10px;">
                                            <a href="quiz.php?session_id=<?php echo $session['id']; ?>" class="btn btn-primary btn-sm" style="flex: 1; padding: 8px; background: #7c3aed; color: white; border-radius: 6px; text-decoration: none; text-align: center;">
                                                <i class="fas fa-play"></i> Take Quiz
                                            </a>
                                            <button onclick="viewSessionDetail(<?php echo $session['id']; ?>, '<?php echo htmlspecialchars($session['title']); ?>')" class="btn btn-outline-secondary btn-sm" style="flex: 1; padding: 8px; border: 1px solid #6b7280; color: #6b7280; border-radius: 6px; background: transparent; cursor: pointer;">
                                                <i class="fas fa-info-circle"></i> Details
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info" style="padding: 24px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid var(--primary-light); border-radius: 12px; color: var(--primary-color); box-shadow: var(--shadow);">
                        <i class="fas fa-info-circle"></i> You haven't registered for any sessions yet. Visit the "Register for Session" section to get started!
                    </div>
                <?php endif; ?>
            <?php
            }
            ?>

        <!-- Footer -->
        <div class="dashboard-footer">
            <p>&copy; <?php echo date('Y'); ?> IAP Portal - SPECANCIENS. All rights reserved.</p>
        </div>
    </div>

    <!-- Session Registration Modal -->
    <div id="sessionModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sessionModalTitle">Session Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="sessionModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="registerBtn" style="display: none;">Register for Session</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Function to view session details
    function viewSessionDetail(sessionId, sessionTitle) {
        // For now, show basic info. In a real app, this would fetch from server
        const modalBody = document.getElementById('sessionModalBody');
        const modalTitle = document.getElementById('sessionModalTitle');
        const registerBtn = document.getElementById('registerBtn');

        modalTitle.textContent = sessionTitle;
        modalBody.innerHTML = `
            <div class="text-center mb-4">
                <i class="fas fa-info-circle fa-3x text-primary mb-3"></i>
                <h4>${sessionTitle}</h4>
            </div>
            <p><strong>Session ID:</strong> ${sessionId}</p>
            <p><strong>Description:</strong> This session covers important topics related to Industry Awareness Program. Please register to access detailed content and quizzes.</p>
            <div class="alert alert-info">
                <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Register for this session to unlock quizzes and track your progress!
            </div>
        `;

        registerBtn.style.display = 'inline-block';
        registerBtn.onclick = function() {
            registerForSession(sessionId, sessionTitle);
        };

        const modal = new bootstrap.Modal(document.getElementById('sessionModal'));
        modal.show();
    }

    // Function to register for a session
    function registerForSession(sessionId, sessionTitle) {
        if (confirm(`Are you sure you want to register for "${sessionTitle}"?`)) {
            // Send registration request
            fetch('session_registration.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'session_id=' + sessionId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Successfully registered for the session!');
                    location.reload(); // Refresh to show updated status
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }
    </script>
</body>
</html>
