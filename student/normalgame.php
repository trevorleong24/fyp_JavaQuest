<?php
session_start();
if (!isset($_SESSION["student_id"])) {
  header("Location: ../login/login.php");
  exit();
}
require_once("../conn/conn.php");

// Fetch Top 5 players for leaderboard
$sql = "
  SELECT g.student_id,
         s.username,
         MAX(g.score) AS high_score
    FROM gameprogress AS g
    JOIN student     AS s USING (student_id)
   WHERE g.quiz_id = 'QZ02'  -- Specific to normal game
   GROUP BY g.student_id, s.username
   ORDER BY high_score DESC
   LIMIT 5
";
$result = $conn->query($sql);
$topScores = [];
while ($row = $result->fetch_assoc()) {
  $topScores[] = $row;
}

// Fetch the logged‑in student's personal best
$userStmt = $conn->prepare("
  SELECT MAX(score) AS my_best
    FROM gameprogress
   WHERE student_id = ?
     AND quiz_id = 'QZ02'  -- Specific to normal game
");
$userStmt->bind_param("s", $_SESSION["student_id"]);
$userStmt->execute();
$userStmt->bind_result($myBestScore);
$userStmt->fetch();
$userStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JavaQuest – Snakes &amp; Ladders</title>
  <link rel="stylesheet" href="../css/game.css" />
</head>
<body>
<video class="video-background" autoplay muted loop playsinline>
    <source src="../video/background home.mp4" type="video/mp4">
  </video>

  <!-- Game Title -->
  <h1><a href="../student/student-homepage.php" id="javaQuestTitle" style="text-decoration: none; color: #FFD700; font-family: 'Press Start 2P', cursive; font-size: 28.8px; margin: 15px 0px;">JavaQuest</a></h1>

  <!-- Lessons Panel (scrollable) -->
  <div class="lessons-panel" style="max-height: 400px; overflow-y: auto;">
    <h2>Lessons &amp; Tips</h2>

    <!-- Chapter 1 -->
    <details class="chapter">
      <summary>Chapter 1 – Introduction to Java</summary>
      <div class="chapter-content">
        <p><strong>Key Concepts:</strong> Java is a high-level, object-oriented, platform-independent language. Code runs on the Java Virtual Machine (JVM) using bytecode.</p>
        <p><strong>Tips:</strong> Learn about JVM, API, Applet vs Application, and the concept of "Write once, run anywhere."</p>
      </div>
    </details>

    <!-- Chapter 2 -->
    <details class="chapter">
      <summary>Chapter 2 – Data Types &amp; Operators</summary>
      <div class="chapter-content">
        <p><strong>Key Concepts:</strong> Java has primitive types (int, double, boolean, etc.) and reference types (arrays, objects). Use operators like +, -, ==, &&, etc. to build expressions.</p>
        <p><strong>Tips:</strong> Practice writing expressions and understand operator precedence and how expressions evaluate.</p>
      </div>
    </details>

    <!-- Chapter 3 -->
    <details class="chapter">
      <summary>Chapter 3 – Conditional Constructs</summary>
      <div class="chapter-content">
        <p><strong>Key Concepts:</strong> Use if, if-else, else-if, and switch-case for decision-making. Good for grading, menus, or input validation.</p>
        <p><strong>Tips:</strong> Always use break in switch-case to prevent fall-through. Match each else with its corresponding if.</p>
      </div>
    </details>

    <!-- Chapter 4 -->
    <details class="chapter">
      <summary>Chapter 4 – Loops</summary>
      <div class="chapter-content">
        <p><strong>Key Concepts:</strong> Use while, do-while, and for loops to repeat code. for is best when you know the number of repetitions.</p>
        <p><strong>Tips:</strong> Avoid infinite loops. Use for-loops for arrays, while for unknown repetitions, and do-while when you want the loop to run at least once.</p>
      </div>
    </details>

    <!-- Chapter 5 -->
    <details class="chapter">
      <summary>Chapter 5 – Arrays</summary>
      <div class="chapter-content">
        <p><strong>Key Concepts:</strong> Arrays store multiple values of the same type. Use indices to access elements. Arrays start at index 0.</p>
        <p><strong>Tips:</strong> Use .length to avoid errors. Arrays work great with loops. Know basic array operations like sorting and searching.</p>
      </div>
    </details>

    <!-- Chapter 6 -->
    <details class="chapter">
      <summary>Chapter 6 – Classes &amp; Objects</summary>
      <div class="chapter-content">
        <p><strong>Key Concepts:</strong> A class is a blueprint; an object is an instance. OOP principles include Encapsulation, Information Hiding, and Modularity.</p>
        <p><strong>Tips:</strong> Learn the difference between instance and static members. Practice defining simple classes and creating objects with methods and variables.</p>
      </div>
    </details>
  </div>

  <!-- Quiz Popup -->
  <div class="overlay" id="popupOverlay" style="display: none;">
    <div class="popup">
      <h2>Answer This!</h2>
      <p id="questionText">Loading question...</p>
      <form id="optionsContainer"></form>
      <button onclick="submitChoice()" type="button">Submit</button>
    </div>
  </div>

  <!-- Game Board -->
  <div class="cont">
    <img src="../image/normal1.png" alt="Game Background" class="board-bg" />

    <!-- Row 10 -->
    <div class="box" id="b100"><div class="tokens"></div></div>
    <div class="box" id="b99"><div class="tokens"></div></div>
    <div class="box" id="b98"><div class="tokens"></div></div>
    <div class="box" id="b97"><div class="tokens"></div></div>
    <div class="box" id="b96"><div class="tokens"></div></div>
    <div class="box" id="b95"><div class="tokens"></div></div>
    <div class="box" id="b94"><div class="tokens"></div></div>
    <div class="box" id="b93"><div class="tokens"></div></div>
    <div class="box" id="b92"><div class="tokens"></div></div>
    <div class="box" id="b91"><div class="tokens"></div></div>

    <!-- Row 9 -->
    <div class="box" id="b81"><div class="tokens"></div></div>
    <div class="box" id="b82"><div class="tokens"></div></div>
    <div class="box" id="b83"><div class="tokens"></div></div>
    <div class="box" id="b84"><div class="tokens"></div></div>
    <div class="box" id="b85"><div class="tokens"></div></div>
    <div class="box" id="b86"><div class="tokens"></div></div>
    <div class="box" id="b87"><div class="tokens"></div></div>
    <div class="box" id="b88"><div class="tokens"></div></div>
    <div class="box" id="b89"><div class="tokens"></div></div>
    <div class="box" id="b90"><div class="tokens"></div></div>

    <!-- Row 8 -->
    <div class="box" id="b80"><div class="tokens"></div></div>
    <div class="box" id="b79"><div class="tokens"></div></div>
    <div class="box" id="b78"><div class="tokens"></div></div>
    <div class="box" id="b77"><div class="tokens"></div></div>
    <div class="box" id="b76"><div class="tokens"></div></div>
    <div class="box" id="b75"><div class="tokens"></div></div>
    <div class="box" id="b74"><div class="tokens"></div></div>
    <div class="box" id="b73"><div class="tokens"></div></div>
    <div class="box" id="b72"><div class="tokens"></div></div>
    <div class="box" id="b71"><div class="tokens"></div></div>

    <!-- Row 7 -->
    <div class="box" id="b61"><div class="tokens"></div></div>
    <div class="box" id="b62"><div class="tokens"></div></div>
    <div class="box" id="b63"><div class="tokens"></div></div>
    <div class="box" id="b64"><div class="tokens"></div></div>
    <div class="box" id="b65"><div class="tokens"></div></div>
    <div class="box" id="b66"><div class="tokens"></div></div>
    <div class="box" id="b67"><div class="tokens"></div></div>
    <div class="box" id="b68"><div class="tokens"></div></div>
    <div class="box" id="b69"><div class="tokens"></div></div>
    <div class="box" id="b70"><div class="tokens"></div></div>

    <!-- Row 6 -->
    <div class="box" id="b60"><div class="tokens"></div></div>
    <div class="box" id="b59"><div class="tokens"></div></div>
    <div class="box" id="b58"><div class="tokens"></div></div>
    <div class="box" id="b57"><div class="tokens"></div></div>
    <div class="box" id="b56"><div class="tokens"></div></div>
    <div class="box" id="b55"><div class="tokens"></div></div>
    <div class="box" id="b54"><div class="tokens"></div></div>
    <div class="box" id="b53"><div class="tokens"></div></div>
    <div class="box" id="b52"><div class="tokens"></div></div>
    <div class="box" id="b51"><div class="tokens"></div></div>

    <!-- Row 5 -->
    <div class="box" id="b41"><div class="tokens"></div></div>
    <div class="box" id="b42"><div class="tokens"></div></div>
    <div class="box" id="b43"><div class="tokens"></div></div>
    <div class="box" id="b44"><div class="tokens"></div></div>
    <div class="box" id="b45"><div class="tokens"></div></div>
    <div class="box" id="b46"><div class="tokens"></div></div>
    <div class="box" id="b47"><div class="tokens"></div></div>
    <div class="box" id="b48"><div class="tokens"></div></div>
    <div class="box" id="b49"><div class="tokens"></div></div>
    <div class="box" id="b50"><div class="tokens"></div></div>

    <!-- Row 4 -->
    <div class="box" id="b40"><div class="tokens"></div></div>
    <div class="box" id="b39"><div class="tokens"></div></div>
    <div class="box" id="b38"><div class="tokens"></div></div>
    <div class="box" id="b37"><div class="tokens"></div></div>
    <div class="box" id="b36"><div class="tokens"></div></div>
    <div class="box" id="b35"><div class="tokens"></div></div>
    <div class="box" id="b34"><div class="tokens"></div></div>
    <div class="box" id="b33"><div class="tokens"></div></div>
    <div class="box" id="b32"><div class="tokens"></div></div>
    <div class="box" id="b31"><div class="tokens"></div></div>

    <!-- Row 3 -->
    <div class="box" id="b21"><div class="tokens"></div></div>
    <div class="box" id="b22"><div class="tokens"></div></div>
    <div class="box" id="b23"><div class="tokens"></div></div>
    <div class="box" id="b24"><div class="tokens"></div></div>
    <div class="box" id="b25"><div class="??
tokens"></div></div>
    <div class="box" id="b26"><div class="tokens"></div></div>
    <div class="box" id="b27"><div class="tokens"></div></div>
    <div class="box" id="b28"><div class="tokens"></div></div>
    <div class="box" id="b29"><div class="tokens"></div></div>
    <div class="box" id="b30"><div class="tokens"></div></div>

    <!-- Row 2 -->
    <div class="box" id="b20"><div class="tokens"></div></div>
    <div class="box" id="b19"><div class="tokens"></div></div>
    <div class="box" id="b18"><div class="tokens"></div></div>
    <div class="box" id="b17"><div class="tokens"></div></div>
    <div class="box" id="b16"><div class="tokens"></div></div>
    <div class="box" id="b15"><div class="tokens"></div></div>
    <div class="box" id="b14"><div class="tokens"></div></div>
    <div class="box" id="b13"><div class="tokens"></div></div>
    <div class="box" id="b12"><div class="tokens"></div></div>
    <div class="box" id="b11"><div class="tokens"></div></div>

    <!-- Row 1 -->
    <div class="box" id="b01"><div class="tokens"><div id="p1"></div></div></div>
    <div class="box" id="b02"><div class="tokens"></div></div>
    <div class="box" id="b03"><div class="tokens"></div></div>
    <div class="box" id="b04"><div class="tokens"></div></div>
    <div class="box" id="b05"><div class="tokens"></div></div>
    <div class="box" id="b06"><div class="tokens"></div></div>
    <div class="box" id="b07"><div class="tokens"></div></div>
    <div class="box" id="b08"><div class="tokens"></div></div>
    <div class="box" id="b09"><div class="tokens"></div></div>
    <div class="box" id="b10"><div class="tokens"></div></div>
  </div>

  <!-- Sidebar Panel -->
  <div class="sidebar-panel">

    <!-- Current Score & Level -->
    <div id="scoreDisplay" class="score-box">
      Score: 0 | Level: Beginner
    </div>

    <!-- Your Personal Best -->
    <div class="score-box">
      <p class="title">Your Best</p>
      <?php if ($myBestScore !== null): ?>
        <p><?= htmlspecialchars($myBestScore) ?> points</p>
      <?php else: ?>
        <p>No record yet</p>
      <?php endif; ?>
    </div>

    <!-- Top 5 Players -->
    <div class="scoreboard">
      <p class="title">Top 5 Players</p>
      <?php if (empty($topScores)): ?>
        <p>No scores yet</p>
      <?php else: ?>
        <?php $rank = 1; ?>
        <?php foreach ($topScores as $row): ?>
          <p><?= $rank ?>. <?= htmlspecialchars($row['username']) ?>: <?= htmlspecialchars($row['high_score']) ?> points</p>
          <?php $rank++; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Dice Area (bottom) -->
    <div id="diceCont">
      <p id="tog">Your Turn :</p>
      <p id="dice">0</p>
      <button id="diceBtn">ROLL</button>
    </div>

  </div>

  <!-- End‑of‑Game Popup -->
  <div class="overlay" id="endGameOverlay" style="display: none; align-items: center; justify-content: center;">
    <div class="popup">
      <h2>🎉 You Won! 🎉</h2>
      <p>What would you like to do next?</p>
      <div class="end-buttons" style="display: flex; gap: 10px; margin-top: 1rem;">
        <button id="goHomeBtn" style="flex: 1; padding: 10px; background: #3b82f6; color: #fff; border: none; border-radius: 6px;">
          Home
        </button>
        <button id="newGameBtn" style="flex: 1; padding: 10px; background: #10b981; color: #fff; border: none; border-radius: 6px;">
          New Game
        </button>
      </div>
    </div>
  </div>

  <!-- Pass the quiz ID into your JS -->
  <script>
    // Get quiz_id from URL parameters, if not present use default value QZ02
    const urlParams = new URLSearchParams(window.location.search);
    const QUIZ_ID = urlParams.get('quiz_id') || 'QZ02';
    console.log("Initialized QUIZ_ID:", QUIZ_ID);
  </script>
  <script src="../js/normalgame.js"></script>
  
  <!-- Add script for JavaQuest title link -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Add event listener to the JavaQuest title link - always use new_game parameter
      document.getElementById('javaQuestTitle').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = "../student/student-homepage.php?new_game=1";
      });
    });
  </script>
</body>
</html>
