<?php
include("../conn/conn.php");
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login/login.php"); // Redirect to login if not logged in
    exit();
}

// Ensure student_id is set
if (!isset($_SESSION['student_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get student ID from the session
    $student_id = $_SESSION['student_id'];

    // Get form inputs
    $username = htmlspecialchars(trim($_POST['option1']));
    $email = htmlspecialchars(trim($_POST['option2']));
    $dob = htmlspecialchars(trim($_POST['option3'])); // Ensure date format validation if needed

    // Validate inputs
    if (!empty($username) && !empty($email) && !empty($dob)) {
        // Update the database
        $query = "UPDATE STUDENT SET username = ?, email = ?, dob = ? WHERE student_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $username, $email, $dob, $student_id);

        if ($stmt->execute()) {
            // Success - redirect back to the profile page with success flag
            header("Location: student-profile.php?update=success");
            exit();
        } else {
            // Error
            echo "Error updating profile: " . $conn->error;
        }
    } else {
        // Handle empty fields
        echo "Please fill in all the fields.";
    }
}

// Fetch current student data to pre-fill form
$student_id = $_SESSION['student_id'];
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
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../css/student-edit.css">
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
            <a href="../student/student-profile.php" class="menu-item"><i class="fas fa-user"></i>Profile</a>
            <a href="../logout/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i>Log Out</a>
        </div>

        <!-- Main Content Section -->
        <main>
            <section class="form-container">
                <h1>Edit Profile</h1>
                <form id="UpdateForm" method="POST" action="">
                    <label for="option1">Username:</label>
                    <input type="text" id="option1" name="option1" value="<?php echo $username; ?>" placeholder="Enter your username" maxlength="50">

                    <label for="option2">Email Address:</label>
                    <input type="email" id="option2" name="option2" value="<?php echo $email; ?>" placeholder="Enter your email" maxlength="100">

                    <label for="option3">Date of Birth:</label>
                    <input type="date" id="option3" name="option3" value="<?php echo $dob; ?>" placeholder="Enter your date of birth">

                    <div class="form-buttons">
                        <button type="button" id="Back" class="back-btn" onclick="location.href='student-profile.php'"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="submit" id="Update" class="update-btn"><i class="fas fa-save"></i> Update</button>
                    </div>
                </form>
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
    
    // Add pixel art floating elements for extra visual appeal
    const mainContent = document.querySelector('main');
    
    // Create floating elements
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
</script>
</body>
</html>
