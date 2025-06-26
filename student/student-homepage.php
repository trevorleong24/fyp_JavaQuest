<?php
session_start(); // Start the session
$is_logged_in = isset($_SESSION['username']) && $_SESSION['role'] === 'student';

if (!$is_logged_in) {
    header("Location: ../login/login.php"); // Redirect to login if not logged in as student
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaQuest - Learn Java Through Snake & Ladder</title>
    <link rel="stylesheet" href="../css/student-homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body <?php if ($is_logged_in) echo 'class="logged-in"'; ?>>
<header>
    <div class="logo-nav">
        <a href="../student/student-homepage.php" class="logo">JavaQuest</a>
        <div class="nav-links">
            <a href="../about-us/about-us.php" class="nav-link">About</a>
            <a href="../term-privacy/term-privacy.php" class="nav-link">Terms & Privacy</a>
        </div>
    </div>
    <div class="auth-social">
        <?php if ($is_logged_in): // Use the variable checked earlier ?>
            <button class="menu-btn" id="menuBtn">
                <i class="fas fa-bars"></i>
            </button>
        <?php else: ?>
            <a href="../login/login.php" class="login-btn">Login</a>
        <?php endif; ?>
    </div>
</header>

<!-- Hamburger Menu with improved implementation -->
<div class="hamburger-menu" id="hamburgerMenu">
    <a href="../student/student-homepage.php" class="menu-item"><i class="fas fa-home"></i>Home</a>
    <a href="leaderboard-student.php" class="menu-item"><i class="fas fa-trophy"></i>Leaderboard</a>
    <a href="progress-student.php" class="menu-item"><i class="fas fa-chart-line"></i>Progress</a>
    <a href="../student/student-profile.php" class="menu-item"><i class="fas fa-user"></i>Profile</a>
    <a href="../logout/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i>Log Out</a>
</div>

<section class="hero-container">
    <div class="hero-content">
        <h1>Explore a New Type of Learning</h1>
        <p>
            Make your home in a world of unlimited Java adventure. Master coding skills and play with friends. Build your programming knowledge through interactive gameplay.
        </p>
        <div class="hero-buttons">
            <a href="../student/javaquest.php" class="cta-button primary">Play For Free</a>
            <a href="../student/progress-student.php" class="cta-button secondary">Track Progress</a>
        </div>
    </div>
    <div class="hero-image">
        <video width="100%" autoplay muted loop>
            <source src="../video/vid1.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="hero-banner">
        <h2>Chapter 2 Is Here</h2>
    </div>
</section>

<section class="features-section">
    <h2>WHERE CODING SKILLS<br>COME TO LIFE</h2>
    
    <p class="section-description">
        JavaQuest is building a platform where learners can master Java programming through an exciting game environment that integrates real coding challenges.
    </p>
    
    <div class="features-grid">
        <div class="feature-box">
            <h3>Game-Based Learning</h3>
            <p>Learn Java programming through an engaging snake and ladder game that makes coding fun and interactive.</p>
        </div>
        <div class="feature-box">
            <h3>Pixel Art World</h3>
            <p>Immerse yourself in a charming pixel art environment that brings coding concepts to life visually.</p>
        </div>
        <div class="feature-box">
            <h3>Progress Tracking</h3>
            <p>Monitor your programming journey with detailed statistics and unlock achievements as you advance.</p>
        </div>
        <div class="feature-box">
            <h3>And more...</h3>
            <p>Compete on our global leaderboard to track your ranking and climb to Snake and Ladders stardom.</p>
        </div>
    </div>
    
    <a href="../student/javaquest.php" class="cta-button primary centered">Play For Free</a>
</section>

<section class="highlights-section">
    <div class="highlight-container">
        <div class="highlight-content">
            <h2>COMPETE FOR GLORY</h2>
            <p>Challenge friends on our global leaderboard. Track rankings, earn achievements, and climb to the top of our Java coding community.</p>
        </div>
        <div class="highlight-image">
            <img src="../image/HP1.gif" alt="Compete For Glory">
        </div>
    </div>
    
    <div class="highlight-container reverse">
        <div class="highlight-content">
            <h2>EARN REWARDS</h2>
            <p>Collect badges, unlock special characters, and earn achievements as you master Java concepts. Track your progress and showcase your coding skills.</p>
        </div>
        <div class="highlight-image">
            <img src="../image/HP2.gif" alt="Earn rewards">
        </div>
    </div>
    
    <div class="highlight-container">
        <div class="highlight-content">
            <h2>BUILD YOUR KNOWLEDGE</h2>
            <p>From basic syntax to advanced algorithms, level up your Java programming skills through interactive challenges and puzzles on our game board.</p>
        </div>
        <div class="highlight-image">
            <img src="../image/HP3.gif" alt="Build your knowledge">
        </div>
    </div>
</section>

<section class="own-world-section">
    <h2>Own Your Progress</h2>
    <p>What you learn is yours to keep. Track your progress, earn certificates, and build a portfolio of Java skills that will serve you in the real world.</p>
    <a href="../student/leaderboard-student.php" class="cta-button secondary">Explore Leaderboard</a>
</section>

<section class="how-to-play">
    <h2>How To Play JavaQuest</h2>
    <div class="steps-container">
        <div class="step">
            <div class="step-number">1</div>
            <h3>Create an Account</h3>
            <p>Sign up and choose your game character - are you a Java Ninja or Code Wizard?</p>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <h3>Roll the Dice</h3>
            <p>Roll virtual dice to move your character across our Java-themed game board.</p>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <h3>Solve Challenges</h3>
            <p>Land on a square and face a Java programming challenge appropriate to your level.</p>
        </div>
        <div class="step">
            <div class="step-number">4</div>
            <h3>Climb or Slide</h3>
            <p>Solve correctly to climb ladders and advance quickly. Get it wrong? Watch out for snakes!</p>
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="stat-container">
        <h3>OVER 5,000 PLAYERS</h3>
        <p>Join one of the largest coding game communities & a player base eager to experience Java programming in a new way.</p>
    </div>
    <div class="stat-container">
        <h3>NEW CHALLENGES COMING</h3>
        <p>Advanced challenges are dropping soon with the release of Chapter Two. Earn tokens and rewards just by solving coding problems.</p>
    </div>
    <div class="stat-container">
        <h3>UPDATES EVERY TWO WEEKS</h3>
        <p>Prepare for new concepts and learning possibilities. A whole new set of Java programming activities await.</p>
    </div>
</section>

<section class="cta-section">
    <h2>Ready to Master Java While Having Fun?</h2>
    <p>Join thousands of programmers who've transformed their coding skills through our game-based approach</p>
    <a href="../student/javaquest.php" class="cta-button primary large">Start Playing</a>
</section>

<footer>
    <div class="footer-container">
        <div class="footer-top">
            <div class="footer-contact">
                <p>support@javaquest.com</p>
                <p>We're here whenever you need help, just send us an email.</p>
            </div>
        </div>
        
        <div class="footer-links">
            <div class="footer-section">
                <h3>About Us</h3>
                <ul>
                    <li><a href="../about-us/about-us.php">About JavaQuest</a></li>
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

<script>
// Improved hamburger menu JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Check for new_game parameter in URL
    const urlParams = new URLSearchParams(window.location.search);
    const isNewGame = urlParams.get('new_game') === '1';
    const completedQuiz = urlParams.get('completed_quiz');
    
    // If this page was loaded after completing a game, modify only the specific quiz link
    if (isNewGame && completedQuiz) {
        // Get quiz mappings from localStorage or use the one from URL
        const quizToComplete = completedQuiz || localStorage.getItem('completed_quiz_id');
        
        if (quizToComplete) {
            // Update only the link for the completed quiz
            document.querySelectorAll('a[href="../student/javaquest.php"]').forEach(link => {
                // Add query parameter for the specific completed quiz
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Pass the completed quiz ID to the javaquest selection page
                    window.location.href = "../student/javaquest.php?completed_quiz=" + quizToComplete;
                });
            });
        }
    }
    
    const menuBtn = document.getElementById('menuBtn');
    const hamburgerMenu = document.getElementById('hamburgerMenu');
    
    // Toggle menu when menu button is clicked
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
    
    // Close menu when clicking outside of it
    document.addEventListener('click', function(event) {
        if (hamburgerMenu.classList.contains('show') && 
            !hamburgerMenu.contains(event.target) && 
            !menuBtn.contains(event.target)) {
            
            hamburgerMenu.classList.remove('show');
            
            // Reset icon and button style
            const icon = menuBtn.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
            
            // Remove active class from button
            menuBtn.classList.remove('active');
        }
    });
    
    // Add active class to current page menu item
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        const itemPath = item.getAttribute('href');
        if (currentPath.endsWith(itemPath)) {
            item.classList.add('active');
        }
    });
    
    // Add close button to the top of the menu
    const closeButton = document.createElement('div');
    closeButton.className = 'menu-close';
    closeButton.innerHTML = '<i class="fas fa-times"></i>';
    closeButton.style.position = 'absolute';
    closeButton.style.top = '20px';
    closeButton.style.right = '20px';
    closeButton.style.fontSize = '24px';
    closeButton.style.color = 'var(--accent-color)';
    closeButton.style.cursor = 'pointer';
    closeButton.style.zIndex = '1002';
    
    hamburgerMenu.prepend(closeButton);
    
    closeButton.addEventListener('click', function() {
        hamburgerMenu.classList.remove('show');
        menuBtn.classList.remove('active');
        const icon = menuBtn.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    });
});
</script>
</body>
</html>
