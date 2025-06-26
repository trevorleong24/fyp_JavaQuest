<?php
// java-quiz-add.php

session_start();
// Redirect if not logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

require_once("../conn/conn.php");

// Check if quiz table exists and has records
$checkQuiz = $conn->query("SELECT * FROM quiz LIMIT 1");
if (!$checkQuiz || $checkQuiz->num_rows === 0) {
    $_SESSION['message'] = "Quiz table setup required. Redirecting to setup page.";
    header("Location: setup_quiz_table.php");
    exit();
}

// Generate next sequential ID helper
function getNextId($conn, $table, $column, $prefix) {
    $sql = "SELECT $column FROM $table WHERE $column LIKE '$prefix%' ORDER BY $column ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $existing = [];
        while ($row = $result->fetch_assoc()) {
            $existing[] = (int)substr($row[$column], strlen($prefix));
        }
        $n = 1;
        while (in_array($n, $existing)) $n++;
        return $prefix . str_pad($n, 2, '0', STR_PAD_LEFT);
    } else {
        return $prefix . "01";
    }
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question   = trim($_POST['question']);
    $option1    = trim($_POST['option1']);
    $option2    = trim($_POST['option2']);
    $option3    = trim($_POST['option3']);
    $option4    = trim($_POST['option4']);
    $answer     = trim($_POST['answer']);
    $quiz_id    = trim($_POST['difficulty']); // now holds "QZ01" / "QZ02" / "QZ03"

    if ($question === '' || $option1 === '' || $option2 === '' ||
        $option3 === ''  || $option4 === '' || $answer === '' ||
        $quiz_id === '') {
        $message = "All fields are required!";
    } else {
        // Verify quiz_id exists in the quiz table
        $verifyQuiz = $conn->prepare("SELECT quiz_id FROM quiz WHERE quiz_id = ?");
        $verifyQuiz->bind_param("s", $quiz_id);
        $verifyQuiz->execute();
        $verifyResult = $verifyQuiz->get_result();
        
        if ($verifyResult->num_rows === 0) {
            $message = "Error: Selected difficulty level (quiz_id: $quiz_id) doesn't exist in the database. Please run the setup_quiz_table.php file first.";
        } else {
            $question_id = getNextId($conn, 'question', 'question_id', 'Q');
            $answer_id   = getNextId($conn, 'answer',   'answer_id',   'A');

            // Insert into question
            $stmt_q = $conn->prepare(
              "INSERT INTO question 
                (question_id, question_text, `Option 1`, `Option 2`, `Option 3`, `Option 4`, quiz_id)
               VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt_q->bind_param(
                "sssssss",
                $question_id, $question,
                $option1, $option2, $option3, $option4,
                $quiz_id
            );

            try {
                if ($stmt_q->execute()) {
                    // Insert into answer
                    $stmt_a = $conn->prepare(
                      "INSERT INTO answer (answer_id, answer_text, question_id)
                       VALUES (?, ?, ?)"
                    );
                    $stmt_a->bind_param("sss", $answer_id, $answer, $question_id);
                    if ($stmt_a->execute()) {
                        $message = "Question and answer added successfully!";
                        $_SESSION['message'] = $message;
                        header("Location: ../admin/questions.php");
                        exit();
                    } else {
                        $message = "Error adding answer: " . $stmt_a->error;
                    }
                    $stmt_a->close();
                } else {
                    $message = "Error adding question: " . $stmt_q->error;
                }
            } catch (mysqli_sql_exception $e) {
                if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                    $message = "Database error: The selected difficulty level is not valid. Please run setup_quiz_table.php to fix this issue.";
                } else {
                    $message = "Database error: " . $e->getMessage();
                }
            }
            $stmt_q->close();
        }
    }

    $_SESSION['message'] = $message;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Java Quiz Question</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/java-quiz-add.css">
</head>
<body>
<header>
  <div class="logo-nav">
    <a href="admin.php" class="logo">JavaQuest Admin</a>
  </div>
  <div class="auth-container">
    <button class="menu-btn" id="menuBtn">
      <i class="fas fa-bars"></i>
    </button>
  </div>
  <div class="hamburger-menu" id="hamburgerMenu">
    <a href="../admin/admin.php" class="menu-item"><i class="fas fa-home"></i>Home</a>
    <a href="../admin/student-profile.php" class="menu-item"><i class="fas fa-user-graduate"></i>Students</a>
    <a href="../admin/questions.php" class="menu-item"><i class="fas fa-question-circle"></i>Questions</a>
    <a href="../admin/leaderboard.php" class="menu-item"><i class="fas fa-trophy"></i>Leaderboard</a>
    <a href="../admin/progress.php" class="menu-item"><i class="fas fa-chart-line"></i>Progress</a>
    <a href="../logout/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i>Log Out</a>
  </div>
</header>

<main>
  <section class="form-container">
    <h1>Add Java Quiz Question</h1>
    <?php if (!empty($message)): ?>
      <script>alert("<?= addslashes($message) ?>");</script>
    <?php endif; ?>

    <form method="POST">
      <label for="question">Question:</label>
      <textarea id="question" name="question" required></textarea>

      <label for="option1">Option 1:</label>
      <input type="text" id="option1" name="option1" required>

      <label for="option2">Option 2:</label>
      <input type="text" id="option2" name="option2" required>

      <label for="option3">Option 3:</label>
      <input type="text" id="option3" name="option3" required>

      <label for="option4">Option 4:</label>
      <input type="text" id="option4" name="option4" required>

      <label for="answer">Correct Answer:</label>
      <input type="text" id="answer" name="answer" required>

      <label for="difficulty">Difficulty Level:</label>
      <select id="difficulty" name="difficulty" required>
        <option value="">-- Select Difficulty --</option>
        <!-- VALUE = actual quiz_id in quiz table -->
        <option value="QZ01">Easy</option>
        <option value="QZ02">Normal</option>
        <option value="QZ03">Hard</option>
      </select>

      <div class="form-buttons">
        <button type="submit" id="addQuestion">Add Question</button>
        <button type="reset">Reset</button>
      </div>
    </form>

    <button class="back-button" onclick="location.href='questions.php'">
      &larr; Back
    </button>
  </section>
</main>

<footer>
  <div class="footer-container">
    <div class="footer-section">
      <h3>About Us</h3>
      <ul><li><a href="../about-us/about-us.php">About JavaQuest</a></li></ul>
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
</body>
</html>