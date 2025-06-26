<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

require_once("../conn/conn.php");

if (!isset($_GET['id'])) {
    header("Location: ../admin/questions.php");
    exit();
}

$question_id = $_GET['id'];
$query = "SELECT * FROM question WHERE question_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $question_id);
$stmt->execute();
$result = $stmt->get_result();
$questionData = $result->fetch_assoc();

if (!$questionData) {
    echo "Question not found!";
    exit();
}

// Get correct answer for this question
$answer_query = "SELECT * FROM answer WHERE question_id = ?";
$answer_stmt = $conn->prepare($answer_query);
$answer_stmt->bind_param("s", $question_id);
$answer_stmt->execute();
$answer_result = $answer_stmt->get_result();
$answerData = $answer_result->fetch_assoc();

$message = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $question = trim($_POST["question"]);
    $option1 = trim($_POST["option1"]);
    $option2 = trim($_POST["option2"]);
    $option3 = trim($_POST["option3"]);
    $option4 = trim($_POST["option4"]);
    $answer = trim($_POST["answer"]);
    $quiz_id = trim($_POST["difficulty"]);

    $updateQuery = "UPDATE question SET question_text = ?, `Option 1` = ?, `Option 2` = ?, `Option 3` = ?, `Option 4` = ?, quiz_id = ? WHERE question_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssssss", $question, $option1, $option2, $option3, $option4, $quiz_id, $question_id);

    if ($updateStmt->execute()) {
        // Update answer if it exists
        if ($answerData) {
            $update_answer_query = "UPDATE answer SET answer_text = ? WHERE question_id = ?";
            $update_answer_stmt = $conn->prepare($update_answer_query);
            $update_answer_stmt->bind_param("ss", $answer, $question_id);
            $update_answer_stmt->execute();
        }
        
        $_SESSION['message'] = "Question updated successfully!";
        header("Location: ../admin/questions.php");
        exit();
    } else {
        $message = "Update failed: " . $updateStmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Java Quiz Question</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/java-quiz-edit.css">
</head>
<body>

<header>
    <div class="logo-nav">
        <a href="../admin/admin.php" class="logo">JavaQuest Admin</a>
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
    <h1>Edit Java Quiz Question</h1>
    <?php if (!empty($message)): ?>
      <div class="error-message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="question">Question:</label>
      <textarea id="question" name="question" required><?php echo htmlspecialchars($questionData['question_text']); ?></textarea>

      <label for="option1">Option 1:</label>
      <input type="text" id="option1" name="option1" value="<?php echo htmlspecialchars($questionData['Option 1']); ?>" required>

      <label for="option2">Option 2:</label>
      <input type="text" id="option2" name="option2" value="<?php echo htmlspecialchars($questionData['Option 2']); ?>" required>

      <label for="option3">Option 3:</label>
      <input type="text" id="option3" name="option3" value="<?php echo htmlspecialchars($questionData['Option 3']); ?>" required>

      <label for="option4">Option 4:</label>
      <input type="text" id="option4" name="option4" value="<?php echo htmlspecialchars($questionData['Option 4']); ?>" required>

      <label for="answer">Correct Answer:</label>
      <input type="text" id="answer" name="answer" value="<?php echo $answerData ? htmlspecialchars($answerData['answer_text']) : ''; ?>" required>

      <label for="difficulty">Difficulty Level:</label>
      <select id="difficulty" name="difficulty" required>
        <option value="">-- Select Difficulty --</option>
        <option value="QZ01" <?php echo ($questionData['quiz_id'] === 'QZ01') ? 'selected' : ''; ?>>Easy</option>
        <option value="QZ02" <?php echo ($questionData['quiz_id'] === 'QZ02') ? 'selected' : ''; ?>>Normal</option>
        <option value="QZ03" <?php echo ($questionData['quiz_id'] === 'QZ03') ? 'selected' : ''; ?>>Hard</option>
      </select>

      <div class="form-buttons">
        <button type="submit">Update Question</button>
        <button type="reset">Reset</button>
      </div>
    </form>

    <button class="back-button" onclick="location.href='admin.php'">
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