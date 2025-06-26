<?php
include("../conn/conn.php");
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit();
}

// Track where the user came from for proper back navigation
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$from_profile_page = strpos($referrer, 'student-profile.php') !== false;

// Initialize variables
$student_id = isset($_GET['id']) ? $_GET['id'] : '';
$student = [];
$progress = [];
$error_message = '';

// Validate request parameters
if (empty($student_id)) {
    $_SESSION['message'] = "Invalid request: No student ID provided.";
    header('Location: ../admin/admin.php');
    exit();
}

// Fetch the student data
$query = "SELECT * FROM STUDENT WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    $_SESSION['message'] = "Student not found.";
    header('Location: ../admin/admin.php');
    exit();
}
$stmt->close();

// Check if GameProgress table exists
$check_progress_table = "SHOW TABLES LIKE 'GameProgress'";
$progress_table_check = mysqli_query($conn, $check_progress_table);
$has_progress_data = false;

if ($progress_table_check && mysqli_num_rows($progress_table_check) > 0) {
    // GameProgress table exists, fetch student progress
    $progress_query = "SELECT gp.*, q.title AS quiz_title 
                      FROM GameProgress gp
                      LEFT JOIN Quiz q ON gp.quiz_id = q.quiz_id
                      WHERE gp.student_id = ?
                      ORDER BY gp.date_taken DESC";
    
    $progress_stmt = $conn->prepare($progress_query);
    $progress_stmt->bind_param("s", $student_id);
    $progress_stmt->execute();
    $progress_result = $progress_stmt->get_result();
    
    if ($progress_result && $progress_result->num_rows > 0) {
        $has_progress_data = true;
        while ($row = $progress_result->fetch_assoc()) {
            $progress[] = $row;
        }
    }
    $progress_stmt->close();
}

// Get student statistics
$total_attempts = 0;
$completed_games = 0;
$total_score = 0;
$average_score = 0;

