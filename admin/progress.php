<?php
include("../conn/conn.php");
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit();
}

// Fetch progress summary
$progress_result = false;

$check_gp_table = "SHOW TABLES LIKE 'gameprogress'";
$check_q_table  = "SHOW TABLES LIKE 'question'";

$gp_table_exists = mysqli_query($conn, $check_gp_table);
$q_table_exists  = mysqli_query($conn, $check_q_table);

if ($gp_table_exists && mysqli_num_rows($gp_table_exists) > 0 &&
    $q_table_exists && mysqli_num_rows($q_table_exists) > 0) {
    
    $progress_query = "
        SELECT 
            (SELECT COUNT(DISTINCT student_id) FROM gameprogress) AS active_students,
            (SELECT COUNT(*) FROM gameprogress WHERE lst_played = 100) AS completed_challenges,
            (SELECT ROUND(AVG(score), 1) FROM gameprogress) AS average_score,
            (SELECT COUNT(*) FROM question) AS total_questions
    ";
    $progress_result = mysqli_query($conn, $progress_query);
} else {
    $progress_query = "SELECT 
        (SELECT COUNT(*) FROM STUDENT) AS active_students,
        0 AS completed_challenges,
        0 AS average_score,
        0 AS total_questions";
    $progress_result = mysqli_query($conn, $progress_query);
}

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JavaQuest Admin - Progress Tracking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/progress.css">
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

<div class="main-container">
    <div class="dashboard-header">
        <h1>Progress Tracking</h1>
        <div class="dashboard-subtitle">Monitor student engagement and performance in JavaQuest</div>
    </div>

    <div class="content-container">
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
        <?php else: ?>
            <div class="chart-container">
                <div class="chart-title">No Data Available</div>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <h3>No Progress Data Available</h3>
                    <p>There is no progress data available yet. As students complete Java quizzes, their progress will be tracked here.</p>
                </div>
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
            <p>&copy; 2025 JavaQuest. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
    // Toggle hamburger menu
    document.getElementById('menuBtn').addEventListener('click', function () {
        document.getElementById('hamburgerMenu').classList.toggle('show');
    });

    <?php if ($progress_result): ?>
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
    <?php endif; ?>
</script>
</body>
</html>
