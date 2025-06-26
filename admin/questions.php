<?php
include("../conn/conn.php");
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit();
}

// Fetch questions from the database
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
    <title>JavaQuest Admin - Question Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin-new.css">
    <style>
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
        }

        .page-subtitle {
            font-size: 1rem;
            color: var(--light-color);
            opacity: 0.8;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            border-radius: 8px;
            overflow: hidden;
        }

        .data-table th, .data-table td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .data-table th {
            background-color: var(--table-header);
            color: var(--accent-color);
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .data-table tr:hover {
            background-color: var(--table-row-hover);
        }

        .add-new-btn {
            background-color: var(--secondary-color);
            color: var(--light-color);
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            text-decoration: none;
            transition: all 0.3s;
        }

        .add-new-btn:hover {
            background-color: var(--accent-color);
            color: var(--dark-color);
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background-color: rgba(76, 175, 80, 0.2);
            color: var(--success-color);
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .alert-danger {
            background-color: rgba(255, 82, 82, 0.2);
            color: var(--danger-color);
            border: 1px solid rgba(255, 82, 82, 0.3);
        }

        .alert-close {
            background: none;
            border: none;
            color: inherit;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0.7;
        }

        .alert-close:hover {
            opacity: 1;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--light-color);
            opacity: 0.7;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--accent-color);
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--accent-color);
        }
    </style>
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
        <div class="page-header">
            <h1>Question Management</h1>
            <div class="page-subtitle">View, add, edit, and delete Java quiz questions</div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo strpos($message, 'success') !== false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $message; ?>
                <button class="alert-close">&times;</button>
            </div>
        <?php endif; ?>

        <div class="content-container">
            <a href="../admin/java-quiz-add.php" class="add-new-btn">
                <i class="fas fa-plus"></i> Add New Question
            </a>

            <div class="search-container">
                <input type="text" id="searchInput" class="search-bar" placeholder="Search questions...">
                <button class="search-button" id="searchBtn">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>

            <?php if ($questions_result && mysqli_num_rows($questions_result) > 0): ?>
                <table class="data-table" id="questionsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Category</th>
                            <th>Difficulty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($questions_result)): ?>
                            <tr>
                                <td><?php echo $row['question_id']; ?></td>
                                <td><?php echo htmlspecialchars(substr($row['question_text'], 0, 50)) . (strlen($row['question_text']) > 50 ? '...' : ''); ?></td>
                                <td><?php echo isset($row['category']) ? htmlspecialchars($row['category']) : 'Java'; ?></td>
                                <td><?php echo isset($row['difficulty']) ? htmlspecialchars($row['difficulty']) : $row['quiz_id']; ?></td>
                                <td>
                                    <a href="../admin/java-quiz-edit.php?id=<?php echo $row['question_id']; ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="../admin/java-quiz-delete.php?id=<?php echo $row['question_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this question?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-question-circle"></i>
                    <h3>No Questions Found</h3>
                    <p>There are no questions in the database. Start by adding your first Java quiz question.</p>
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

        // Close alert message
        document.querySelector('.alert-close')?.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });

        // Search functionality
        document.getElementById('searchBtn').addEventListener('click', function() {
            searchQuestions();
        });
        
        document.getElementById('searchInput').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchQuestions();
            }
        });

        function searchQuestions() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const table = document.getElementById('questionsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                let found = false;
                const cells = rows[i].getElementsByTagName('td');
                
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(input) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html> 