if ($has_progress_data) {
    $stats_query = "SELECT 
                   COUNT(*) as total_attempts,
                   COUNT(CASE WHEN lst_played >= 100 THEN 1 END) as completed_games,
                   SUM(score) as total_score,
                   AVG(score) as avg_score
                   FROM GameProgress
                   WHERE student_id = ?";
    
    $stats_stmt = $conn->prepare($stats_query);
    $stats_stmt->bind_param("s", $student_id);
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result();
    
    if ($stats_result && $stats_result->num_rows > 0) {
        $stats = $stats_result->fetch_assoc();
        $total_attempts = $stats['total_attempts'];
        $completed_games = $stats['completed_games'];
        $total_score = $stats['total_score'] ?? 0;
        $average_score = $stats['avg_score'] ?? 0;
    }
    $stats_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaQuest - Student Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/student-detail.css">
</head>
<body>
<header>
    <div class="logo-nav">
        <a href="../admin/admin.php" class="logo">JavaQuest Admin</a>
    </div>
    <button class="menu-btn" id="menuBtn">
        <i class="fas fa-bars"></i>
    </button>
    <div class="hamburger-menu" id="hamburgerMenu">
        <a href="../admin/admin.php" class="menu-item"><i class="fas fa-home"></i>Home</a>
        <a href="../admin/student-profile.php" class="menu-item"><i class="fas fa-user-graduate"></i>Students</a>
        <a href="#" class="menu-item" onclick="switchTab('questions'); return false;"><i class="fas fa-question-circle"></i>Questions</a>
        <a href="#" class="menu-item" onclick="switchTab('leaderboard'); return false;"><i class="fas fa-trophy"></i>Leaderboard</a>
        <a href="#" class="menu-item" onclick="switchTab('progress'); return false;"><i class="fas fa-chart-line"></i>Progress</a>
        <a href="../logout/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i>Log Out</a>
    </div>
</header>

<div class="main-container">
    <div class="dashboard-header">
        <h1><?php echo htmlspecialchars($student['username']); ?>'s Profile</h1>
        <p class="dashboard-subtitle">Student Profile Details</p>
    </div>

    <div class="content-container">
        <div class="profile-section">
            <div class="gender-avatar">
                <?php
                $gender = strtolower($student['gender']);
                if ($gender === 'male') {
                    echo '<img src="../image/male.png" alt="Male Avatar">';
                } elseif ($gender === 'female') {
                    echo '<img src="../image/female.png" alt="Female Avatar">';
                } else {
                    echo '<img src="../image/default.png" alt="Default Avatar">';
                }
                ?>
            </div>
            <div class="card-info">
                <div class="card-info-items">
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($student['gender']); ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($student['dob']); ?></p>
                </div>
                
                <div class="card-buttons">
                    <a href="admin-edit.php?type=student&id=<?php echo urlencode($student['student_id']); ?>" class="action-btn edit-btn">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                    <a href="admin-delete.php?type=student&id=<?php echo urlencode($student['student_id']); ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.')">
                        <i class="fas fa-trash"></i> Delete Student
                    </a>
                    <a href="<?php echo $from_profile_page ? 'student-profile.php' : 'admin.php#students'; ?>" class="action-btn view-btn">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Student Statistics -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-value"><?php echo $total_attempts; ?></div>
                <div class="stat-label">Total Attempts</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo $completed_games; ?></div>
                <div class="stat-label">Completed Games</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-value"><?php echo number_format($total_score, 0); ?></div>
                <div class="stat-label">Total Score</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value"><?php echo number_format($average_score, 1); ?></div>
                <div class="stat-label">Average Score</div>
            </div>
        </div>
        
        <!-- Progress History -->
        <div class="chart-container">
            <div class="chart-title">Progress History</div>
            
            <?php if ($has_progress_data && count($progress) > 0): ?>
            <div class="questions-table-container">
                <table class="questions-table">
                    <thead>
                        <tr>
                            <th>Progress ID</th>
                            <th>Quiz</th>
                            <th>Date Taken</th>
                            <th>Score</th>
                            <th>Level Achieved</th>
                            <th>Progress</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($progress as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['progress_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($entry['quiz_title'] ?? $entry['quiz_id']); ?></td>
                            <td><?php echo htmlspecialchars($entry['date_taken']); ?></td>
                            <td><?php echo htmlspecialchars($entry['score']); ?></td>
                            <td><?php echo htmlspecialchars($entry['level_achieved']); ?></td>
                            <td><?php echo htmlspecialchars($entry['lst_played']); ?>/100</td>
                            <td>
                                <?php if ($entry['lst_played'] >= 100): ?>
                                <span class="action-btn small view-btn">Completed</span>
                                <?php else: ?>
                                <span class="action-btn small edit-btn">In Progress</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p>No progress data available for this student yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3>About Us</h3>
            <ul>
                <li><a href="../about-us/about-us.php">About JavaQuest</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Resources</h3>
            <ul>
                <li><a href="../term-privacy/term-privacy.php">Terms</a></li>
                <li><a href="../term-privacy/term-privacy.php">Privacy</a></li>
            </ul>
        </div>
        <div class="footer-copyright">
            <p>Copyright &copy; 2025 JavaQuest. All rights reserved</p>
        </div>
    </div>
</footer>

<script>
    // Toggle hamburger menu
    document.getElementById('menuBtn').addEventListener('click', function() {
        document.getElementById('hamburgerMenu').classList.toggle('show');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('hamburgerMenu');
        const menuBtn = document.getElementById('menuBtn');
        
        if (!menu.contains(event.target) && !menuBtn.contains(event.target) && menu.classList.contains('show')) {
            menu.classList.remove('show');
        }
    });

    // Function to switch tabs (for hamburger menu)
    function switchTab(tabName) {
        window.location.href = 'admin.php#' + tabName;
    }
</script>
</body>
</html> 