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
$quiz_id = $_GET['quiz_id'] ?? '';
if (!$quiz_id) {
    echo json_encode(['success' => false, 'message' => 'Quiz ID missing']);
    exit();
}

// Get the session key for this specific quiz
$session_key = 'current_game_progress_id_' . $quiz_id;

// Check if this is a reset/new game request
$reset = isset($_GET['reset']) && $_GET['reset'] == '1';

if ($reset) {
    // Clear any existing progress ID for this quiz
    unset($_SESSION[$session_key]);
    
    // Return reset game state
    echo json_encode([
        'success' => true,
        'message' => 'New game started',
        'score' => 0,
        'lst_played' => 0,
        'level' => 'Beginner',
        'is_new_game' => true
    ]);
    exit();
}

// First check if we have a progress_id in session for this quiz
if (isset($_SESSION[$session_key])) {
    $progress_id = $_SESSION[$session_key];
    
    // Verify this progress still exists and belongs to this quiz
    $verify = $conn->prepare("
        SELECT score, level_achieved, lst_played
        FROM gameprogress
        WHERE progress_id = ? AND quiz_id = ? AND student_id = ?
    ");
    $verify->bind_param("sss", $progress_id, $quiz_id, $student_id);
    $verify->execute();
    $verify->store_result();
    
    if ($verify->num_rows > 0) {
        $verify->bind_result($score, $level, $lst_played);
        $verify->fetch();
        
        echo json_encode([
            'success' => true,
            'message' => 'Game progress loaded from session',
            'score' => $score,
            'lst_played' => $lst_played,
            'level' => $level
        ]);
        exit();
    }
    // If verification fails, continue to load latest progress
}

// Query latest game progress if no valid session progress exists
$sql = "
    SELECT progress_id, score, level_achieved, lst_played
    FROM gameprogress
    WHERE student_id = ? AND quiz_id = ?
    ORDER BY date_taken DESC, progress_id DESC
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $student_id, $quiz_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($progress_id, $score, $level, $lst_played);
    $stmt->fetch();
    
    // Store the progress_id in session for future use
    $_SESSION[$session_key] = $progress_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'Game progress loaded',
        'score' => $score,
        'lst_played' => $lst_played,
        'level' => $level
    ]);
} else {
    echo json_encode([
        'success' => true,
        'message' => 'No existing progress found',
        'score' => 0,
        'lst_played' => 0,
        'level' => 'Beginner',
        'is_new_game' => true
    ]);
}
?>
