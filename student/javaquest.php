<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login/login.php");
    exit();
}

$is_logged_in = isset($_SESSION['username']) && $_SESSION['role'] === 'student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>JavaQuest</title>
  <link rel="stylesheet" href="../css/javaquest.css">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .start-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
    
    #quizDescription {
      font-family: 'Press Start 2P', cursive;
      font-size: 0.8rem;
      line-height: 1.8;
      letter-spacing: 0.05em;
      color: #fff;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .highlight {
      animation: pulse 1.5s infinite;
      border: 2px solid #FFD700;
    }
    
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.7); }
      70% { box-shadow: 0 0 0 10px rgba(255, 215, 0, 0); }
      100% { box-shadow: 0 0 0 0 rgba(255, 215, 0, 0); }
    }
  </style>
</head>
<body <?php if ($is_logged_in) echo 'class="logged-in"'; ?>>
  <div class="page-container">
    <!-- Header Section -->
    <header>
      <div class="logo-nav">
        <a href="student-homepage.php" class="logo">JavaQuest</a>
        <div class="nav-links">
          <a href="../about-us/about-us.php" class="nav-link">About</a>
          <a href="../term-privacy/term-privacy.php" class="nav-link">Terms & Privacy</a>
        </div>
      </div>
      <div class="auth-social">
        <?php if ($is_logged_in): ?>
          <button class="menu-btn" id="menuBtn">
            <i class="fas fa-bars"></i>
          </button>
        <?php else: ?>
          <a href="../login/login.php" class="login-btn">Login</a>
        <?php endif; ?>
      </div>
    </header>

    <!-- Hamburger Menu -->
    <div class="hamburger-menu" id="hamburgerMenu">
      <a href="../student/student-homepage.php" class="menu-item"><i class="fas fa-home"></i>Home</a>
      <a href="leaderboard-student.php" class="menu-item"><i class="fas fa-trophy"></i>Leaderboard</a>
      <a href="progress-student.php" class="menu-item"><i class="fas fa-chart-line"></i>Progress</a>
      <a href="../student/student-profile.php" class="menu-item"><i class="fas fa-user"></i>Profile</a>
      <a href="../logout/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i>Log Out</a>
    </div>

    <!-- Quiz Container -->
    <section class="quiz-container">
      <h1>JavaQuest</h1>

      <div class="quiz-box">
        <div class="quiz-info-box">
          <!-- hard‑coded until user selects -->
          <p id="quizDescription">
            JavaQuest is a gamified learning experience that combines the classic Snakes and Ladders game with Java programming quizzes.
            Each dice roll presents a Java multiple-choice question that players must answer correctly to move forward.
            A correct answer takes you closer to victory, while a wrong one could keep you in place—or worse, send you down a snake!
          </p>

          <div class="game-controls">
            <!-- Level selector -->
            <div class="select-box" id="levelSelector">
              <span class="label">Select Level</span>
              <span class="chevron">▼</span>
              <ul class="options">
                <li data-level="easy">Easy</li>
                <li data-level="normal">Normal</li>
                <li data-level="hard">Hard</li>
              </ul>
            </div>

            <!-- Start button -->
            <button class="cta-button primary" id="startGame" disabled>
              Start Game
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer Section -->
    <footer>
      <div class="footer-container">
        <div class="footer-top">
          <div class="footer-contact">
            <p>support@javaquest.com</p>
            <p>We're here whenever you need help, just send us an email.</p>
          </div>
          <div class="footer-social">
            <a href="#" class="social-icon"><i class="fab fa-discord"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
          </div>
        </div>
        
        <div class="footer-links">
          <div class="footer-section">
            <h3>About Us</h3>
            <ul>
              <li><a href="../about-us/about-us.php">About JavaQuest</a></li>
              <li><a href="../about-us/team.php">Our Team</a></li>
            </ul>
          </div>
          <div class="footer-section">
            <h3>Resources</h3>
            <ul>
              <li><a href="https://www.geeksforgeeks.org/java-cheat-sheet/" target="_blank">Java Cheatsheet</a></li>
              <li><a href="../term-privacy/term-privacy.php">Terms & Privacy</a></li>
            </ul>
          </div>
          <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
              <li><a href="leaderboard-student.php">Leaderboard</a></li>
              <li><a href="progress-student.php">Progress</a></li>
            </ul>
          </div>
        </div>
        
        <div class="footer-copyright">
          <p>Copyright &copy; 2025 JavaQuest. All rights reserved</p>
        </div>
      </div>
    </footer>
  </div>

  <script>
    // Hamburger menu JavaScript
    document.addEventListener('DOMContentLoaded', function() {
      const menuBtn = document.getElementById('menuBtn');
      const hamburgerMenu = document.getElementById('hamburgerMenu');
      
      // Toggle menu when menu button is clicked
      if (menuBtn) {
        menuBtn.addEventListener('click', function(event) {
          event.stopPropagation(); // Prevent event from bubbling to document
          hamburgerMenu.classList.toggle('show');
          
          // Add active class to button
          this.classList.toggle('active');
          
          // Change icon to X when menu is open
          const icon = this.querySelector('i');
          if (hamburgerMenu.classList.contains('show')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
          } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
          }
        });
      }
      
      // Close menu when clicking outside of it
      document.addEventListener('click', function(event) {
        if (hamburgerMenu && hamburgerMenu.classList.contains('show') && 
            !hamburgerMenu.contains(event.target) && 
            menuBtn && !menuBtn.contains(event.target)) {
            
          hamburgerMenu.classList.remove('show');
          
          // Reset icon and button style
          if (menuBtn) {
            const icon = menuBtn.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
            
            // Remove active class from button
            menuBtn.classList.remove('active');
          }
        }
      });
      
      // Add close button to the top of the menu
      if (hamburgerMenu) {
        const closeButton = document.createElement('div');
        closeButton.className = 'menu-close';
        closeButton.innerHTML = '<i class="fas fa-times"></i>';
        
        hamburgerMenu.prepend(closeButton);
        
        closeButton.addEventListener('click', function() {
          hamburgerMenu.classList.remove('show');
          if (menuBtn) {
            menuBtn.classList.remove('active');
            const icon = menuBtn.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
          }
        });
      }
      
      // Level selector functionality
      const levelSelector = document.getElementById('levelSelector');
      const startBtn = document.getElementById('startGame');
      let selectedQuizId = '';
      let selectedLevel = '';

      // Map quiz IDs to their levels
      const quizIdToLevel = {
        'QZ01': 'easy',
        'QZ02': 'normal',
        'QZ03': 'hard'
      };

      // Map levels to quiz IDs
      const levelToQuizId = {
        'easy': 'QZ01',
        'normal': 'QZ02',
        'hard': 'QZ03'
      };

      // Check if there's a completed quiz parameter
      const urlParams = new URLSearchParams(window.location.search);
      const completedQuiz = urlParams.get('completed_quiz');
      
      if (completedQuiz) {
        // User has completed a specific quiz
        document.getElementById('quizDescription').textContent = 
          `You've completed the ${quizIdToLevel[completedQuiz] || 'previous'} level! Please select a difficulty level to play.`;
        levelSelector.querySelector('.label').textContent = 'Select Difficulty';
        levelSelector.classList.add('highlight');
        // Store the completed quiz ID with quiz-specific key
        localStorage.setItem(`completed_quiz_${completedQuiz}`, 'true');
      }
      // If no completed quiz but new_game parameter is present
      else if (urlParams.get('new_game') === '1') {
        document.getElementById('quizDescription').textContent = 
          'You have completed your previous game! Please select a difficulty level to start a new game.';
        levelSelector.querySelector('.label').textContent = 'Select Difficulty';
        levelSelector.classList.add('highlight');
      }
      // Check if URL parameter contains new=1, this is used when directly starting a new game
      else if (urlParams.get('new') === '1') {
        document.getElementById('quizDescription').textContent = 
          'Please select a difficulty level to start a new game.';
        levelSelector.querySelector('.label').textContent = 'Select Difficulty';
        levelSelector.classList.add('highlight');
      }

      // toggle dropdown on selector click
      levelSelector.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('open');
        
        // Toggle a class on game-controls to create space when dropdown is open
        const gameControls = document.querySelector('.game-controls');
        if (this.classList.contains('open')) {
          gameControls.classList.add('dropdown-open');
        } else {
          gameControls.classList.remove('dropdown-open');
        }
      });

      // close dropdown if clicking outside
      document.addEventListener('click', () => {
        levelSelector.classList.remove('open');
        // Also remove the dropdown-open class when clicking outside
        document.querySelector('.game-controls').classList.remove('dropdown-open');
      });

      // handle selecting a level
      document.querySelectorAll('#levelSelector .options li')
        .forEach(li => li.addEventListener('click', function(e) {
          e.stopPropagation();               // prevent re-opening
          const level = this.dataset.level;  // "easy"|"normal"|"hard"
          selectedLevel = level;
          selectedQuizId = levelToQuizId[level]; // Set quiz ID based on level

          // update visible label
          levelSelector.querySelector('.label')
                      .textContent = this.textContent;

          // collapse dropdown
          levelSelector.classList.remove('open');
          
          // Remove the dropdown-open class immediately after selection
          document.querySelector('.game-controls').classList.remove('dropdown-open');

          // enable Start button
          startBtn.disabled = false;

          // fetch and swap in real description & quiz_id
          fetch(`get-level-description.php?level=${level}`)
            .then(res => res.json())
            .then(data => {
              if (data.description) {
                document.getElementById('quizDescription')
                        .textContent = data.description;
                selectedQuizId = data.quiz_id;
              } else {
                console.error(data.error);
              }
            })
            .catch(console.error);
        }));

      // start quiz redirect based on selectedLevel
      startBtn.addEventListener('click', () => {
        if (!selectedLevel || !selectedQuizId) {
          alert('Please select a level first.');
          return;
        }

        let targetPage;
        switch (selectedLevel) {
          case 'easy':
            targetPage = 'easygame.php';
            break;
          case 'normal':
            targetPage = 'normalgame.php';
            break;
          case 'hard':
            targetPage = 'hardgame.php';
            break;
          default:
            targetPage = 'index.php';
        }

        // Check if this specific quiz was completed
        const wasCompleted = localStorage.getItem(`completed_quiz_${selectedQuizId}`) === 'true';
        
        // Add new=1 if this specific quiz was completed or if explicitly requested
        const shouldBeNewGame = wasCompleted || urlParams.get('new') === '1';
        
        // Clear any existing game session before starting
        fetch("clear-game-session.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ 
            quiz_id: selectedQuizId,
            force_new: shouldBeNewGame ? '1' : '0'  // Add force_new parameter
          }).toString()
        }).then(() => {
          // Also clear the session progress ID to force new entry
          if (shouldBeNewGame) {
            // Clear the session storage for this quiz's progress
            sessionStorage.removeItem(`current_game_progress_id_${selectedQuizId}`);
          }
          // Redirect to the game after clearing session
          window.location.href = `${targetPage}?quiz_id=${selectedQuizId}${shouldBeNewGame ? '&new=1&force_new=1' : ''}`;
        }).catch(err => {
          console.error("Error clearing game session:", err);
          // Redirect anyway even if clearing fails
          window.location.href = `${targetPage}?quiz_id=${selectedQuizId}${shouldBeNewGame ? '&new=1&force_new=1' : ''}`;
        });
      });
      
      // Add floating pixel art elements for visual appeal
      const quizContainer = document.querySelector('.quiz-container');
      if (quizContainer) {
        for (let i = 0; i < 5; i++) {
          const pixelElement = document.createElement('div');
          pixelElement.style.position = 'absolute';
          pixelElement.style.width = Math.floor(Math.random() * 15 + 5) + 'px';
          pixelElement.style.height = pixelElement.style.width;
          pixelElement.style.backgroundColor = i % 2 === 0 ? '#8A2BE2' : '#FFD700';
          pixelElement.style.opacity = '0.2';
          pixelElement.style.top = Math.floor(Math.random() * 80 + 10) + '%';
          pixelElement.style.left = Math.floor(Math.random() * 80 + 10) + '%';
          pixelElement.style.zIndex = '0';
          pixelElement.style.animation = `float ${Math.floor(Math.random() * 5 + 5)}s ease-in-out infinite`;
          pixelElement.style.animationDelay = `${Math.random() * 2}s`;
          quizContainer.appendChild(pixelElement);
        }
      }
    });
  </script>
</body>
</html>
