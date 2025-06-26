<?php
session_start(); // Start the session

// Determine the user's homepage based on their role
function getUserHomepage() {
    if (!isset($_SESSION['role'])) {
        return "../homepage/main-homepage.html"; // Default to main homepage if no role
    }

    switch ($_SESSION['role']) {
        case 'instructor':
            return "../instructor/instructor-homepage.php";
        case 'admin':
            return "../admin/admin.php";
        case 'student':
            return "../student/student-homepage.php";
        default:
            return "../homepage/main-homepage.html"; // Default for unrecognized roles
    }
}

// Determine the user's profile link based on their role
function getUserProfileLink() {
    if (!isset($_SESSION['role'])) {
        return "#"; // Default to a placeholder if no role
    }

    switch ($_SESSION['role']) {
        case 'instructor':
            return "../instructor/instructor-profile.php";
        case 'admin':
            return null; // Admin username is not clickable
        case 'student':
            return "../student/student-profile.php";
        default:
            return "#"; // Default for unrecognized roles
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaQuest - Terms and Privacy</title>
    
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <!-- Admin styling -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../css/admin.css">
    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
        <!-- Student styling -->
        <link rel="stylesheet" href="../css/student-homepage.css">
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin'): ?>
        <!-- Regular logged-in user styling -->
        <link rel="stylesheet" href="../css/term-privacy.css">
    <?php else: ?>
        <!-- Non-logged in user styling (main homepage style) -->
        <link rel="stylesheet" href="../css/main-homepage.css">
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php endif; ?>

    <style>
        /* Additional styles for terms and privacy content that work with all themes */
        .tabs {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            border-bottom: 2px solid var(--gray-color, #e0e0e0);
            margin-bottom: 30px;
            padding-left: 0;
        }

        .tabs a {
            text-decoration: none;
            padding: 12px 25px;
            font-size: 1em;
            color: var(--light-color, #666);
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            margin-right: 10px;
            cursor: pointer;
        }

        .tabs a:hover {
            color: var(--accent-color, #007BFF);
        }

        .tabs a.active {
            border-bottom: 3px solid var(--accent-color, #007BFF);
            color: var(--accent-color, #007BFF);
            font-weight: bold;
        }

        .hidden {
            display: none;
        }

        /* Content styling for admin theme */
        .terms-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .terms-content-container {
            background: rgba(30, 30, 30, 0.7);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Ensure consistent styling for content across themes */
        .effective-dates {
            text-align: center;
            color: var(--light-color, #666);
            font-style: italic;
            margin-bottom: 25px;
        }

        /* Specific styling for admin theme content */
        body.admin-theme .content {
            color: var(--light-color, #F0F8FF);
            line-height: 1.8;
        }

        body.admin-theme h2 {
            color: var(--accent-color, #FFD700);
            font-family: 'Press Start 2P', cursive;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
        }

        body.admin-theme .content strong {
            color: var(--accent-color, #FFD700);
            background-color: rgba(255, 215, 0, 0.1);
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        body.admin-theme .content ol, 
        body.admin-theme .content ul {
            padding-left: 30px;
        }

        body.admin-theme .content ol li, 
        body.admin-theme .content ul li {
            margin-bottom: 15px;
        }

        /* Main homepage theme styling for non-logged in users */
        body.main-theme {
            font-family: 'Press Start 2P', cursive;
            background-color: var(--dark-color);
            color: var(--light-color);
        }

        body.main-theme .terms-pixel-container {
            max-width: 800px;
            margin: 120px auto 40px;
            background-color: rgba(30, 30, 30, 0.8);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        body.main-theme .tabs {
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        body.main-theme .tabs a {
            color: var(--light-color);
            font-size: 0.9em;
            padding: 10px 20px;
        }

        body.main-theme .tabs a:hover,
        body.main-theme .tabs a.active {
            color: var(--accent-color);
            border-bottom: 3px solid var(--accent-color);
        }

        body.main-theme h2 {
            color: var(--accent-color);
            font-size: 1.5em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
        }

        body.main-theme .effective-dates {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8em;
        }

        body.main-theme .content {
            color: var(--light-color);
            font-size: 0.85em;
            line-height: 1.8;
        }

        body.main-theme .content strong {
            color: var(--accent-color);
            background-color: rgba(255, 215, 0, 0.1);
            padding: 2px 4px;
            border-radius: 3px;
        }

        body.main-theme .content ol {
            padding-left: 20px;
        }

        body.main-theme .content ol li {
            margin-bottom: 15px;
        }

        body.main-theme .content ul {
            padding-left: 15px;
            margin-top: 10px;
        }

        body.main-theme .content ul li {
            margin-bottom: 8px;
        }

        /* Standard theme styling overrides */
        .container {
            max-width: 800px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }
    </style>
</head>

<body <?php 
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        echo 'class="admin-theme"';
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
        echo 'class="logged-in"';
    } elseif (!isset($_SESSION['role'])) {
        echo 'class="main-theme"';
    }
?>>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <!-- Admin Header -->
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

    <!-- Admin styled content -->
    <section class="terms-container">
        <div class="dashboard-header">
            <h1>Terms and Privacy</h1>
            <p class="dashboard-subtitle">Legal information about JavaQuest</p>
        </div>

        <div class="terms-content-container">
            <div class="tabs">
                <a href="#" id="terms-tab" class="active" onclick="showContent('terms')">Terms of Service</a>
                <a href="#" id="privacy-tab" onclick="showContent('privacy')">Privacy Policy</a>
            </div>

            <div id="terms-content">
                <h2 style="color: #FFD700; text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5); max-width: 100%; word-wrap: break-word; font-size: 1.3em; margin-bottom: 20px;">Terms of Service</h2>
                <p class="effective-dates" style="color: rgba(173, 216, 230, 0.8); width: 100%; text-align: center; font-size: 0.85em; margin-bottom: 20px;">Effective Date: 2 April 2015. Last Updated: 14 April 2022</p>
                <div class="content" style="color: white; max-width: 100%; word-wrap: break-word; font-size: 0.9em; line-height: 1.7;">
                    <ol style="color: white; padding-left: 25px; max-width: 100%; overflow-wrap: break-word; margin-top: 0;">
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Acceptance of Terms</strong>: 
                            <span style="display: block; margin-top: 6px;">By accessing or using this website, you agree to be bound by these Terms of Service.</span>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Eligibility</strong>: 
                            <span style="display: block; margin-top: 6px;">This platform is intended for users who are at least 18 years old. If you are under 18, you must obtain parental consent.</span>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">User Responsibilities</strong>: 
                            <span style="display: block; margin-top: 6px;">You are responsible for safeguarding your account credentials and all activities under your account.</span>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Prohibited Conduct</strong>:
                            <ul style="color: white; padding-left: 20px; margin-top: 10px; list-style-position: outside; margin-bottom: 0;">
                                <li style="color: white; margin-bottom: 10px;">Uploading harmful, illegal, or infringing content.</li>
                                <li style="color: white; margin-bottom: 10px;">Attempting to disrupt or breach website security.</li>
                                <li style="color: white; margin-bottom: 10px;">Engaging in fraudulent activities.</li>
                            </ul>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Content Ownership</strong>: 
                            <span style="display: block; margin-top: 6px;">All content provided by this platform, including text, graphics, and software, is owned by us or our licensors.</span>
                        </li>
                    </ol>
                </div>
            </div>

            <div id="privacy-content" class="hidden">
                <h2 style="color: #FFD700; text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5); max-width: 100%; word-wrap: break-word; font-size: 1.3em; margin-bottom: 20px;">Privacy Policy</h2>
                <p class="effective-dates" style="color: rgba(173, 216, 230, 0.8); width: 100%; text-align: center; font-size: 0.85em; margin-bottom: 20px;">Effective Date: 2 April 2015. Last Updated: 14 April 2022</p>
                <div class="content" style="color: white; max-width: 100%; word-wrap: break-word; font-size: 0.9em; line-height: 1.7;">
                    <ol style="color: white; padding-left: 25px; max-width: 100%; overflow-wrap: break-word; margin-top: 0;">
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Information Collection</strong>: 
                            <span style="display: block; margin-top: 6px;">We collect personal information, including your name, email address, and payment details, for service provision.</span>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Use of Information</strong>: 
                            <span style="display: block; margin-top: 6px;">Your data is used to personalize your experience, process transactions, and provide customer support.</span>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Data Sharing</strong>: 
                            <span style="display: block; margin-top: 6px;">We do not sell or share your data with third parties, except as required by law or for essential services (e.g., payment processors).</span>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Cookies</strong>: 
                            <span style="display: block; margin-top: 6px;">Our website uses cookies to enhance your browsing experience and analyze website traffic.</span>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">User Rights</strong>:
                            <ul style="color: white; padding-left: 20px; margin-top: 10px; list-style-position: outside; margin-bottom: 0;">
                                <li style="color: white; margin-bottom: 10px;">You may request access to, correction of, or deletion of your personal information.</li>
                                <li style="color: white; margin-bottom: 10px;">You may opt-out of data collection for marketing purposes.</li>
                            </ul>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Data Security</strong>: 
                            <span style="display: block; margin-top: 6px;">We implement encryption and secure servers to protect your personal information.</span>
                        </li>
                        <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                            <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Policy Updates</strong>: 
                            <span style="display: block; margin-top: 6px;">This Privacy Policy may be updated periodically to reflect changes in our practices.</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Admin Footer -->
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

<?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
    <!-- Student Header (same as student-homepage.php) -->
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

    <!-- Student Content with Direct Inline Styles to fix color issues -->
    <style>
        /* Direct styles for the student view to ensure proper colors and visibility */
        .container {
            max-width: 800px !important;
            margin: 120px auto 40px !important;
            background-color: rgba(30, 30, 30, 0.95) !important;
            border-radius: 8px !important;
            padding: 30px !important;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
            overflow: hidden !important;
        }
        
        .tabs {
            border-bottom: 2px solid rgba(255, 255, 255, 0.2) !important;
            display: flex !important;
            flex-wrap: wrap !important;
            margin-bottom: 25px !important;
        }
        
        .tabs a {
            color: white !important;
            white-space: nowrap !important;
            font-size: 0.9em !important;
            padding: 10px 20px !important;
        }
        
        .tabs a:hover, .tabs a.active {
            color: #FFD700 !important; /* Gold */
            border-bottom: 3px solid #FFD700 !important;
        }
        
        h2 {
            color: #FFD700 !important; /* Bright gold */
            font-size: 1.3em !important;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5) !important;
            word-wrap: break-word !important;
            max-width: 100% !important;
            margin-bottom: 20px !important;
        }
        
        .effective-dates {
            color: rgba(173, 216, 230, 0.8) !important; /* Light blue */
            text-align: center !important;
            width: 100% !important;
            font-size: 0.85em !important;
            margin-bottom: 25px !important;
        }
        
        .content {
            color: white !important; /* Pure white */
            word-wrap: break-word !important;
            max-width: 100% !important;
            font-size: 0.9em !important;
            line-height: 1.6 !important;
        }
        
        .content strong {
            color: #FFD700 !important; /* Bright gold */
            background-color: rgba(255, 215, 0, 0.1) !important;
            font-weight: bold !important;
            font-size: 0.95em !important;
            padding: 2px 4px !important;
        }
        
        .content ol, .content ul, .content li {
            color: white !important;
            max-width: 100% !important;
            overflow-wrap: break-word !important;
        }
        
        .content ol {
            padding-left: 25px !important;
            margin-top: 0 !important;
        }
        
        /* Fix for bullet points alignment */
        .content ul {
            padding-left: 20px !important;
            list-style-position: outside !important;
            margin-top: 8px !important;
        }
        
        .content li {
            margin-bottom: 12px !important;
            line-height: 1.5 !important;
        }
    </style>
    
    <div class="container">
        <div class="tabs">
            <a href="#" id="terms-tab" class="active" onclick="showContent('terms')">Terms of Service</a>
            <a href="#" id="privacy-tab" onclick="showContent('privacy')">Privacy Policy</a>
        </div>

        <div id="terms-content">
            <h2 style="color: #FFD700; text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5); max-width: 100%; word-wrap: break-word;">Terms of Service</h2>
            <p class="effective-dates" style="color: rgba(173, 216, 230, 0.8); width: 100%; text-align: center;">Effective Date: 2 April 2015. Last Updated: 14 April 2022</p>
            <div class="content" style="color: white; max-width: 100%; word-wrap: break-word;">
                <ol style="color: white; padding-left: 25px; max-width: 100%; overflow-wrap: break-word;">
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Acceptance of Terms</strong>: 
                        By accessing or using this website, you agree to be bound by these Terms of Service.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Eligibility</strong>: 
                        This platform is intended for users who are at least 18 years old. If you are under 18, you must obtain parental consent.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">User Responsibilities</strong>: 
                        You are responsible for safeguarding your account credentials and all activities under your account.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Prohibited Conduct</strong>:
                        <ul style="color: white; padding-left: 20px; margin-top: 10px; list-style-position: outside;">
                            <li style="color: white; margin-bottom: 8px;">Uploading harmful, illegal, or infringing content.</li>
                            <li style="color: white; margin-bottom: 8px;">Attempting to disrupt or breach website security.</li>
                            <li style="color: white; margin-bottom: 8px;">Engaging in fraudulent activities.</li>
                        </ul>
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Content Ownership</strong>: 
                        All content provided by this platform, including text, graphics, and software, is owned by us or our licensors.
                    </li>
                </ol>
            </div>
        </div>

        <div id="privacy-content" class="hidden">
            <h2 style="color: #FFD700; text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5); max-width: 100%; word-wrap: break-word;">Privacy Policy</h2>
            <p class="effective-dates" style="color: rgba(173, 216, 230, 0.8); width: 100%; text-align: center;">Effective Date: 2 April 2015. Last Updated: 14 April 2022</p>
            <div class="content" style="color: white; max-width: 100%; word-wrap: break-word;">
                <ol style="color: white; padding-left: 25px; max-width: 100%; overflow-wrap: break-word;">
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Information Collection</strong>: 
                        We collect personal information, including your name, email address, and payment details, for service provision.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Use of Information</strong>: 
                        Your data is used to personalize your experience, process transactions, and provide customer support.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Data Sharing</strong>: 
                        We do not sell or share your data with third parties, except as required by law or for essential services (e.g., payment processors).
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Cookies</strong>: 
                        Our website uses cookies to enhance your browsing experience and analyze website traffic.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">User Rights</strong>:
                        <ul style="color: white; padding-left: 20px; margin-top: 10px; list-style-position: outside;">
                            <li style="color: white; margin-bottom: 8px;">You may request access to, correction of, or deletion of your personal information.</li>
                            <li style="color: white; margin-bottom: 8px;">You may opt-out of data collection for marketing purposes.</li>
                        </ul>
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Data Security</strong>: 
                        We implement encryption and secure servers to protect your personal information.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Policy Updates</strong>: 
                        This Privacy Policy may be updated periodically to reflect changes in our practices.
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Student Footer -->
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
                <p>Copyright &copy; 2025 JavaQuest. All rights reserved</p>
            </div>
        </div>
    </footer>

<?php elseif (!isset($_SESSION['role'])): ?>
    <!-- Main Homepage Style Header (for non-logged in users) -->
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

    <!-- Main Homepage Style Content (for non-logged in users) -->
    <div class="terms-pixel-container">
        <div class="tabs">
            <a href="#" id="terms-tab" class="active" onclick="showContent('terms')">Terms of Service</a>
            <a href="#" id="privacy-tab" onclick="showContent('privacy')">Privacy Policy</a>
        </div>

        <div id="terms-content">
            <h2 style="color: #FFD700; text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5); max-width: 100%; word-wrap: break-word;">Terms of Service</h2>
            <p class="effective-dates" style="color: rgba(173, 216, 230, 0.8); width: 100%; text-align: center;">Effective Date: 2 April 2015. Last Updated: 14 April 2022</p>
            <div class="content" style="color: white; max-width: 100%; word-wrap: break-word;">
                <ol style="color: white; padding-left: 25px; max-width: 100%; overflow-wrap: break-word;">
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Acceptance of Terms</strong>: 
                        By accessing or using this website, you agree to be bound by these Terms of Service.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Eligibility</strong>: 
                        This platform is intended for users who are at least 18 years old. If you are under 18, you must obtain parental consent.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">User Responsibilities</strong>: 
                        You are responsible for safeguarding your account credentials and all activities under your account.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Prohibited Conduct</strong>:
                        <ul style="color: white; padding-left: 20px; margin-top: 10px; list-style-position: outside;">
                            <li style="color: white; margin-bottom: 8px;">Uploading harmful, illegal, or infringing content.</li>
                            <li style="color: white; margin-bottom: 8px;">Attempting to disrupt or breach website security.</li>
                            <li style="color: white; margin-bottom: 8px;">Engaging in fraudulent activities.</li>
                        </ul>
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Content Ownership</strong>: 
                        All content provided by this platform, including text, graphics, and software, is owned by us or our licensors.
                    </li>
                </ol>
            </div>
        </div>

        <div id="privacy-content" class="hidden">
            <h2 style="color: #FFD700; text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5); max-width: 100%; word-wrap: break-word;">Privacy Policy</h2>
            <p class="effective-dates" style="color: rgba(173, 216, 230, 0.8); width: 100%; text-align: center;">Effective Date: 2 April 2015. Last Updated: 14 April 2022</p>
            <div class="content" style="color: white; max-width: 100%; word-wrap: break-word;">
                <ol style="color: white; padding-left: 25px; max-width: 100%; overflow-wrap: break-word;">
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Information Collection</strong>: 
                        We collect personal information, including your name, email address, and payment details, for service provision.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Use of Information</strong>: 
                        Your data is used to personalize your experience, process transactions, and provide customer support.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Data Sharing</strong>: 
                        We do not sell or share your data with third parties, except as required by law or for essential services (e.g., payment processors).
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Cookies</strong>: 
                        Our website uses cookies to enhance your browsing experience and analyze website traffic.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">User Rights</strong>:
                        <ul style="color: white; padding-left: 20px; margin-top: 10px; list-style-position: outside;">
                            <li style="color: white; margin-bottom: 8px;">You may request access to, correction of, or deletion of your personal information.</li>
                            <li style="color: white; margin-bottom: 8px;">You may opt-out of data collection for marketing purposes.</li>
                        </ul>
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Data Security</strong>: 
                        We implement encryption and secure servers to protect your personal information.
                    </li>
                    <li style="color: white; margin-bottom: 15px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1);">Policy Updates</strong>: 
                        This Privacy Policy may be updated periodically to reflect changes in our practices.
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Main Homepage Style Footer (for non-logged in users) -->
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

<?php else: ?>
    <!-- Regular Header for logged-in non-admin users -->
    <header>
        <div class="logo-nav">
            <a href="#" class="logo" id="phraseThizLink">JavaQuest</a>

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
        </div>
        <div class="auth-container">
            <?php if (isset($_SESSION['username'])): ?>
                <?php 
                    $username = htmlspecialchars($_SESSION['username']);
                    $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

                    if ($role === 'student') {
                        echo '<span>Welcome, <a href="../student/student-profile.php" class="profile-link">' . $username . '</a>!</span>';
                    } elseif ($role === 'instructor') {
                        echo '<span>Welcome, <a href="../instructor/instructor-profile.php" class="profile-link">' . $username . '</a>!</span>';
                    } else {
                        echo '<span>Welcome, ' . $username . '!</span>';
                    }
                ?>
            <?php endif; ?>
        </div>
    </header>

    <!-- Regular Content for logged-in non-admin users -->
    <div class="container">
        <div class="tabs">
            <a href="#" id="terms-tab" class="active" onclick="showContent('terms')">Terms of Service</a>
            <a href="#" id="privacy-tab" onclick="showContent('privacy')">Privacy Policy</a>
        </div>

        <div id="terms-content">
            <h2 style="color: #FFD700; text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5); max-width: 100%; word-wrap: break-word; font-size: 1.3em; margin-bottom: 20px;">Terms of Service</h2>
            <p class="effective-dates" style="color: rgba(173, 216, 230, 0.8); width: 100%; text-align: center; font-size: 0.85em; margin-bottom: 20px;">Effective Date: 2 April 2015. Last Updated: 14 April 2022</p>
            <div class="content" style="color: white; max-width: 100%; word-wrap: break-word; font-size: 0.9em; line-height: 1.7;">
                <ol style="color: white; padding-left: 25px; max-width: 100%; overflow-wrap: break-word; margin-top: 0;">
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Acceptance of Terms</strong>: 
                        <span style="display: block; margin-top: 6px;">By accessing or using this website, you agree to be bound by these Terms of Service.</span>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Eligibility</strong>: 
                        <span style="display: block; margin-top: 6px;">This platform is intended for users who are at least 18 years old. If you are under 18, you must obtain parental consent.</span>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">User Responsibilities</strong>: 
                        <span style="display: block; margin-top: 6px;">You are responsible for safeguarding your account credentials and all activities under your account.</span>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Prohibited Conduct</strong>:
                        <ul style="color: white; padding-left: 20px; margin-top: 10px; list-style-position: outside; margin-bottom: 0;">
                            <li style="color: white; margin-bottom: 10px;">Uploading harmful, illegal, or infringing content.</li>
                            <li style="color: white; margin-bottom: 10px;">Attempting to disrupt or breach website security.</li>
                            <li style="color: white; margin-bottom: 10px;">Engaging in fraudulent activities.</li>
                        </ul>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Content Ownership</strong>: 
                        <span style="display: block; margin-top: 6px;">All content provided by this platform, including text, graphics, and software, is owned by us or our licensors.</span>
                    </li>
                </ol>
            </div>
        </div>

        <div id="privacy-content" class="hidden">
            <h2 style="color: #FFD700; text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5); max-width: 100%; word-wrap: break-word; font-size: 1.3em; margin-bottom: 20px;">Privacy Policy</h2>
            <p class="effective-dates" style="color: rgba(173, 216, 230, 0.8); width: 100%; text-align: center; font-size: 0.85em; margin-bottom: 20px;">Effective Date: 2 April 2015. Last Updated: 14 April 2022</p>
            <div class="content" style="color: white; max-width: 100%; word-wrap: break-word; font-size: 0.9em; line-height: 1.7;">
                <ol style="color: white; padding-left: 25px; max-width: 100%; overflow-wrap: break-word; margin-top: 0;">
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Information Collection</strong>: 
                        <span style="display: block; margin-top: 6px;">We collect personal information, including your name, email address, and payment details, for service provision.</span>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Use of Information</strong>: 
                        <span style="display: block; margin-top: 6px;">Your data is used to personalize your experience, process transactions, and provide customer support.</span>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Data Sharing</strong>: 
                        <span style="display: block; margin-top: 6px;">We do not sell or share your data with third parties, except as required by law or for essential services (e.g., payment processors).</span>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Cookies</strong>: 
                        <span style="display: block; margin-top: 6px;">Our website uses cookies to enhance your browsing experience and analyze website traffic.</span>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">User Rights</strong>:
                        <ul style="color: white; padding-left: 20px; margin-top: 10px; list-style-position: outside; margin-bottom: 0;">
                            <li style="color: white; margin-bottom: 10px;">You may request access to, correction of, or deletion of your personal information.</li>
                            <li style="color: white; margin-bottom: 10px;">You may opt-out of data collection for marketing purposes.</li>
                        </ul>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Data Security</strong>: 
                        <span style="display: block; margin-top: 6px;">We implement encryption and secure servers to protect your personal information.</span>
                    </li>
                    <li style="color: white; margin-bottom: 20px; padding-right: 10px;">
                        <strong style="color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-size: 0.95em; padding: 2px 4px;">Policy Updates</strong>: 
                        <span style="display: block; margin-top: 6px;">This Privacy Policy may be updated periodically to reflect changes in our practices.</span>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Regular Footer for logged-in non-admin users -->
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
            <div class="footer-copyright">
                <p>Copyright &copy; 2025 JavaQuest. All rights reserved</p>
            </div>
        </div>
    </footer>
<?php endif; ?>

<script>
    function showContent(contentType) {
        // Hide both content sections
        document.getElementById('terms-content').classList.add('hidden');
        document.getElementById('privacy-content').classList.add('hidden');

        // Remove 'active' class from both tabs
        document.getElementById('terms-tab').classList.remove('active');
        document.getElementById('privacy-tab').classList.remove('active');

        // Show the selected content section
        if (contentType === 'terms') {
            document.getElementById('terms-content').classList.remove('hidden');
            document.getElementById('terms-tab').classList.add('active');
        } else if (contentType === 'privacy') {
            document.getElementById('privacy-content').classList.remove('hidden');
            document.getElementById('privacy-tab').classList.add('active');
        }
    }

    // Initialize hamburger menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('menuBtn');
        if (menuBtn) {
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
        }
    });
</script>
</body>
</html>
