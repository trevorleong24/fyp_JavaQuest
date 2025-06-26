<?php
include("../conn/conn.php");
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit();
}

// Handle delete requests
if (isset($_GET['action']) && isset($_GET['type']) && $_GET['action'] === 'delete') {
    $type = $_GET['type'];
    $id = $_GET['id'];
    if ($type === 'student') {
        $delete_query = "DELETE FROM STUDENT WHERE student_id = '$id'";
    } elseif ($type === 'question') {
        $delete_query = "DELETE FROM question WHERE question_id = '$id'";
    }
    if (isset($delete_query)) {
        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['message'] = ucfirst($type) . " deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete " . ucfirst($type) . ": " . mysqli_error($conn);
        }
        header('Location: ../admin/admin.php');
        exit();
    }
}

// Fetch students from the database
$students_query = "SELECT * FROM STUDENT";
$students_result = mysqli_query($conn, $students_query);
if (!$students_result) {
    die("Error fetching students: " . mysqli_error($conn));
}

// Fetch questions from the database (check if question table exists first)
$questions_result = false; // Default to false if table doesn't exist
$check_table_query = "SHOW TABLES LIKE 'question'";
$table_check = mysqli_query($conn, $check_table_query);

if ($table_check && mysqli_num_rows($table_check) > 0) {
    // Table exists, proceed with query
    $questions_query = "SELECT q.*, qz.title as difficulty 
                      FROM question q 
                      LEFT JOIN quiz qz ON q.quiz_id = qz.quiz_id 
                      ORDER BY q.question_id";
    $questions_result = mysqli_query($conn, $questions_query);
    if (!$questions_result) {
        // Query failed for some reason
        $questions_result = false;
    }
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

// Fetch overall progress data (safely handling potentially non-existent tables)
$progress_result = false;

// First check if both necessary tables exist
$check_progress_table = "SHOW TABLES LIKE 'gameprogress'";
$progress_table_check = mysqli_query($conn, $check_progress_table);
$check_questions_table = "SHOW TABLES LIKE 'question'";
$questions_table_check = mysqli_query($conn, $check_questions_table);

if ($progress_table_check && mysqli_num_rows($progress_table_check) > 0 && 
    $questions_table_check && mysqli_num_rows($questions_table_check) > 0) {
    // Both tables exist, proceed with the full query
    $progress_query = "SELECT 
                       COUNT(DISTINCT p.student_id) AS active_students,
                       COUNT(CASE WHEN p.lst_played = 100 THEN 1 END) AS completed_challenges,
                       (SELECT ROUND(AVG(score), 1) FROM gameprogress) AS average_score,
                       COUNT(DISTINCT q.question_id) AS total_questions
                       FROM gameprogress p
                       RIGHT JOIN question q ON p.quiz_id = q.quiz_id";


    $progress_result = mysqli_query($conn, $progress_query);
} else {
    // At least one table doesn't exist, create a mock result with zeroes
    // Use a simple query that will work regardless of table existence
    $progress_query = "SELECT 
                      (SELECT COUNT(*) FROM STUDENT) as active_students,
                      0 as completed_challenges,
                      0 as average_score,
                      0 as total_questions";
    $progress_result = mysqli_query($conn, $progress_query);
}

// Retrieve and clear message
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JavaQuest - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

<section class="main-container">
    <div class="dashboard-header">
        <h1>JavaQuest Admin Dashboard</h1>
        <p class="dashboard-subtitle">Manage your JavaQuest platform</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="tab-container">
        <div class="tab active" data-tab="students">Students</div>
        <div class="tab" data-tab="questions">Questions</div>
        <div class="tab" data-tab="leaderboard">Leaderboard</div>
        <div class="tab" data-tab="progress">Progress</div>
    </div>

    <div class="content-container">
        <!-- Students Tab Content -->
        <div class="tab-content active" id="students">
            <div class="search-container">
                <input type="text" id="studentSearchInput" class="search-bar" placeholder="Search students by name, email, or ID...">
                <button class="search-button"><i class="fas fa-search"></i> Search</button>
            </div>
            
            <div class="card-grid">
                <?php 
                if (mysqli_num_rows($students_result) > 0) {
                    while ($student = mysqli_fetch_assoc($students_result)) {
                        $gender = strtolower($student['gender']);
                ?>
                    <div class="card student-card">
                        <div class="gender-avatar">
                            <?php
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
                            <p><strong>ID:</strong> <i class="fas fa-id-card" style="color: #3498db; margin-right: 5px;"></i><?php echo htmlspecialchars($student['student_id']); ?></p>
                            <p><strong>Username:</strong> <i class="fas fa-user" style="color: #3498db; margin-right: 5px;"></i><?php echo htmlspecialchars($student['username']); ?></p>
                            <p><strong>Email:</strong> <i class="fas fa-envelope" style="color: #3498db; margin-right: 5px;"></i><?php echo htmlspecialchars($student['email']); ?></p>
                            <p><strong>Gender:</strong> <i class="fas fa-venus-mars" style="color: #3498db; margin-right: 5px;"></i><?php echo htmlspecialchars($student['gender']); ?></p>
                            <p><strong>DoB:</strong> <i class="fas fa-calendar" style="color: #3498db; margin-right: 5px;"></i><?php echo htmlspecialchars($student['dob']); ?></p>
                        </div>
                        
                        <div class="card-buttons">
                            <a href="admin-edit.php?type=student&id=<?php echo urlencode($student['student_id']); ?>" class="action-btn small edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="admin-delete.php?type=student&id=<?php echo urlencode($student['student_id']); ?>" class="action-btn small delete-btn" onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                            <a href="student-detail.php?id=<?php echo urlencode($student['student_id']); ?>" class="action-btn small view-btn">
                                <i class="fas fa-user"></i> Profile
                            </a>
                        </div>
                    </div>
                <?php 
                    }
                } else {
                    echo "<p style='text-align: center;'>No students found. Add students to get started.</p>";
                }
                ?>
            </div>
        </div>
        
        <!-- Questions Tab Content -->
        <div class="tab-content" id="questions">
            <div class="search-container">
                <input type="text" id="questionSearchInput" class="search-bar" placeholder="Search questions by text or options...">
                <button class="search-button"><i class="fas fa-search"></i> Search</button>
            </div>
            
            <div class="action-buttons" style="margin-bottom: 20px;">
                <a href="java-quiz-add.php" class="action-btn view-btn">
                    <i class="fas fa-plus"></i> Add New Question
                </a>
            </div>
            
            <!-- Questions table that matches java-quiz-view.php -->
            <div class="questions-table-container">
                <table class="questions-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Opt 1</th>
                            <th>Opt 2</th>
                            <th>Opt 3</th>
                            <th>Opt 4</th>
                            <th>Difficulty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all questions with their difficulty title - same as in java-quiz-view.php
                        $sql = "
                            SELECT 
                              q.question_id,
                              q.question_text,
                              q.`Option 1`,
                              q.`Option 2`,
                              q.`Option 3`,
                              q.`Option 4`,
                              quiz.title AS difficulty
                            FROM question AS q
                            LEFT JOIN quiz
                              ON q.quiz_id = quiz.quiz_id
                            ORDER BY q.question_id DESC
                        ";
                        $result = mysqli_query($conn, $sql);
                        
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['question_id']) ?></td>
                                <td class="question-cell" title="<?= htmlspecialchars($row['question_text']) ?>">
                                    <?= htmlspecialchars($row['question_text']) ?>
                                </td>
                                <td class="option-cell" title="<?= htmlspecialchars($row['Option 1']) ?>"><?= htmlspecialchars($row['Option 1']) ?></td>
                                <td class="option-cell" title="<?= htmlspecialchars($row['Option 2']) ?>"><?= htmlspecialchars($row['Option 2']) ?></td>
                                <td class="option-cell" title="<?= htmlspecialchars($row['Option 3']) ?>"><?= htmlspecialchars($row['Option 3']) ?></td>
                                <td class="option-cell" title="<?= htmlspecialchars($row['Option 4']) ?>"><?= htmlspecialchars($row['Option 4']) ?></td>
                                <td><?= htmlspecialchars($row['difficulty']) ?></td>
                                <td>
                                    <div class="action-buttons-container">
                                        <a href="java-quiz-edit.php?id=<?= urlencode($row['question_id']) ?>" class="action-btn small edit-btn">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="java-quiz-delete.php?id=<?= urlencode($row['question_id']) ?>" class="action-btn small delete-btn" onclick="return confirm('Are you sure you want to delete this question?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No questions found. Add questions to get started.</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Leaderboard Tab Content -->
        <div class="tab-content" id="leaderboard">
            <div class="leaderboard-container">
                <table class="leaderboard-table">
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
        
        <!-- Progress Tab Content -->
        <div class="tab-content" id="progress">
            <?php if ($progress_result && $row = mysqli_fetch_assoc($progress_result)): ?>
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value"><?php echo $row['active_students']; ?></div>
                        <div class="stat-label">Active Students</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-value"><?php echo $row['completed_challenges']; ?></div>
                        <div class="stat-label">Completed Games</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($row['average_score'], 1); ?></div>
                        <div class="stat-label">Average Score</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="stat-value"><?php echo $row['total_questions']; ?></div>
                        <div class="stat-label">Total Questions</div>
                    </div>
                </div>
            <?php else: ?>
                <p style="text-align: center;">No progress data available yet.</p>
            <?php endif; ?>
            
            <div class="progress-charts">
                <!-- Student Activity Chart -->
                <div class="chart-container">
                    <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Student Activity</h3>
                    <canvas id="studentActivityChart"></canvas>
                </div>
                
                <!-- Question Distribution Chart -->
                <div class="chart-container">
                    <h3 class="chart-title"><i class="fas fa-chart-pie"></i> Question Distribution</h3>
                    <canvas id="questionDistributionChart"></canvas>
                </div>
            </div>

            <?php
            // Fetch data for average score chart - Query to get student scores
            $avg_scores_query = "SELECT 
                                  s.username,
                                  AVG(gp.score) AS average_score
                                FROM 
                                  STUDENT s
                                  JOIN gameprogress gp ON s.student_id = gp.student_id
                                GROUP BY 
                                  s.username
                                ORDER BY 
                                  average_score DESC
                                LIMIT 10";
            
            $scores_result = mysqli_query($conn, $avg_scores_query);
            $students = [];
            $avg_scores = [];
            
            if ($scores_result && mysqli_num_rows($scores_result) > 0) {
                while ($score_row = mysqli_fetch_assoc($scores_result)) {
                    $students[] = $score_row['username'];
                    $avg_scores[] = $score_row['average_score'];
                }
            } else {
                // Sample data if no real data is available
                $students = ['Student 1', 'Student 2', 'Student 3', 'Student 4', 'Student 5'];
                $avg_scores = [85, 70, 65, 90, 75];
            }
            
            // Fetch data for question distribution chart - Count questions by difficulty
            $question_dist_query = "SELECT 
                                      quiz.title AS difficulty,
                                      COUNT(q.question_id) AS question_count
                                    FROM 
                                      question q
                                      JOIN quiz ON q.quiz_id = quiz.quiz_id
                                    GROUP BY 
                                      q.quiz_id, quiz.title";
            
            $dist_result = mysqli_query($conn, $question_dist_query);
            $difficulties = [];
            $question_counts = [];
            
            if ($dist_result && mysqli_num_rows($dist_result) > 0) {
                while ($dist_row = mysqli_fetch_assoc($dist_result)) {
                    $difficulties[] = $dist_row['difficulty'];
                    $question_counts[] = $dist_row['question_count'];
                }
            } else {
                // Sample data if no real data is available
                $difficulties = ['Easy', 'Normal', 'Hard'];
                $question_counts = [5, 3, 2];
            }
            ?>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Chart colors
                const chartColors = {
                    easy: 'rgba(50, 205, 50, 0.7)', // green for easy
                    normal: 'rgba(255, 215, 0, 0.7)', // gold for normal
                    hard: 'rgba(255, 82, 82, 0.7)', // red for hard
                    border: 'rgba(255, 255, 255, 0.8)',
                    grid: 'rgba(255, 255, 255, 0.1)'
                };
                
                // Font options for both charts
                const fontOptions = {
                    family: "'Roboto', sans-serif",
                    size: 14,
                    weight: 'bold',
                    color: 'rgba(240, 248, 255, 0.8)' // Light color
                };

                // Student Activity Chart - Average Score
                const activityCtx = document.getElementById('studentActivityChart').getContext('2d');
                const activityChart = new Chart(activityCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($students); ?>,
                        datasets: [{
                            label: 'Average Score',
                            data: <?php echo json_encode($avg_scores); ?>,
                            backgroundColor: 'rgba(138, 43, 226, 0.7)', // Purple with transparency
                            borderColor: chartColors.border,
                            borderWidth: 2,
                            borderRadius: 5,
                            hoverBackgroundColor: 'rgba(138, 43, 226, 0.9)',
                            barPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    font: fontOptions,
                                    color: fontOptions.color
                                }
                            },
                            title: {
                                display: true,
                                text: 'Student Average Scores',
                                color: 'rgba(255, 215, 0, 1)', // Gold color
                                font: {
                                    family: "'Press Start 2P', cursive",
                                    size: 16
                                },
                                padding: {
                                    top: 10,
                                    bottom: 20
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(18, 18, 18, 0.9)',
                                titleFont: {
                                    family: fontOptions.family,
                                    size: fontOptions.size
                                },
                                bodyFont: {
                                    family: fontOptions.family,
                                    size: fontOptions.size
                                },
                                padding: 12,
                                caretSize: 8,
                                cornerRadius: 6,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: chartColors.grid
                                },
                                ticks: {
                                    color: fontOptions.color,
                                    font: fontOptions
                                },
                                suggestedMax: 100 // Since scores typically max at 100
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: fontOptions.color,
                                    font: fontOptions
                                }
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeOutQuart'
                        }
                    }
                });

                // Question Distribution Chart
                const difficultyLabels = <?php echo json_encode($difficulties); ?>;
                const backgroundColors = difficultyLabels.map(label => {
                    if (label.toLowerCase() === 'easy') return chartColors.easy;
                    if (label.toLowerCase() === 'normal') return chartColors.normal;
                    if (label.toLowerCase() === 'hard') return chartColors.hard;
                    return 'rgba(138, 43, 226, 0.7)'; // default purple
                });

                const distributionCtx = document.getElementById('questionDistributionChart').getContext('2d');
                const distributionChart = new Chart(distributionCtx, {
                    type: 'pie',
                    data: {
                        labels: difficultyLabels,
                        datasets: [{
                            data: <?php echo json_encode($question_counts); ?>,
                            backgroundColor: backgroundColors,
                            borderColor: chartColors.border,
                            borderWidth: 2,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: fontOptions,
                                    color: fontOptions.color,
                                    padding: 20
                                }
                            },
                            title: {
                                display: true,
                                text: 'Question Distribution by Difficulty',
                                color: 'rgba(255, 215, 0, 1)', // Gold color
                                font: {
                                    family: "'Press Start 2P', cursive",
                                    size: 16
                                },
                                padding: {
                                    top: 10,
                                    bottom: 20
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(18, 18, 18, 0.9)',
                                titleFont: {
                                    family: fontOptions.family,
                                    size: fontOptions.size
                                },
                                bodyFont: {
                                    family: fontOptions.family,
                                    size: fontOptions.size
                                },
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.formattedValue;
                                        const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                        const percentage = Math.round((context.raw / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 2000,
                            easing: 'easeOutQuart'
                        }
                    }
                });

                // Add resize handler for responsiveness
                window.addEventListener('resize', function() {
                    activityChart.resize();
                    distributionChart.resize();
                });
            });
            </script>
        </div>
    </div>
</section>

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

<script src="../js/admin.js"></script>
</body>
</html>