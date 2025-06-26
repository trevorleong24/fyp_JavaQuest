<?php
include("../conn/conn.php");
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login/login.php");
    exit();
}

// Ensure student_id is set
if (!isset($_SESSION['student_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Get student ID from the session
$student_id = $_SESSION['student_id'];

// Fetch student details from the database
$query = "SELECT username, email, dob, gender FROM STUDENT WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $username = htmlspecialchars($student['username']);
    $email = htmlspecialchars($student['email']);
    $dob = htmlspecialchars($student['dob']);
    $gender = strtolower(htmlspecialchars($student['gender']));

    // Determine avatar based on gender
    if ($gender === 'male') {
        $avatar = '../image/male.png';
    } elseif ($gender === 'female') {
        $avatar = '../image/female.png';
    } else {
        $avatar = ''; // No avatar shown
    }
} else {
    header("Location: ../login/login.php");
    exit();
}

$is_logged_in = isset($_SESSION['username']) && $_SESSION['role'] === 'student';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="../css/student-profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <a href="../student/student-profile.php" class="menu-item active"><i class="fas fa-user"></i>Profile</a>
            <a href="../logout/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i>Log Out</a>
        </div>

        <!-- Main Content Section -->
        <main class="main-content">
            <section class="profile-section">
                <div class="profile-container">
                    <aside class="profile-sidebar">
                        <div class="avatar-placeholder">
                            <?php if (!empty($avatar)) : ?>
                                <img src="<?php echo $avatar; ?>" alt="Student Avatar">
                            <?php endif; ?>
                        </div>
                        <div class="avatar-profile-text">
                            <h3><?php echo $username; ?></h3>
                            <p>Student</p>
                        </div>
                    </aside>
                    <section class="profile-content">
                        <h2>Your Profile</h2>
                        <div class="details-box">
                            <h3>Your Details</h3>
                            <p><strong>Username:</strong> <?php echo $username; ?></p>
                            <p><strong>Email:</strong> <?php echo $email; ?></p>
                            <p><strong>Date of Birth:</strong> <?php echo $dob; ?></p>
                            <p><strong>Gender:</strong> <?php echo ucfirst($gender); ?></p>
                            <div class="profile-buttons">
                                <button class="update-button" onclick="window.location.href='student-editprofile.php'"><i class="fas fa-edit"></i> Update</button>
                                <button class="logout-button" onclick="window.location.href='../logout/logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </main>

        <!-- Footer Section -->
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
    </div>

<?php if (isset($_GET['update']) && $_GET['update'] === 'success') : ?>
  <script>
      alert('Profile updated successfully.');
      const url = new URL(window.location.href);
      url.searchParams.delete('update');
      window.history.replaceState(null, null, url.toString());
  </script>
<?php endif; ?>

<script>
// Hamburger menu JavaScript
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
    
    // Add close button to the top of the menu
    const closeButton = document.createElement('div');
    closeButton.className = 'menu-close';
    closeButton.innerHTML = '<i class="fas fa-times"></i>';
    
    hamburgerMenu.prepend(closeButton);
    
    closeButton.addEventListener('click', function() {
        hamburgerMenu.classList.remove('show');
        menuBtn.classList.remove('active');
        const icon = menuBtn.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    });
    
    // Add some pixel art animations for the profile page
    const mainContent = document.querySelector('.main-content');
    
    // Create floating elements for extra visual appeal
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
        mainContent.appendChild(pixelElement);
    }
});

// Profile update function
function updateProfile() {
    window.location.href = 'update-profile.php';
}
</script>
</body>
</html>
