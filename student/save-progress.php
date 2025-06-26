<?php
include("../conn/conn.php");
session_start();

// 1) get logged-in student
$student_id = $_SESSION['student_id'] ?? '';
if (!$student_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// 2) pull quiz_id from the request
$quiz_id = $_POST['quiz_id'] ?? '';
if (!$quiz_id) {
    echo json_encode(['success' => false, 'message' => 'Quiz ID missing']);
    exit();
}

// 3) other inputs
$score      = intval($_POST['score'] ?? 0);
$level      = $_POST['level'] ?? 'Beginner';
$lst_played = $_POST['lst_played'] ?? '';
$date_taken = date("Y-m-d");
$reset      = isset($_POST['reset']) && $_POST['reset'] == '1';
$is_complete = isset($_POST['is_complete']) && $_POST['is_complete'] == '1';

// helper to generate new progress_id
function getNextProgressId($conn) {
    $prefix = "PG";
    // Get the highest progress_id from the database
    $r = $conn->query(
        "SELECT progress_id
         FROM gameprogress
         WHERE progress_id LIKE '$prefix%'
         ORDER BY CAST(SUBSTRING(progress_id, 3) AS UNSIGNED) DESC
         LIMIT 1"
    );
    if ($r && $row = $r->fetch_assoc()) {
        // Extract the number and increment
        $last = intval(substr($row['progress_id'], 2));
        $nextId = $last + 1;
    } else {
        $nextId = 1;
    }
    return $prefix . str_pad($nextId, 2, "0", STR_PAD_LEFT);
}

// Get the session key for this specific quiz
$session_key = 'current_game_progress_id_' . $quiz_id;

// If this is a completion update, just update the existing record
if ($is_complete && isset($_SESSION[$session_key])) {
    $progress_id = $_SESSION[$session_key];
    
    // Update the existing record with completion data
    $upd = $conn->prepare("
        UPDATE gameprogress
           SET score = ?, 
               level_achieved = ?, 
               lst_played = ?, 
               date_taken = ?
         WHERE progress_id = ?
           AND student_id = ?
           AND quiz_id = ?
    ");
    $upd->bind_param("issssss", $score, $level, $lst_played, $date_taken, $progress_id, $student_id, $quiz_id);
    $upd->execute();
    
    // Clear the session for this quiz
    unset($_SESSION[$session_key]);
    
    echo json_encode(["success" => true, "message" => "Game completion recorded."]);
    exit();
}

// Clear session key if this is a new game request
if ($reset) {
    unset($_SESSION[$session_key]);
}

// If this is a reset/new game request, create a new record directly
if ($reset) {
    // Always get a new progress_id for reset/new games
    $progress_id = getNextProgressId($conn);
    $ins = $conn->prepare("
        INSERT INTO gameprogress
          (progress_id, student_id, quiz_id, date_taken, score, level_achieved, lst_played)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $ins->bind_param("sssisss", $progress_id, $student_id, $quiz_id, $date_taken, $score, $level, $lst_played);
    $ins->execute();

    // Store this new progress_id in the session for future updates in this game
    $_SESSION[$session_key] = $progress_id;

    echo json_encode(["success" => true, "message" => "New game progress created.", "progress_id" => $progress_id]);
    exit();
}

// For existing games, check if we have a progress_id in the session for this quiz
if (isset($_SESSION[$session_key])) {
    $progress_id = $_SESSION[$session_key];
    
    // Verify if this progress_id still exists and belongs to this student
    $verify = $conn->prepare("
        SELECT 1 FROM gameprogress 
        WHERE progress_id = ? AND student_id = ? AND quiz_id = ?
    ");
    $verify->bind_param("sss", $progress_id, $student_id, $quiz_id);
    $verify->execute();
    $verify->store_result();
    
    // If verification fails, unset session and create new entry
    if ($verify->num_rows == 0) {
        unset($_SESSION[$session_key]);
        $progress_id = getNextProgressId($conn);
        $ins = $conn->prepare("
            INSERT INTO gameprogress
              (progress_id, student_id, quiz_id, date_taken, score, level_achieved, lst_played)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param("sssisss", $progress_id, $student_id, $quiz_id, $date_taken, $score, $level, $lst_played);
        $ins->execute();
        $_SESSION[$session_key] = $progress_id;
        echo json_encode(["success" => true, "message" => "New progress entry created."]);
        exit();
    }
    
    $upd = $conn->prepare("
        UPDATE gameprogress
           SET score = ?, level_achieved = ?, lst_played = ?, date_taken = ?
         WHERE progress_id = ?
           AND quiz_id = ?
           AND student_id = ?  -- Added student_id check for safety
    ");
    $upd->bind_param("issssss", $score, $level, $lst_played, $date_taken, $progress_id, $quiz_id, $student_id);
    $upd->execute();

    echo json_encode(["success" => true, "message" => "Progress updated."]);
    exit();
}

// For new games without a session ID, always create a new entry
$progress_id = getNextProgressId($conn);
$ins = $conn->prepare("
    INSERT INTO gameprogress
      (progress_id, student_id, quiz_id, date_taken, score, level_achieved, lst_played)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$ins->bind_param("sssisss", $progress_id, $student_id, $quiz_id, $date_taken, $score, $level, $lst_played);
$ins->execute();

// Store for future updates with quiz-specific key
$_SESSION[$session_key] = $progress_id;

echo json_encode(["success" => true, "message" => "New progress entry created."]);
?>
