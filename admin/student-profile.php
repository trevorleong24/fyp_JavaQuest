<?php
include("../conn/conn.php");
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit();
}

// Initialize variables
$students = [];
$error_message = '';
$success_message = '';

// Check for messages from redirects
if (isset($_SESSION['message'])) {
    $success_message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Fetch all students
$query = "SELECT * FROM STUDENT ORDER BY username";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
}

// Search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $filtered_students = [];
    
    foreach ($students as $student) {
        if (stripos($student['username'], $search) !== false || 
            stripos($student['email'], $search) !== false || 
            stripos($student['student_id'], $search) !== false) {
            $filtered_students[] = $student;
        }
    }
    
    $students = $filtered_students;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaQuest - Student Profiles</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/student-profile-admin.css">
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
        <a href="../admin/questions.php" class="menu-item"><i class="fas fa-question-circle"></i>Questions</a>
        <a href="../admin/leaderboard.php" class="menu-item"><i class="fas fa-trophy"></i>Leaderboard</a>
        <a href="../admin/progress.php" class="menu-item"><i class="fas fa-chart-line"></i>Progress</a>
        <a href="../logout/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i>Log Out</a>
    </div>
</header>

<div class="main-container">
    <div class="dashboard-header">
        <h1>Student Profiles</h1>
        <p class="dashboard-subtitle">View and manage student profiles</p>
    </div>

    <div class="content-container">
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Stats Overview -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo count($students); ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            <?php
            // Count by gender
            $maleCount = 0;
            $femaleCount = 0;
            foreach ($students as $student) {
                if (strtolower($student['gender']) === 'male') {
                    $maleCount++;
                } elseif (strtolower($student['gender']) === 'female') {
                    $femaleCount++;
                }
            }
            ?>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-mars"></i>
                </div>
                <div class="stat-value"><?php echo $maleCount; ?></div>
                <div class="stat-label">Male Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-venus"></i>
                </div>
                <div class="stat-value"><?php echo $femaleCount; ?></div>
                <div class="stat-label">Female Students</div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <form action="" method="GET" style="width: 100%; display: flex;">
                <input type="text" name="search" placeholder="Search by name, email, or ID..." class="search-bar" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="flex: 3; padding: 6px 10px;">
                <button type="submit" class="search-button" style="flex: 1; max-width: 120px; padding: 6px 10px;">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

        <?php if (count($students) > 0): ?>
            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                <div class="search-results-info">
                    <i class="fas fa-info-circle"></i> Found <?php echo count($students); ?> students matching "<?php echo htmlspecialchars($_GET['search']); ?>"
                    <a href="student-profile.php" class="reset-search"><i class="fas fa-times"></i> Clear search</a>
                </div>
            <?php endif; ?>
            
            <div class="card-grid">
                <?php foreach ($students as $student): ?>
                <div class="card">
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
                        <h3><?php echo htmlspecialchars($student['username']); ?></h3>
                        
                        <p><strong>ID:</strong> <span><?php echo htmlspecialchars($student['student_id']); ?></span></p>
                        <p><strong>Email:</strong> <span><?php echo htmlspecialchars($student['email']); ?></span></p>
                        <p><strong>Gender:</strong> <span><?php echo htmlspecialchars($student['gender']); ?></span></p>
                    </div>
                    
                    <div class="card-buttons">
                        <a href="student-detail.php?id=<?php echo urlencode($student['student_id']); ?>" class="action-btn view-btn">
                            <i class="fas fa-user"></i> View Profile
                        </a>
                        <a href="admin-edit.php?type=student&id=<?php echo urlencode($student['student_id']); ?>" class="action-btn edit-btn">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h3>No Students Found</h3>
            <p>No students match your search criteria. Try a different search or add new students.</p>
            <a href="java-student-add.php" class="action-btn edit-btn" style="margin-top: 1rem; display: inline-block;">
                <i class="fas fa-user-plus"></i> Add New Student
            </a>
        </div>
        <?php endif; ?>
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
</script>

<style>
/* Additional styles specific to this page */
.search-results-info {
    background: rgba(138, 43, 226, 0.1);
    border-left: 3px solid var(--primary-color);
    padding: 0.8rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.search-results-info i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

.reset-search {
    color: var(--accent-color);
    text-decoration: none;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.reset-search i {
    color: var(--accent-color);
    margin-right: 0.3rem;
}

.reset-search:hover {
    text-decoration: underline;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
    opacity: 0.7;
}

.empty-state h3 {
    color: var(--accent-color);
    margin-bottom: 0.5rem;
}

.empty-state p {
    opacity: 0.8;
    max-width: 500px;
    margin: 0 auto;
}

/* Make the avatar pop more */
.gender-avatar img {
    transition: all 0.3s ease;
    box-shadow: 0 0 0 3px rgba(138, 43, 226, 0.3);
}

.card:hover .gender-avatar img {
    transform: scale(1.05);
    box-shadow: 0 0 0 4px rgba(138, 43, 226, 0.5);
}

/* Give more definition to the cards */
.card {
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.card h3 {
    color: var(--accent-color);
    margin-bottom: 1rem;
    text-align: center;
    font-size: 1.2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 0.5rem;
}
</style>
</body>
</html> 