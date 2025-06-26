<?php
include("../conn/conn.php");
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit();
}

// Initialize variables
$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$data = [];
$error_message = '';
$success_message = '';

// Validate request parameters
if (empty($type) || empty($id)) {
    $_SESSION['message'] = "Invalid request parameters.";
    $_SESSION['message_type'] = 'error';
    header('Location: ../admin/admin.php');
    exit();
}

// Fetch the data based on type
if ($type === 'student') {
    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM STUDENT WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = "Student not found.";
        $_SESSION['message_type'] = 'error';
        header('Location: ../admin/admin.php');
        exit();
    }
    $stmt->close();
} elseif ($type === 'question') {
    // Handle question editing (placeholder for future implementation)
    $_SESSION['message'] = "Question editing not implemented yet.";
    $_SESSION['message_type'] = 'error';
    header('Location: ../admin/admin.php');
    exit();
} else {
    $_SESSION['message'] = "Invalid type specified.";
    $_SESSION['message_type'] = 'error';
    header('Location: ../admin/admin.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Passwords should not be trimmed
    $gender = isset($_POST['gender']) ? $_POST['gender'] : $data['gender']; // Keep original if not provided
    $dob = $_POST['dob'];
    
    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($dob)) {
        $error_message = "All fields are required.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Update the student record using prepared statement
        $update_query = "UPDATE STUDENT SET username = ?, email = ?, password = ?, gender = ?, dob = ? WHERE student_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssss", $username, $email, $password, $gender, $dob, $id);
        
        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Student updated successfully!";
            $_SESSION['message_type'] = 'success';
            header("Location: ../admin/admin.php");
            exit();
        } else {
            $error_message = "Error updating record: " . $update_stmt->error;
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaQuest - Edit Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin-new.css">
    <style>
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: rgba(30, 30, 30, 0.7);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        h1 {
            color: var(--accent-color);
            margin-bottom: 1.5rem;
            text-align: center;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--accent-color);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            font-size: 1rem;
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--light-color);
        }

        .error-message {
            background-color: rgba(255, 82, 82, 0.2);
            color: var(--danger-color);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 82, 82, 0.3);
        }

        .success-message {
            background-color: rgba(76, 175, 80, 0.2);
            color: var(--success-color);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .back-btn, .update-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .back-btn {
            background-color: var(--gray-color);
            color: var(--light-color);
        }

        .back-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .update-btn {
            background-color: var(--primary-color);
            color: var(--light-color);
        }

        .update-btn:hover {
            background-color: var(--accent-color);
            color: var(--dark-color);
            transform: translateY(-2px);
        }
    </style>
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

    <div class="container">
        <h1>Edit Student</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($data['password']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="Male" <?php echo ($data['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($data['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($data['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($data['dob']); ?>" required>
            </div>
            
            <div class="button-group">
                <button type="button" class="back-btn" onclick="window.location.href='../admin/admin.php'">Back to Dashboard</button>
                <button type="submit" class="update-btn">Update Student</button>
            </div>
        </form>
    </div>

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