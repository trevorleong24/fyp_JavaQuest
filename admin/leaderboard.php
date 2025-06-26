<?php
include("../conn/conn.php");
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit();
}

// Fetch leaderboard data (check if gameprogress table exists first)
$leaderboard_result = false; // Default to false if table doesn't exist
$check_progress_table = "SHOW TABLES LIKE 'gameprogress'";
$progress_table_check = mysqli_query($conn, $check_progress_table);

if ($progress_table_check && mysqli_num_rows($progress_table_check) > 0) {
    // gameprogress table exists, try to query leaderboard data
    $leaderboard_query = "SELECT s.username, s.student_id, s.gender, 
                      MAX(p.level_achieved) AS level_achieved,
                      MAX(p.score) AS highest_score
                      FROM STUDENT s
                      INNER JOIN gameprogress p ON s.student_id = p.student_id
                      GROUP BY s.student_id
                      ORDER BY highest_score DESC, level_achieved DESC";

    $leaderboard_result = mysqli_query($conn, $leaderboard_query);
    if (!$leaderboard_result) {
        // Query failed
        $leaderboard_result = false;
    }
} else {
    // If gameprogress table doesn't exist, create a simple query with just student data
    $leaderboard_query = "SELECT username, student_id, gender, 
                          'N/A' as level_achieved, 
                          0 as highest_score 
                          FROM STUDENT
                          ORDER BY username";
    $leaderboard_result = mysqli_query($conn, $leaderboard_query);
}

// Get total students count
$student_count = 0;
if ($leaderboard_result) {
    $student_count = mysqli_num_rows($leaderboard_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JavaQuest Admin - Leaderboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/leaderboard.css">
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
            <h1>Student Leaderboard</h1>
            <p class="dashboard-subtitle">Track student progress and scores in JavaQuest</p>
        </div>

        <div class="content-container">
            <div class="search-container">
                <input type="text" id="leaderboardSearchInput" class="search-bar" placeholder="Search students...">
                <button class="search-button" id="searchBtn">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>

            <div class="leaderboard-container">
                <table class="leaderboard-table" id="leaderboardTable">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student</th>
                            <th>Level Achieved</th>
                            <th>Highest Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($leaderboard_result && mysqli_num_rows($leaderboard_result) > 0) {
                            $rank = 1;
                            while ($row = mysqli_fetch_assoc($leaderboard_result)) {
                                $rankClass = ($rank <= 3) ? "rank-$rank" : "";
                                $gender = strtolower($row['gender']);
                                $avatar = ($gender === 'male') ? '../image/male.png' : 
                                         (($gender === 'female') ? '../image/female.png' : '../image/default.png');
                        ?>
                            <tr>
                                <td class="rank <?php echo $rankClass; ?>"><?php echo $rank; ?></td>
                                <td>
                                    <div class="student-info">
                                        <img src="<?php echo $avatar; ?>" alt="Student Avatar">
                                        <span><?php echo htmlspecialchars($row['username']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo ($row['level_achieved'] !== null) ? htmlspecialchars($row['level_achieved']) : 'N/A'; ?></td>
                                <td><?php echo $row['highest_score']; ?></td>
                            </tr>
                        <?php
                                $rank++;
                            }
                        } else {
                            echo '<tr><td colspan="4" style="text-align: center;">No leaderboard data available yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
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

        // Leaderboard search functionality
        document.getElementById('searchBtn').addEventListener('click', function() {
            const searchInput = document.getElementById('leaderboardSearchInput').value.toLowerCase();
            const tableRows = document.querySelectorAll('#leaderboardTable tbody tr');
            
            tableRows.forEach(row => {
                const studentName = row.querySelector('.student-info span');
                if (studentName) {
                    const studentText = studentName.textContent.toLowerCase();
                    if (studentText.includes(searchInput)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });

        // Reset search when input is cleared
        document.getElementById('leaderboardSearchInput').addEventListener('input', function() {
            if (this.value === '') {
                const tableRows = document.querySelectorAll('#leaderboardTable tbody tr');
                tableRows.forEach(row => {
                    row.style.display = '';
                });
            }
        });
    </script>
</body>
</html> 