<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - JavaQuest</title>
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
        
        /* Leaderboard specific styles */
        .leaderboard-container {
            overflow-x: auto;
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
        
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .leaderboard-table th, .leaderboard-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .leaderboard-table th {
            background-color: var(--table-header, #1a1a1a);
            color: var(--accent-color);
            font-weight: normal;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        
        .leaderboard-table tr:hover {
            background-color: var(--table-row-hover, rgba(138, 43, 226, 0.1));
        }
        
        .rank {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--dark-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .rank-1 {
            background-color: gold;
            color: black;
        }
        
        .rank-2 {
            background-color: silver;
            color: black;
        }
        
        .rank-3 {
            background-color: #cd7f32; /* bronze */
            color: black;
        }
        
        .student-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .student-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent-color);
        }
        
        .search-container {
            display: flex;
            margin-bottom: 1.5rem;
        }
        
        .search-bar {
            flex: 1;
            padding: 0.8rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 4px 0 0 4px;
            font-size: 1rem;
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--light-color);
        }
        
        .search-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .search-button:hover {
            background-color: var(--accent-color);
            color: var(--dark-color);
        }

        /* Make feature boxes bigger to prevent text overlap */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-box {
            background-color: rgba(255, 255, 255, 0.05);
            padding: 30px 25px;
            border-radius: 8px;
            text-align: left;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        
        .feature-box h3 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.1em;
            color: var(--accent-color);
            line-height: 1.6;
        }
        
        .feature-box p {
            margin-bottom: 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8em;
            line-height: 1.8;
        }
        
        /* Add responsive adjustments for smaller screens */
        @media (max-width: 1200px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .features-grid {
                grid-template-columns: 1fr;
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
            <h1>JavaQuest Leaderboard</h1>
            <p>
                Compare your achievements, compete with others, and climb the ranks in our Java coding adventure. The leaderboard showcases top performers and motivates you to improve your skills.
            </p>
            <div class="hero-buttons">
                <a href="../login/login.php" class="cta-button primary">Create Account Now</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="../image/L1.gif" alt="JavaQuest Leaderboard" onerror="this.src='../image/game_background_1.png'">
        </div>
    </section>

    <section class="features-section">
        <h2>THE PURPOSE OF OUR LEADERBOARD</h2>
        
        <p class="section-description">
            Our leaderboard system is designed to foster competition, motivate players to improve their performance, and create a sense of community among Java learners.
        </p>
        
        <div class="features-grid">
            <div class="feature-box">
                <h3>Foster Competition</h3>
                <p>Compete with fellow coders to see who can solve challenges faster and more efficiently, driving you to sharpen your Java skills.</p>
            </div>
            <div class="feature-box">
                <h3>Track Progress</h3>
                <p>Monitor your ranking over time and see how your Java programming journey is progressing relative to others.</p>
            </div>
            <div class="feature-box">
                <h3>Build Community</h3>
                <p>Connect with like-minded Java enthusiasts through healthy competition and shared learning goals.</p>
            </div>
            <div class="feature-box">
                <h3>Earn Recognition</h3>
                <p>Get recognized for your coding achievements and showcase your expertise to the JavaQuest community.</p>
            </div>
        </div>
    </section>

    <section class="highlights-section">
        <div class="highlight-container">
            <div class="highlight-content">
                <h2>COMPETE GLOBALLY</h2>
                <p>Challenge yourself against Java learners from around the world. Our global leaderboard updates in real-time, showing your position among thousands of players competing to master Java programming concepts.</p>
            </div>
            <div class="highlight-image">
                <img src="../image/L2.gif" alt="Global Competition" onerror="this.src='../image/homepage1.png'">
            </div>
        </div>
        
        <div class="highlight-container reverse">
            <div class="highlight-content">
                <h2>MULTIPLE RANKING CATEGORIES</h2>
                <p>JavaQuest's leaderboard ranks players based on different criteria: score, level completion, coding accuracy, and more. Find your strength and work to the top of multiple categories.</p>
            </div>
            <div class="highlight-image">
                <img src="../image/L3.jpeg" alt="Multiple Rankings" onerror="this.src='../image/homepage2.png'">
            </div>
        </div>
    </section>

    <div class="content-container">
        <h2 style="text-align: center; margin-bottom: 1.5rem;">CURRENT RANKINGS</h2>
        <div class="search-container">
            <input type="text" class="search-bar" placeholder="Search by username...">
            <button class="search-button">Search</button>
        </div>
        
        <div class="leaderboard-container">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Player</th>
                        <th>Level</th>
                        <th>Challenges Completed</th>
                        <th>Total Score</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sample data - In a real application, this would be populated from the database -->
                    <tr>
                        <td><div class="rank rank-1">1</div></td>
                        <td>
                            <div class="student-info">
                                <img src="../image/Player-1.jpg" alt="Player 1" onerror="this.src='../image/default-avatar.png'">
                                <span>JavaMaster123</span>
                            </div>
                        </td>
                        <td>30</td>
                        <td>145</td>
                        <td>9850</td>
                    </tr>
                    <tr>
                        <td><div class="rank rank-2">2</div></td>
                        <td>
                            <div class="student-info">
                                <img src="../image/Player-2.png" alt="Player 2" onerror="this.src='../image/default-avatar.png'">
                                <span>CodeNinja42</span>
                            </div>
                        </td>
                        <td>28</td>
                        <td>132</td>
                        <td>9420</td>
                    </tr>
                    <tr>
                        <td><div class="rank rank-3">3</div></td>
                        <td>
                            <div class="student-info">
                                <img src="../image/Player-3.gif" alt="Player 3" onerror="this.src='../image/default-avatar.png'">
                                <span>ProgrammingWizard</span>
                            </div>
                        </td>
                        <td>27</td>
                        <td>129</td>
                        <td>9150</td>
                    </tr>
                    <tr>
                        <td><div class="rank">4</div></td>
                        <td>
                            <div class="student-info">
                                <img src="../image/Player-4.jpg" alt="Player 4" onerror="this.src='../image/default-avatar.png'">
                                <span>ByteBuster</span>
                            </div>
                        </td>
                        <td>25</td>
                        <td>118</td>
                        <td>8720</td>
                    </tr>
                    <tr>
                        <td><div class="rank">5</div></td>
                        <td>
                            <div class="student-info">
                                <img src="../image/Player-5.jpeg" alt="Player 5" onerror="this.src='../image/default-avatar.png'">
                                <span>AlgorithmAce</span>
                            </div>
                        </td>
                        <td>23</td>
                        <td>110</td>
                        <td>8150</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <section class="cta-section">
        <h2>Ready to Climb the Ranks?</h2>
        <p>Join thousands of Java enthusiasts and see where you stand in the JavaQuest community</p>
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
        });
    </script>
</body>
</html> 