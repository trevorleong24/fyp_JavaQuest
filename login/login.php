<?php
include("../conn/conn.php");
session_start();

$successMessage = isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : "";
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : "";

unset($_SESSION['successMessage'], $_SESSION['errorMessage']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $emailOrUsername = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $studentQuery = "SELECT * FROM student WHERE (email='$emailOrUsername' OR username='$emailOrUsername')";
        $studentResult = mysqli_query($conn, $studentQuery);
        $student = mysqli_fetch_assoc($studentResult);

        if ($student && $password === $student['password']) {
            $_SESSION['username'] = $student['username'];
            $_SESSION['role'] = 'student';
            $_SESSION['student_id'] = $student['student_id'];
            header("Location: ../student/student-homepage.php");
            exit();
        }

        $adminQuery = "SELECT * FROM administrator WHERE (email='$emailOrUsername' OR username='$emailOrUsername')";
        $adminResult = mysqli_query($conn, $adminQuery);
        $admin = mysqli_fetch_assoc($adminResult);

        if ($admin && $password === $admin['password']) {
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = 'admin';
            $_SESSION['admin_id'] = $admin['admin_id'];
            header("Location: ../admin/admin.php");
            exit();
        }

        $_SESSION['errorMessage'] = "Invalid email/username or password.";
        header("Location: ../login/login.php");
        exit();
    } else {
        $role = $_POST['role'];

        if (!isset($_POST['terms'])) {
            $_SESSION['errorMessage'] = "You must accept the Terms of Service to sign up.";
            header("Location: ../login/login.php");
            exit();
        } else {
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $dob = mysqli_real_escape_string($conn, $_POST['dob']);
            $gender = mysqli_real_escape_string($conn, $_POST['gender']);

            if ($role === 'student') {
                $checkQuery = "SELECT * FROM student WHERE email = '$email' OR username = '$username'";
                $checkResult = mysqli_query($conn, $checkQuery);

                if (mysqli_num_rows($checkResult) > 0) {
                    $_SESSION['errorMessage'] = "An account with this email or username already exists.";
                    header("Location: ../login/login.php");
                    exit();
                }

                $result = mysqli_query($conn, "SELECT student_id FROM student ORDER BY student_id DESC LIMIT 1");
                $lastId = mysqli_fetch_assoc($result)['student_id'];

                if ($lastId) {
                    $num = (int)substr($lastId, 1);
                    $newNum = str_pad($num + 1, 2, '0', STR_PAD_LEFT);
                    $studentId = "S" . $newNum;
                } else {
                    $studentId = "S01";
                }

                $adminId = "A01"; // Example admin ID

                $sql = "INSERT INTO student (student_id, username, password, email, dob, gender, admin_id) 
                        VALUES ('$studentId', '$username', '$password', '$email', '$dob', '$gender', '$adminId')";

                if (mysqli_query($conn, $sql)) {
                    $_SESSION['successMessage'] = "Signup successful! Please log in.";
                    header("Location: ../login/login.php");
                    exit();
                } else {
                    $_SESSION['errorMessage'] = "Error: " . mysqli_error($conn);
                    header("Location: ../login/login.php");
                    exit();
                }
            } else {
                $_SESSION['errorMessage'] = "Invalid role selected.";
                header("Location: ../login/login.php");
                exit();
            }
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaQuest - Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<a href="../homepage/main-homepage.html" class="close-icon" aria-label="Close"><i class="fas fa-times"></i></a>

<!-- 左侧视频区域 -->
<div class="left-section">
    <video autoplay muted loop playsinline disablePictureInPicture>
        <source src="../video/signup.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

<!-- 右侧登录/注册区域 -->
<div class="right-section">
    <div class="tab-container">
        <a href="#" id="signup-tab">Sign Up</a>
        <a href="#" id="login-tab" class="active">Log In</a>
    </div>

    <!-- 登录表单 -->
    <div class="form-container active" id="login-container">
        <form action="login.php" method="POST">
            <label for="email">Email or Username</label>
            <input type="text" name="email" id="email" placeholder="Enter your email address or username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>

            <button type="submit" name="login">Start Your Adventure</button>
        </form>
        <div class="form-footer">
            <p>New to JavaQuest? <a href="#" id="switch-to-signup">Create an account</a></p>
            <?php if (!empty($errorMessage)): ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php elseif (!empty($successMessage)): ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- 学生注册表单 -->
    <form action="login.php" method="POST" class="student-signup" id="student-signup-container">
        <input type="hidden" name="role" value="student">

        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" required>

        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="">-- Select Gender --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label class="checkbox-container">
            <input type="checkbox" name="terms" required>
            I accept JavaQuest's <a href="../term-privacy/term-privacy.php">Terms of Service</a> and <a href="../term-privacy/term-privacy.php">Privacy Policy</a>
        </label>

        <button type="submit">Join The Quest</button>

        <div class="form-footer">
            <p>Already have an account? <a href="#" id="switch-to-login">Log in</a></p>
        </div>
    </form>
</div>

<script src="../js/login.js"></script>
</body>
</html>
