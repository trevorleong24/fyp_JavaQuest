<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Tracking - JavaQuest</title>
    <link rel="stylesheet" href="../css/main-homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        /* Fix header position to match main-homepage.html */
        header {
            position: fixed;
            width: 90%;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        
        /* Add top padding to account for fixed header */
        .hero-container {
            padding-top: 150px;
        }
        
        /* Update dropdown menu styling to match main-homepage.html */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: var(--dark-color);
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 10;
            min-width: 200px;
            padding: 10px 0;
            border-radius: 4px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transform: translateY(10px);
            transition: all 0.3s ease-in-out;
        }
        
        .dropdown-content a {
            display: block;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--light-color);
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--accent-color);
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
            transform: translateY(0);
        }
        
        /* Progress specific styles */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        /* Style the content-container to match main-homepage.html */
        .content-container {
            background: rgba(30, 30, 30, 0.7);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            margin: 3rem auto;
            max-width: 1200px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stat-card {
            background: rgba(30, 30, 30, 0.7);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.5s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            border-top: 4px solid var(--primary-color);
        }
        
        .stat-card:nth-child(1) {
            border-top-color: var(--primary-color);
        }
        
        .stat-card:nth-child(2) {
            border-top-color: var(--secondary-color);
        }
        
        .stat-card:nth-child(3) {
            border-top-color: var(--accent-color);
        }
        
        .stat-card:nth-child(4) {
            border-top-color: var(--danger-color, #FF5252);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-card:nth-child(1) .stat-icon {
            color: var(--primary-color);
        }
        
        .stat-card:nth-child(2) .stat-icon {
            color: var(--secondary-color);
        }
        
        .stat-card:nth-child(3) .stat-icon {
            color: var(--accent-color);
        }
        
        .stat-card:nth-child(4) .stat-icon {
            color: var(--danger-color, #FF5252);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--light-color);
            opacity: 0.8;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background: rgba(30, 30, 30, 0.5);
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .empty-state h3 {
            margin-bottom: 1rem;
            color: var(--accent-color);
        }
        
        .empty-state p {
            opacity: 0.8;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .chart-container {
            background: rgba(30, 30, 30, 0.7);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .chart-title {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            color: var(--light-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-title i {
            color: var(--accent-color);
        }
        
        canvas {
            width: 100% !important;
            max-height: 300px !important;
        }
        
        .progress-charts {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        @media (min-width: 768px) {
            .progress-charts {
                flex-direction: row;
            }
            .progress-charts .chart-container {
                flex: 1;
            }
        }
        
        @media (max-width: 767px) {
            .chart-container {
                margin-bottom: 1.5rem;
            }
            canvas {
                height: 250px !important;
            }
        }
    </style>
</head>
<body>
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

    <section class="hero-container">
        <div class="hero-content">
            <h1>Track Your Java Journey</h1>
            <p>
                JavaQuest's progression system helps you monitor your learning path, visualize skill improvements, and stay motivated throughout your coding adventure.
            </p>
            <div class="hero-buttons">
                <a href="../login/login.php" class="cta-button primary">Create Account Now</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="../image/P1.gif" alt="JavaQuest Progress" onerror="this.src='../image/game_background_1.png'">
        </div>
    </section>

    <section class="features-section">
        <h2>WHAT IS GAME PROGRESSION?</h2>
        
        <p class="section-description">
            In JavaQuest, progression is how you advance through levels, mechanics, and challenges, giving you a sense of achievement and forward momentum in your Java learning journey.
        </p>
        
        <div class="features-grid">
            <div class="feature-box">
                <h3>Feel Productive</h3>
                <p>Our progression system makes you feel accomplished as you complete Java challenges and unlock new content.</p>
            </div>
            <div class="feature-box">
                <h3>Feel Powerful</h3>
                <p>Gain new Java skills and abilities that let you solve increasingly complex programming challenges.</p>
            </div>
            <div class="feature-box">
                <h3>Face Evolving Challenges</h3>
                <p>Experience a carefully designed difficulty curve that keeps you engaged and learning without frustration.</p>
            </div>
            <div class="feature-box">
                <h3>Track Long-Term Growth</h3>
                <p>Set goals for your Java mastery and watch as your programming portfolio grows over time.</p>
            </div>
        </div>
    </section>

    <section class="highlights-section">
        <div class="highlight-container">
            <div class="highlight-content">
                <h2>SKILL MASTERY</h2>
                <p>Watch your Java knowledge grow through detailed skill trees and proficiency metrics. JavaQuest breaks down complex programming concepts into manageable learning paths, making it easy to see your progress in specific areas.</p>
            </div>
            <div class="highlight-image">
                <img src="../image/P2.gif" alt="Skill Mastery" onerror="this.src='../image/homepage1.png'">
            </div>
        </div>
        
        <div class="highlight-container reverse">
            <div class="highlight-content">
                <h2>ACHIEVEMENT SYSTEM</h2>
                <p>Earn badges and achievements as you master Java concepts. Our achievement system recognizes your coding milestones, from writing your first loop to implementing complex algorithms.</p>
            </div>
            <div class="highlight-image">
                <img src="../image/P3.jpg" alt="Achievement System" onerror="this.src='../image/homepage2.png'">
            </div>
        </div>
    </section>

    <section class="how-to-play">
        <h2>How Progression Works</h2>
        <div class="steps-container">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Complete Games</h3>
                <p>Solve Java programming problems to earn points and experience.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Level Up</h3>
                <p>Gain levels as you accumulate experience, unlocking new areas of the game.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Master Skills</h3>
                <p>Focus on specific Java concepts to increase your proficiency in different areas.</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>Earn Rewards</h3>
                <p>Collect badges, achievements, and special items as you progress through your journey.</p>
            </div>
        </div>
    </section>

    <div class="content-container">
        <h2 style="text-align: center; margin-bottom: 1.5rem;">YOUR PROGRESS DASHBOARD</h2>
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-code"></i>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-label">Games Completed</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-label">Total Attempts</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-label">Total Score</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-level-up-alt"></i>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-label">Current Level</div>
            </div>
        </div>
        
        <div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <h3>No Progress Data Available</h3>
            <p>Create an account or log in to start tracking your Java programming journey!</p>
        </div>
        
        <div class="progress-charts">
            <div class="chart-container">
                <h3 class="chart-title"><i class="fas fa-calendar-week"></i> Weekly Activity</h3>
                <canvas id="weeklyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <section class="cta-section">
        <h2>Ready to Start Your Java Journey?</h2>
        <p>Join thousands of learners and start tracking your coding progression today</p>
        <a href="../login/login.php" class="cta-button primary large">Create Your Account</a>
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
                        <li><a href="../homepage/leaderboard-main.php">Leaderboard</a></li>
                        <li><a href="../homepage/progress-main.php">Progress</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-copyright">
                <p>Copyright &copy; 2025 JavaQuest. All rights reserved</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown menu
            const menuButton = document.querySelector('.dropdown-btn');
            const dropdownContent = document.querySelector('.dropdown-content');
            
            if (menuButton && dropdownContent) {
                menuButton.addEventListener('click', function() {
                    dropdownContent.classList.toggle('show');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!event.target.matches('.dropdown-btn') && !event.target.matches('.dropdown-content *')) {
                        dropdownContent.classList.remove('show');
                    }
                });
            }
            
            // Sample chart data
            const weeklyActivityCtx = document.getElementById('weeklyActivityChart').getContext('2d');
            const weeklyActivityChart = new Chart(weeklyActivityCtx, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Completed Challenges',
                        data: [0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: '#8A2BE2',
                        borderColor: '#8A2BE2',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html> 