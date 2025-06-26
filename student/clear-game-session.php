<?php
session_start();

// Get quiz_id from POST request
$quiz_id = $_POST['quiz_id'] ?? '';

if ($quiz_id) {
    // Clear only the specific quiz's progress ID from session
    unset($_SESSION['current_game_progress_id_' . $quiz_id]);
    echo json_encode(['success' => true, 'message' => "Game session cleared for quiz $quiz_id"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Quiz ID required']);
}
?> 