<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaQuest - About Us</title>
    
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <!-- Use admin styling for admin users -->
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional styles for about us content when viewed as admin */
        .main-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .content-container {
            background: rgba(30, 30, 30, 0.7);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        h1 {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
            text-align: center;
            font-family: 'Press Start 2P', cursive;
        }
        
        h2.section-title {
            font-size: 1.4rem;
            margin: 30px 0 15px;
            color: var(--secondary-color);
            text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-family: 'Press Start 2P', cursive;
        }
        
        p {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--light-color);
            margin-bottom: 20px;
        }
        
        ol {
            padding-left: 25px;
            margin: 20px 0;
            color: var(--light-color);
        }
        
        ol li {
            margin-bottom: 15px;
            font-size: 1rem;
        }
    </style>
    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
    <!-- Use student homepage styling for student users -->
    <link rel="stylesheet" href="../css/student-homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional styles for about us content that works with student theme */
        .about-container {
            max-width: 1000px;
            margin: 120px auto 40px; /* Increased top margin to avoid header overlap */
            padding: 40px;
            background-color: rgba(30, 30, 30, 0.9);
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .about-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(0deg, transparent 24%, 
                    rgba(255, 255, 255, .02) 25%, 
                    rgba(255, 255, 255, .02) 26%, 
                    transparent 27%, transparent 74%, 
                    rgba(255, 255, 255, .02) 75%, 
                    rgba(255, 255, 255, .02) 76%, transparent 77%, transparent),
                linear-gradient(90deg, transparent 24%, 
                    rgba(255, 255, 255, .02) 25%, 
                    rgba(255, 255, 255, .02) 26%, 
                    transparent 27%, transparent 74%, 
                    rgba(255, 255, 255, .02) 75%, 
                    rgba(255, 255, 255, .02) 76%, transparent 77%, transparent);
            background-size: 50px 50px;
            opacity: 0.5;
            z-index: -1;
        }
        
        h1 {
            font-size: 2.5em;
            margin-bottom: 30px;
            color: var(--accent-color);
            text-align: center;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Press Start 2P', cursive;
        }
        
        h2.section-title {
            font-size: 1.8em;
            margin: 30px 0 15px;
            color: var(--secondary-color);
            text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-family: 'Press Start 2P', cursive;
        }
        
        p {
            font-size: 1.1em;
            line-height: 1.7;
            color: var(--light-color);
            margin-bottom: 20px;
        }
        
        ol {
            padding-left: 25px;
            margin: 20px 0;
            color: var(--light-color);
        }
        
        ol li {
            margin-bottom: 15px;
            font-size: 1.05em;
        }
    </style>
    <?php else: ?>
    <!-- Use main homepage styling for both instructor and non-logged-in users -->
    <link rel="stylesheet" href="../css/main-homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            font-family: 'Press Start 2P', cursive;
        }
        
        .login-btn {
            background-color: purple;
            color: white;
        }
        
        .dropdown-btn, .dropdown-content a {
            font-family: 'Press Start 2P', cursive;
        }

        /* Additional styles for about us page content */
        .container {
            margin-top: 120px;
            padding: 40px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
            background-color: rgba(30, 30, 30, 0.9);
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(0deg, transparent 24%, 
                    rgba(255, 255, 255, .02) 25%, 
                    rgba(255, 255, 255, .02) 26%, 
                    transparent 27%, transparent 74%, 
                    rgba(255, 255, 255, .02) 75%, 
                    rgba(255, 255, 255, .02) 76%, transparent 77%, transparent),
                linear-gradient(90deg, transparent 24%, 
                    rgba(255, 255, 255, .02) 25%, 
                    rgba(255, 255, 255, .02) 26%, 
                    transparent 27%, transparent 74%, 
                    rgba(255, 255, 255, .02) 75%, 
                    rgba(255, 255, 255, .02) 76%, transparent 77%, transparent);
            background-size: 50px 50px;
            opacity: 0.5;
            z-index: -1;
        }
        
        h1 {
            font-size: 2.5em;
            margin-bottom: 30px;
            color: var(--accent-color);
            text-align: center;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        h2.section-title {
            font-size: 1.8em;
            margin: 30px 0 15px;
            color: var(--secondary-color);
            text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        p {
            font-size: 1.1em;
            line-height: 1.7;
            color: var(--light-color);
            margin-bottom: 20px;
        }
        
        ol {
            padding-left: 25px;
            margin: 20px 0;
            color: var(--light-color);
        }
        
        ol li {
            margin-bottom: 15px;
            font-size: 1.05em;
        }

        /* Adjust container margin for student users to account for sticky header */
        body.logged-in .container {
            margin-top: 40px;
        }
    </style>
    <?php endif; ?>
</head>
<body <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') echo 'class="logged-in"'; ?>>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
<!-- Header for student users - exactly like in student-homepage.php -->
<header>
    <div class="logo-nav">
        <a href="../student/student-homepage.php" class="logo">JavaQuest</a>
        <div class="nav-links">
            <a href="../about-us/about-us.php" class="nav-link">About</a>
            <a href="../term-privacy/term-privacy.php" class="nav-link">Terms & Privacy</a>
        </div>
    </div>
    <div class="auth-social">
        <button class="menu-btn" id="menuBtn">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>

<!-- Hamburger Menu with improved implementation -->
<div class="hamburger-menu" id="hamburgerMenu">
    <a href="../student/student-homepage.php" class="menu-item"><i class="fas fa-home"></i>Home</a>
    <a href="../student/leaderboard-student.php" class="menu-item"><i class="fas fa-trophy"></i>Leaderboard</a>
    <a href="../student/progress-student.php" class="menu-item"><i class="fas fa-chart-line"></i>Progress</a>
    <a href="../student/student-profile.php" class="menu-item"><i class="fas fa-user"></i>Profile</a>
    <a href="../logout/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i>Log Out</a>
</div>

<?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
<!-- Header for admin users - like in admin.php -->
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

<?php elseif (!isset($_SESSION['username'])): ?>
<!-- Header for non-logged in users - styled like main-homepage.html -->
<header>
    <div class="logo-nav">
        <a href="../homepage/main-homepage.html" class="logo">JavaQuest</a>
        <div class="nav-links">
            <div class="dropdown">
                <button class="dropdown-btn">More ▼</button>
                <div class="dropdown-content">
                    <a href="../homepage/leaderboard-main.php">Leaderboard</a>
                    <a href="../homepage/progress-main.php">Progress</a>
                </div>
            </div>
            <a href="../about-us/about-us.php" class="nav-link">About</a>
            <a href="../term-privacy/term-privacy.php" class="nav-link">Terms & Privacy</a>
        </div>
    </div>
    <div class="auth-social">
        <a href="../login/login.php" class="login-btn">Login</a>
    </div>
</header>
<?php else: ?>
<!-- Original header for other logged in users -->
<header>
    <div class="logo-nav">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="#" class="logo" id="phraseThizLink">JavaQuest Admin</a>
        <?php else: ?>
            <a href="#" class="logo" id="phraseThizLink">JavaQuest</a>
        <?php endif; ?>

        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
        <div class="dropdown">
            <button class="dropdown-btn">Our Quiz ▼</button>
            <div class="dropdown-content">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'instructor'): ?>
                    <a href="../instructor/grammar-instructor.php">Grammar</a>
                    <a href="../instructor/vocabulary-instructor.php">Vocabulary</a>
                <?php else: ?>
                    <a href="../student/grammar.php">Grammar</a>
                    <a href="../student/vocabulary.php">Vocabulary</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="auth-container">
        <?php 
            $username = htmlspecialchars($_SESSION['username']);
            $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

            if ($role === 'instructor') {
                echo '<span>Welcome, <a href="../instructor/instructor-profile.php" class="profile-link">' . $username . '</a>!</span>';
            } elseif ($role === 'admin') {
                echo '<span>Welcome, ' . $username . '!</span> <a href="../logout/logout.php">Logout</a>';
            } else {
                echo '<span>Welcome, ' . $username . '!</span>';
            }
        ?>
    </div>
</header>
<?php endif; ?>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
<!-- Admin content container -->
<section class="main-container">
    <div class="dashboard-header">
        <h1>About Us</h1>
    </div>
    
    <div class="content-container">
        <p>
            JavaQuest is a web-based interactive learning platform aimed at improving Java programming skills among university students. 
            Our mission is to provide an engaging, accessible, and personalized learning environment that allows students to enhance 
            their coding skills with ease and confidence. Whether you're aiming for better development skills or preparing for exams, 
            JavaQuest offers the tools you need to succeed.
        </p>

        <h2 class="section-title">Vision</h2>
        <p>
            To be the leading platform that empowers university students to master Java programming through interactive, accessible, and personalized 
            learning experiences, fostering global communication and lifelong success.
        </p>

        <h2 class="section-title">Mission</h2>
        <ol>
            <li>To improve Java programming proficiency among university students by offering an engaging and interactive learning platform.</li>
            <li>To create a supportive and accessible environment that adapts to the unique learning needs and goals of each student.</li>
            <li>To enhance students' confidence in coding, whether for academic, personal, or professional purposes.</li>
        </ol>

        <h2 class="section-title">Goals</h2>
        <ol>
            <li>Provide a wide range of interactive tools, exercises, and resources designed to improve Java programming skills.</li>
            <li>Implement adaptive learning paths that cater to individual skill levels and learning progress.</li>
            <li>Partner with universities and educational institutions to integrate JavaQuest into their curriculum and extracurricular activities.</li>
            <li>Continuously update the platform with new content and features to maintain relevance and engagement.</li>
            <li>Offer performance tracking and feedback mechanisms to help students monitor their progress and achieve their learning goals efficiently.</li>
        </ol>
    </div>
</section>
<?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
<!-- Student content container -->
<div class="about-container">
    <h1>About Us</h1>
    <p>
        JavaQuest is a web-based interactive learning platform aimed at improving Java programming skills among university students. 
        Our mission is to provide an engaging, accessible, and personalized learning environment that allows students to enhance 
        their coding skills with ease and confidence. Whether you're aiming for better development skills or preparing for exams, 
        JavaQuest offers the tools you need to succeed.
    </p>

    <h2 class="section-title">Vision</h2>
    <p>
        To be the leading platform that empowers university students to master Java programming through interactive, accessible, and personalized 
        learning experiences, fostering global communication and lifelong success.
    </p>

    <h2 class="section-title">Mission</h2>
    <ol>
        <li>To improve Java programming proficiency among university students by offering an engaging and interactive learning platform.</li>
        <li>To create a supportive and accessible environment that adapts to the unique learning needs and goals of each student.</li>
        <li>To enhance students' confidence in coding, whether for academic, personal, or professional purposes.</li>
    </ol>

    <h2 class="section-title">Goals</h2>
    <ol>
        <li>Provide a wide range of interactive tools, exercises, and resources designed to improve Java programming skills.</li>
        <li>Implement adaptive learning paths that cater to individual skill levels and learning progress.</li>
        <li>Partner with universities and educational institutions to integrate JavaQuest into their curriculum and extracurricular activities.</li>
        <li>Continuously update the platform with new content and features to maintain relevance and engagement.</li>
        <li>Offer performance tracking and feedback mechanisms to help students monitor their progress and achieve their learning goals efficiently.</li>
    </ol>
</div>
<?php else: ?>
<div class="container">
    <h1>About Us</h1>
    <p>
        JavaQuest is a web-based interactive learning platform aimed at improving Java programming skills among university students. 
        Our mission is to provide an engaging, accessible, and personalized learning environment that allows students to enhance 
        their coding skills with ease and confidence. Whether you're aiming for better development skills or preparing for exams, 
        JavaQuest offers the tools you need to succeed.
    </p>

    <h2 class="section-title">Vision</h2>
    <p>
        To be the leading platform that empowers university students to master Java programming through interactive, accessible, and personalized 
        learning experiences, fostering global communication and lifelong success.
    </p>

    <h2 class="section-title">Mission</h2>
    <ol>
        <li>To improve Java programming proficiency among university students by offering an engaging and interactive learning platform.</li>
        <li>To create a supportive and accessible environment that adapts to the unique learning needs and goals of each student.</li>
        <li>To enhance students' confidence in coding, whether for academic, personal, or professional purposes.</li>
    </ol>

    <h2 class="section-title">Goals</h2>
    <ol>
        <li>Provide a wide range of interactive tools, exercises, and resources designed to improve Java programming skills.</li>
        <li>Implement adaptive learning paths that cater to individual skill levels and learning progress.</li>
        <li>Partner with universities and educational institutions to integrate JavaQuest into their curriculum and extracurricular activities.</li>
        <li>Continuously update the platform with new content and features to maintain relevance and engagement.</li>
        <li>Offer performance tracking and feedback mechanisms to help students monitor their progress and achieve their learning goals efficiently.</li>
    </ol>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
<!-- Footer for student users - exactly like in student-homepage.php -->
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
                    <li><a href="../student/leaderboard-student.php">Leaderboard</a></li>
                    <li><a href="../student/progress-student.php">Progress</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-copyright">
            <p>Copyright &copy; 2024 JavaQuest. All rights reserved</p>
        </div>
    </div>
</footer>

<?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
<!-- Footer for admin users - like in admin.php -->
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

<?php elseif (!isset($_SESSION['username'])): ?>
<!-- Footer for non-logged in users - styled like main-homepage.html -->
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
                    <li><a href="../homepage/leaderboard-main.php">Leaderboard</a></li>
                    <li><a href="../homepage/progress-main.php">Progress</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-copyright">
            <p>Copyright &copy; 2024 JavaQuest. All rights reserved</p>
        </div>
    </div>
</footer>
<?php else: ?>
<!-- Original footer for other logged in users -->
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
        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'instructor'): ?>
                        <li><a href="../instructor/grammar-instructor.php">Grammar</a></li>
                        <li><a href="../instructor/vocabulary-instructor.php">Vocabulary</a></li>
                    <?php else: ?>
                        <li><a href="../student/grammar.php">Grammar</a></li>
                        <li><a href="../student/vocabulary.php">Vocabulary</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="footer-copyright">
            <p>Copyright &copy; 2024 JavaQuest. All rights reserved</p>
        </div>
    </div>
</footer>
<?php endif; ?>

<script>
    document.getElementById('phraseThizLink')?.addEventListener('click', function(event) {
        event.preventDefault();
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'student'): ?>
                window.location.href = '../student/student-homepage.php';
            <?php elseif ($_SESSION['role'] === 'instructor'): ?>
                window.location.href = '../instructor/instructor-homepage.php';
            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                window.location.href = '../admin/admin.php';
            <?php else: ?>
                window.location.href = '../main-homepage.html';
            <?php endif; ?>
        <?php else: ?>
            window.location.href = '../main-homepage.html';
        <?php endif; ?>
    });

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
    // Improved hamburger menu JavaScript - exactly like in student-homepage.php
    document.addEventListener('DOMContentLoaded', function() {
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
    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    // Hamburger menu JavaScript for admin - similar to admin.js
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
                !menuBtn.contains(event.target) && 
                !hamburgerMenu.contains(event.target)) {
                
                hamburgerMenu.classList.remove('show');
                
                // Reset icon and button style
                const icon = menuBtn.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
                
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
    });
    <?php endif; ?>
</script>

</body>
</html>
